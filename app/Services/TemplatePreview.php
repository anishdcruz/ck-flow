<?php

namespace App\Services;

use Mpdf\Mpdf;
use App\Contact\Contact;
use App\Organization\Organization;
use App\Opportunity\Opportunity;
use App\Proposal\Proposal;
use App\Invoice\Invoice;
use App\Payment\Payment;
use App\Payment\Deposit;
use App\Payment\Method;
use App\Expense\Expense;
use App\Contract\Contract;
use App\Vendor\Vendor;

class TemplatePreview {

	protected $template;

	protected $content;

	protected $users;

	public function __construct()
	{
		$this->users = false;
	}

	public function load($t)
	{
		$this->template = $t;
		$this->extractContentFields();
		return $this;
	}

	public function document($document)
	{
		$this->users = true;
		// $this->content = array_merge(
		// 	$this->content,
		// 	$this->getUserVars(
		// 		json_decode($document->custom_values, true)
		// 	)
		// );

		switch ($this->template->type_id) {
			case '1':
				$available = array_merge(
					Proposal::templateVariables(null, false),
					Contact::templateVariables('contact', false),
					Organization::templateVariables('contact.organization', false),
					Opportunity::templateVariables('opportunity', false)
				);
				break;

			case '2':
				$available = array_merge(
					Contract::templateVariables(null, false),
					Contact::templateVariables('contact', false),
					Organization::templateVariables('contact.organization', false),
					Proposal::templateVariables('proposal', false)
				);
				break;

			case '3':
				$available = array_merge(
					Invoice::templateVariables(null, false),
					Contact::templateVariables('contact', false),
					Organization::templateVariables('contact.organization', false),
					Proposal::templateVariables('proposal', false),
					Contract::templateVariables('contract', false)
				);
				break;

			case '4':
				$available = array_merge(
					Payment::templateVariables(null, false),
					Contact::templateVariables('contact', false),
					Organization::templateVariables('contact.organization', false)
				);

				// todo render table
				break;

			case '5':
				$available = array_merge(
					Expense::templateVariables(null, false),
					Vendor::templateVariables('vendor', false)
				);
				break;
			default:
				break;
		}


		$this->content = array_merge(
			$this->content,
			array_only(array_dot($document->toArray()), $available),
			$this->getUserVars(
				json_decode($document->custom_values, true)
			)
		);
		// dd($document->toArray());
		return $this;
	}

	public function output()
	{
		$mpdf = $this->process();

		return $mpdf->Output();
	}

	public function toString()
	{
		$mpdf = $this->process();

		return  $mpdf->Output('', 'S');
	}

	protected function process()
	{
		$mpdf = new Mpdf($this->getGlobalConfig());
		// $mpdf->showImageErrors = true;
		$mpdf->WriteHTML(view('flow/css/base'), 1);

		$mpdf->WriteHTML($this->template->stylesheet, 1);

		$mpdf->defHTMLHeaderByName(
		    'flowHeader',
			$this->getHeaderHTML()
		);

		$mpdf->defHTMLFooterByName(
		    'flowFooter',
			$this->getFooterHTML()
		);

		foreach($this->template->pages as $page) {
			$mpdf->AddPageByArray($this->getPageConfig($page));
			$mpdf->WriteHTML($this->getPageHTML($page), 2);
		}

		return $mpdf;
	}

	protected function getPageHTML($page)
	{
		// process vars
		return $this->replaceVariables($page->page_html);
	}

	protected function getHeaderHTML()
	{
		// process vars
		return $this->replaceVariables($this->template->header_html);
	}

	protected function getFooterHTML()
	{
		// process vars
		return $this->replaceVariables($this->template->footer_html);
	}

