<?php

return [
	// set the inital values and prefixes before database migration
	'counters' => [
	    ['key' => 'contact', 'prefix' => 'CT-', 'value' => '10001'],
	    ['key' => 'organization', 'prefix' => 'OT-', 'value' => '10001'],
	    ['key' => 'item', 'prefix' => 'IT-', 'value' => '10001'],
	    ['key' => 'lead', 'prefix' => 'LD-', 'value' => '10001'],
	    ['key' => 'opportunity', 'prefix' => 'OP-', 'value' => '10001'],
	    ['key' => 'proposal', 'prefix' => 'PR-', 'value' => '10001'],
	    ['key' => 'contract', 'prefix' => 'CR-', 'value' => '10001'],
	    ['key' => 'project', 'prefix' => 'PT-', 'value' => '10001'],
	    ['key' => 'invoice', 'prefix' => 'IN-', 'value' => '10001'],
	    ['key' => 'payment', 'prefix' => 'PY-', 'value' => '10001'],
	    ['key' => 'vendor', 'prefix' => 'VD-', 'value' => '10001'],
	    ['key' => 'expense', 'prefix' => 'EX-', 'value' => '10001'],
	    ['key' => 'payment_request', 'prefix' => 'PRQ-', 'value' => '10001'],
	],
	'per_page' => 10,
	'application' => [
	    'name' => 'Flow'
	],
    'max_image_size' => '2048' // 2mb
];