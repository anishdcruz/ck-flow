<?php

namespace App\Schema;

class Contact {

	public static function fields()
	{
		return [
			['type' => 'id', 'index' => false, 'sortable' => true],
			[
				'type' => 'auto', 'sortable' => true, 'name' => 'number',
				'validation' => [
					'create' => ['required', 'string'],
					'update' => []
				]
			]
		];
	}
}