	protected function getPageConfig($page)
	{
		$h = $this->template->header_height;
		$f = $this->template->footer_height;

		// if not enabled per page
		if(!$page->header_and_footer) {
			$h = 0;
			$f = 0;
		}

		return [
			'ohname' => 'flowHeader',
			'ohvalue' => $page->header_and_footer ? 1 : -1,
			'ofname' => 'flowFooter',
			'ofvalue' => $page->header_and_footer ? 1 : -1,
			'orientation' => $page->orientation,
    	    'margin-top' => $h,
			'margin-bottom' => $f,
		];
	}

	protected function getGlobalConfig()
	{
		return [
			'format' => $this->template->page_size,
			'orientation' => $this->template->orientation,
    	    'margin_left' => 0,
    	    'margin_right' => 0,
    	    'margin_top' => $this->template->header_height,
			'margin_bottom' => $this->template->footer_height,
    	    'margin_header' => 0,
    	    'margin_footer' => 0,
		];
	}

	protected function replaceVariables($html)
	{
		$vals = [];

		$pattern = '/{+(.*?)}/';

		foreach($this->content as $k => $v) {
			if(is_string($v)) {
				$vals[$k] = preg_replace_callback($pattern, function($match) use ($vals)
				{
				    return isset($vals[$match[1]]) ? $vals[$match[1]] : $match[0];
				}, $v);
			} else {
				$vals[$k] = $v;
			}
		}

		$replace = preg_replace_callback($pattern, function($match) use ($vals)
		{
		    return isset($vals[$match[1]]) ? $vals[$match[1]] : $match[0];
		}, $html);


		return $replace;
	}

	protected function extractContentFields()
	{
		$fields = [];

		// header
		$fields = array_merge($fields,
			$this->getVars($this->template->header_content_fields, 'header')
		);

		// footer
		$fields = array_merge($fields,
			$this->getVars($this->template->footer_content_fields, 'footer')
		);

		foreach($this->template->pages as $page) {
			// pages content
			$fields = array_merge($fields,
				$this->getVars($page->content_fields, $page->name.'.'.__('lang.cf'))
			);

			if($this->users) {
				// pages user
				$fields = array_merge($fields,
					$this->getVars($page->user_fields, $page->name.'.'.__('lang.uf'))
				);
			}
		}
		$this->content = $fields;
	}

	protected function getVars($sections, $page)
	{
		$f = [];
		foreach($sections as $section) {
			foreach($section['fields'] as $field) {
				$k = $page.'.'.$section['name'].'.'.$field['name'];

				switch ($field['type']) {
					case 'text':
					case 'number':
					case 'textarea':
					case 'select':
						$f[$k] = $field['model'];
						break;
					case 'image':
						$f[$k] = $field['model'];
						break;

					case 'date':
						$f[$k] = formatDate($field['model'], $field['format']);
						break;
					case 'currency':
						$f[$k] = formatCurrency($field['model'], $field['currency']);
						break;

					case 'list':
						$f[$k] = $this->renderList([
							'list_model' => $field['list_model'],
							'class_name' => isset($field['class_name']) ? $field['class_name'] : ''
						]);
						break;

					case 'table':
						$f[$k] = $this->renderTable([
							'thead' => $field['thead'],
							'tbody' => $field['tbody'],
							'tfoot' => $field['tfoot'],
							'currency' => $field['currency'],
							'colspan' => $field['colspan'],
							'class_name' => isset($field['class_name']) ? $field['class_name'] : ''
						]);
						break;
					default:
						break;
				}
			}
		}

		return $f;
	}

