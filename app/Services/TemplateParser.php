<?php

namespace App\Services;
use Mpdf\Mpdf;

class TemplateParser {

	protected $contentFields;
	protected $template;
	protected $users;

	public function templatePreview($template)
	{
		$this->users = false;
		$this->template = $template;
		$this->contentFields = array_merge(
			$this->extractContentFields(),
			global_variables()
		);

		$mpdf = $this->process();

		return $mpdf->Output();
	}

	public function documentPreview($template, $model)
	{
		// extract content fields
		// 1. header, 2. footer, 3. pages (content, user)
		// 2. content = document to array and custom fields to array
		// 3. get fillable contents
		$this->users = true;
		$this->template = $template;
		$cfs = $this->extractContentFields();

		$this->contentFields = array_merge(
			$cfs,
			$this->getRelations($model),
			array_dot($model->toArray()),
			// array_only(array_dot($model->toArray()), []),
			$this->getUserVars(
				json_decode($model->custom_values, true)
			),
			global_variables()
		);

		return $this;
	}

	public function getRelations($model)
	{
		$items = [];

		foreach($model->getRelations() as $key => $rel) {
			if(method_exists($rel, 'customFields')) {
				$items = array_merge($rel->customFields($key), $items);
				foreach($rel->getRelations() as $skey => $srel) {
					if(method_exists($srel, 'customFields')) {
						$items = array_merge($srel->customFields($key.'.'.$skey), $items);
					}
				}
			}
		}
		// dd($items);
		return $items;
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

		foreach($this->contentFields as $k => $v) {
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

			$filter = array_map('trim', explode('|', $match[1]));

			if(count($filter) > 1  && isset($vals[$filter[0]])) {
				if($filter[1] == 'formatMoney') {
					return formatMoney($vals[$filter[0]]);
				}
				if($filter[1] == 'formatDate') {
					return formatDate($vals[$filter[0]], settings()->get('application_date_format'));
				}
			} elseif(isset($vals[$match[1]])) {
				return $vals[$match[1]];
			}

			return $match[0];
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
				$this->getVars($page->content_fields, $page->name.'.cf')
			);

			if($this->users) {
				// pages user
				$fields = array_merge($fields,
					$this->getVars($page->user_fields, $page->name.'.uf')
				);
			}
		}

		return $fields;
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