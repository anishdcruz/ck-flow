<?php

namespace App\Services;
use App\Template\Template;

class TemplateFields {

	protected $template;
	protected $cvs;

	public function __construct($template, $cvs)
	{
		$this->template = $template;
		$this->cvs = $cvs;
	}

	public function getFields()
	{
		$pages = [];

		foreach($this->template->pages as $page) {
			$p = [
				'title' => $page->title,
				'name' => $page->name,
				'instruction' => $page->instruction,
				'user_fields' => []
			];

			foreach($page->user_fields as $section) {
				$s = [
					'title' => $section['title'],
					'name' => $section['name'],
					'fields' => []
				];

				foreach($section['fields'] as $field) {
					$k = $page['name'].'.'.__('lang.uf').'.'.$section['name'].'.'.$field['name'];

					if(isset($this->cvs[$k])) {

						switch ($field['type']) {
						    case 'list':
						        $field = array_merge($field, $this->cvs[$k]);
						        break;

						    case 'table':
						    	$field = array_merge($field, $this->cvs[$k]);
						        break;

						    default:
						    	$field['model'] = $this->cvs[$k];
						        break;
						}

					}
					$s['fields'][] = $field;
				}

				$p['user_fields'][] = $s;
			}

			$pages[] = $p;
		}

		return $pages;
	}

}