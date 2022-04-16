<?php

namespace App\Services;

class Permission {

	public static function schema()
	{
		return [
			[
				'name' => 'contacts',
				'actions' => [
					'export' => 1,
					'index' => 1,
					'create' => 1,
					'show' => 1,
					'update' => 1,
					'delete' => 1
				]
			],
			[
				'name' => 'organizations',
				'actions' => [
					'export' => 1,
					'index' => 1,
					'create' => 1,
					'show' => 1,
					'update' => 1,
					'delete' => 1
				]
			],
			[
				'name' => 'media_library',
				'actions' => [
					'index' => 1,
					'upload' => 1,
					'update' => 1,
					'delete' => 1
				]
			],
			[
				'name' => 'items',
				'actions' => [
					'export' => 1,
					'index' => 1,
					'create' => 1,
					'show' => 1,
					'update' => 1,
					'delete' => 1
				]
			],
			[
				'name' => 'leads',
				'actions' => [
					'export' => 1,
					'index' => 1,
					'create' => 1,
					'show' => 1,
					'update' => 1,
					'delete' => 1,
					'change_status' => 1
				]
			],
			[
				'name' => 'opportunities',
				'actions' => [
					'export' => 1,
					'index' => 1,
					'create' => 1,
					'show' => 1,
					'update' => 1,
					'delete' => 1,
					'change_status' => 1,
					'change_stage' => 1
				]
			],
			[
				'name' => 'proposals',
				'actions' => [
					'export' => 1,
					'index' => 1,
					'create' => 1,
					'show' => 1,
					// 'clone' => 1,
					'update' => 1,
					'delete' => 1,
					'change_status' => 1,
					'send_email' => 1
				]
			],
			[
				'name' => 'contracts',
				'actions' => [
					'export' => 1,
					'index' => 1,
					'create' => 1,
					'show' => 1,
					// 'clone' => 1,
					'update' => 1,
					'delete' => 1,
					'change_status' => 1,
					'send_email' => 1
				]
			],
			[
				'name' => 'projects',
				'actions' => [
					'export' => 1,
					'index' => 1,
					'create' => 1,
					'show' => 1,
					// 'clone' => 1,
					'update' => 1,
					'delete' => 1,
					'change_stage' => 1,
					'add_tasks' => 1,
					'update_tasks' => 1,
					'delete_tasks' => 1
				]
			],
			[
				'name' => 'invoices',
				'actions' => [
					'export' => 1,
					'index' => 1,
					'create' => 1,
					'show' => 1,
					// 'clone' => 1,
					'update' => 1,
					'delete' => 1,
					'change_status' => 1,
					'send_email' => 1,
					'send_payment_request' => 1
				]
			],
			[
				'name' => 'payments',
				'actions' => [
					'export' => 1,
					'index' => 1,
					'create' => 1,
					'show' => 1,
					// 'update' => 1,
					'delete' => 1,
					'send_email' => 1,
				]
			],
			[
				'name' => 'payment_requests',
				'actions' => [
					'export' => 1,
					'index' => 1,
					// 'create' => 1,
					'show' => 1,
					// 'update' => 1,
					'delete' => 1
				]
			],
			[
				'name' => 'recurring_exports',
				'actions' => [
					// 'export' => 1,
					'index' => 1,
					// 'create' => 1,
					'show' => 1,
					// 'update' => 1,
					'delete' => 1
				]
			],
			[
				'name' => 'expenses',
				'actions' => [
					'export' => 1,
					'index' => 1,
					'create' => 1,
					'show' => 1,
					// 'update' => 1,
					'delete' => 1
				]
			],
			[
				'name' => 'vendors',
				'actions' => [
					'export' => 1,
					'index' => 1,
					'create' => 1,
					'show' => 1,
					'update' => 1,
					'delete' => 1
				]
			],
			[
				'name' => 'templates',
				'actions' => [
					'export' => 1,
					'import' => 1,
					'index' => 1,
					'create' => 1,
					'show' => 1,
					'update' => 1,
					'delete' => 1
				]
			],
			[
				'name' => 'settings',
				'actions' => [
					'general' => 1,
					'email' => 1,
					'globals' => 1,
					'contacts' => 1,
					'organizations' => 1,
					'items' => 1,
					'leads' => 1,
					'opportunities' => 1,
					'proposals' => 1,
					'contracts' => 1,
					'projects' => 1,
					'invoices' => 1,
					'payments' => 1,
					'expenses' => 1,
					'vendors' => 1,
					'web_payments' => 1,
					'users' => 1,
					'invitations' => 1,
					'roles_and_permission' => 1
				]
			],
		];
	}
}