	protected function getUserVars($cvs)
	{
		$f = [];
		foreach($this->template->pages as $page) {
			foreach($page->user_fields as $section) {

				foreach($section['fields'] as $field) {
					$k = $page['name'].'.'.__('lang.uf').'.'.$section['name'].'.'.$field['name'];

					if(isset($cvs[$k])) {

						switch ($field['type']) {
							case 'date':
								$f[$k] = formatDate($field['model'], $field['format']);
								break;
							case 'currency':
								$f[$k] = formatCurrency($field['model'], $field['currency']);
								break;

							case 'list':
								$field = array_merge($field, $cvs[$k]);
								$f[$k] = $this->renderList([
									'list_model' => $field['list_model'],
									'class_name' => isset($field['class_name']) ? $field['class_name'] : ''
								]);
								break;

							case 'table':
								$field = array_merge($field, $cvs[$k]);
								$f[$k] = $this->renderTable([
									'thead' => $field['thead'],
									'tbody' => $field['tbody'],
									'tfoot' => $field['tfoot'],
									'currency' => $field['currency'],
									'colspan' => $field['colspan'],
									'class_name' => isset($field['class_name']) ? $field['class_name'] : ''
								]);
								break;

						    default:
						    	$f[$k] = $cvs[$k];
						        break;
						}
					}
				}
			}
		}

		return $f;
	}

	protected function renderList($model)
	{
		$h = $model['class_name'] ? '<ul class="'.$model['class_name'].'">' : '<ul>';
		foreach($model['list_model'] as $item) {
			$h .= '<li>'.$item.'</li>';
		}
		$h .= '</ul>';
		return $h;
	}

	protected function renderTable($model)
	{
		$h = $model['class_name'] ? '<table class="'.$model['class_name'].'">' : '<table>';
		// 1. thead
		$h .= '<thead><tr>';
			foreach($model['thead'] as $col) {
				if($col['type'] != 'hidden') {
					$h .= '<th class="w-'.$col['width'].'">'.$col['title'].'</th>';
				}
			}
		$h .= '</tr></thead>';
		// 2. tbody
		$h .= '<tbody>';
			foreach($model['tbody'] as $row) {
				$h .= '<tr>';
					foreach($model['thead'] as $col) {
						switch ($col['type']) {
							case 'currency':
							case 'computed_currency':
								$h .= '<td class="align-'.$col['align'].'">'.formatCurrency($row[$col['name']], $model['currency']).'</td>';
								break;

							case 'date':
								$h .= '<td class="align-'.$col['align'].'">'.formatDate($row[$col['name']], $col['format']).'</td>';
								break;

							case 'image':
								$h .= '<td class="align-'.$col['align'].'"class="w-'.$col['width'].'"><img class="w-'.$col['width'].'" src="'.$row[$col['name']].'"></td>';
								break;

							default:
								$h .= '<td class="align-'.$col['align'].'">'.$row[$col['name']].'</td>';
								break;
						}
					}
				$h .= '</tr>';
			}
		$h .= '</tbody>';
		// 3. tfoot
		$h .= '<tfoot>';
		foreach($model['tfoot'] as $col) {
			if($col['type'] != 'hidden') {
				$h .= '<tr>';

				$span = $model['colspan'];

				switch ($col['type']) {

					case 'aggregate_currency':
					case 'computed_currency':
					case 'input_currency':
						$h .= '<td colspan="'.$span['empty'].'">&nbsp;</td>';
						$h .= '<td class="align-right" colspan="'.$span['title'].'">'.$col['title'].'</td>';
						$h .= '<td class="align-'.$col['align'].'" colspan="'.$span['value'].'">'.formatCurrency($col['model'], $model['currency']).'</td>';
						break;
					case 'tax':
						$h .= '<td colspan="'.$span['empty'].'">&nbsp;</td>';
						$h .= '<td class="align-right" colspan="'.$span['title'].'">'.$col['title'].' @ '.$col['percent_model'].'%</td>';
						$h .= '<td class="align-'.$col['align'].'" colspan="'.$span['value'].'">'.formatCurrency($col['model'], $model['currency']).'</td>';
						break;
					default:
						$h .= '<td colspan="'.$span['empty'].'">&nbsp;</td>';
						$h .= '<td class="align-right" colspan="'.$span['title'].'">'.$col['title'].'</td>';
						$h .= '<td class="align-'.$col['align'].'" colspan="'.$span['value'].'">'.$col['model'].'</td>';
						break;
				}
				$h .= '</tr>';
			}
		}
		$h .= '</tfoot>';
		$h .= '</table>';
		return $h;
	}
}