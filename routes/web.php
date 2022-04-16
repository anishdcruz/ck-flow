<?php

Route::get('payment-requests/{id}/invoice', 'PaymentRequestController@previewInvoice');
Route::get('payment-requests/{id}', 'PaymentRequestController@show');
Route::post('payment-requests/{id}', 'PaymentRequestController@store');

Route::get('invite/{id}', 'UserInviteController@register');
Route::post('invite/{id}', 'UserInviteController@saveUser');

// Route::view('test', 'payment_error');

Route::get('images/{filename}', 'ImageController@show');
Route::get('dl/images/{filename}', 'ImageController@download');

Route::group(['prefix' => 'api', 'middleware' => 'auth'], function() {
	Route::group(['prefix' => 'search'], function() {
		Route::get('contacts', 'ContactController@search');
		Route::get('organization_categories', 'OrganizationCategoryController@search');
		Route::get('organizations', 'OrganizationController@search');
		Route::get('uoms', 'UomController@search');
		Route::get('item_categories', 'ItemCategoryController@search');
		Route::get('items', 'ItemController@search');
		Route::get('leads', 'LeadController@search');
		Route::get('lead_statuses', 'LeadStatusController@search');
		Route::get('opportunities', 'OpportunityController@search');
		Route::get('opportunity_categories', 'OpportunityCategoryController@search');
		Route::get('opportunity_sources', 'OpportunitySourceController@search');
		Route::get('opportunity_stages', 'OpportunityStageController@search');
		Route::get('proposals', 'ProposalController@search');
		Route::get('proposal_statuses', 'ProposalStatusController@search');
		Route::get('templates', 'TemplateController@search');
		Route::get('contract_types', 'ContractTypeController@search');
		Route::get('contract_statuses', 'ContractStatusController@search');
		Route::get('contracts', 'ContractController@search');
		Route::get('activity_types', 'ActivityTypeController@search');
		Route::get('project_categories', 'ProjectCategoryController@search');
		Route::get('project_stages', 'ProjectStagesController@search');
		Route::get('payment_methods', 'PaymentMethodController@search');
		Route::get('payment_deposits', 'PaymentDepositController@search');
		Route::get('roles', 'RoleController@search');
		Route::get('vendors', 'VendorController@search');
		Route::get('projects', 'ProjectController@search');
		Route::get('invoices', 'InvoiceController@search');
		Route::get('vendor_categories', 'VendorCategoryController@search');
		Route::get('expense_categories', 'ExpenseCategoryController@search');
		Route::get('invoice_statuses', 'InvoiceStatusController@search');
		Route::get('payment_requests', 'PaymentRequestCrudController@search');
	});

	Route::group(['prefix' => 'typeahead'], function() {
		Route::get('contacts', 'ContactController@typeahead');
		Route::get('vendors', 'VendorController@typeahead');

		// Route::get('organization_categories', 'OrganizationCategoryController@typeahead');
		Route::get('organizations', 'OrganizationController@typeahead');
		// Route::get('uoms', 'UomController@typeahead');
		// Route::get('item_categories', 'ItemCategoryController@typeahead');
		Route::get('items', 'ItemController@typeahead');
		// Route::get('leads', 'LeadController@typeahead');
		// Route::get('opportunities', 'OpportunityController@typeahead');
		// Route::get('opportunity_categories', 'OpportunityCategoryController@typeahead');
		// Route::get('opportunity_sources', 'OpportunitySourceController@typeahead');
		// Route::get('opportunity_stages', 'OpportunityStageController@typeahead');
		// Route::get('proposals', 'ProposalController@typeahead');
		// Route::get('proposal_statuses', 'ProposalStatusController@typeahead');
		// Route::get('templates', 'TemplateController@typeahead');
		// Route::get('contract_types', 'ContactTypeController@typeahead');
		// Route::get('contracts', 'ContactController@typeahead');
	});

	Route::group(['prefix' => 'mark-as'], function() {
		Route::post('leads/{lead}', 'LeadController@markAs');
		Route::post('opportunities/{opportunity}/status', 'OpportunityController@markStatusAs');
		Route::post('opportunities/{opportunity}', 'OpportunityController@markAs');
		Route::post('proposals/{proposal}', 'ProposalController@markAs');
		Route::post('contracts/{contract}', 'ContractController@markAs');
		Route::post('invoices/{invoice}', 'InvoiceController@markAs');
		Route::post('projects/{project}', 'ProjectController@markAs');
	});

	Route::group(['prefix' => 'settings'], function() {
		Route::resource('roles', 'RoleController');
		Route::resource('users', 'UserController');
		Route::resource('invitations', 'UserInviteController');
		Route::resource('organization_categories', 'OrganizationCategoryController');
		Route::resource('item_categories', 'ItemCategoryController');
		Route::resource('item_uoms', 'UomController');
		Route::resource('lead_statuses', 'LeadStatusController');
		Route::resource('opportunity_categories', 'OpportunityCategoryController');
		Route::resource('opportunity_sources', 'OpportunitySourceController');
		Route::resource('opportunity_stages', 'OpportunityStageController');
		Route::resource('proposal_statuses', 'ProposalStatusController');
		Route::resource('contract_statuses', 'ContractStatusController');
		Route::resource('contract_types', 'ContractTypeController');
		Route::resource('project_stages', 'ProjectStagesController');
		Route::resource('project_categories', 'ProjectCategoryController');
		Route::resource('invoice_statuses', 'InvoiceStatusController');
		Route::resource('payment_methods', 'PaymentMethodController');
		Route::resource('payment_deposits', 'PaymentDepositController');
		Route::resource('expense_categories', 'ExpenseCategoryController');
		Route::resource('vendor_categories', 'VendorCategoryController');

		Route::get('email_base', 'EmailController@showBase');
		Route::post('email_base', 'EmailController@storeBase');

		Route::get('custom_fields', 'CustomFieldController@show');
		Route::post('custom_fields', 'CustomFieldController@update');

		Route::get('general', 'GeneralSettingsController@show');
		Route::post('general', 'GeneralSettingsController@update');

		Route::get('organizations', 'OrganizationSettingsController@show');
		Route::post('organizations', 'OrganizationSettingsController@update');

		Route::get('items', 'ItemSettingsController@show');
		Route::post('items', 'ItemSettingsController@update');

		Route::get('leads', 'LeadSettingsController@show');
		Route::post('leads', 'LeadSettingsController@update');

		Route::get('opportunities', 'OpportunitySettingsController@show');
		Route::post('opportunities', 'OpportunitySettingsController@update');

		Route::get('proposals', 'ProposalSettingsController@show');
		Route::post('proposals', 'ProposalSettingsController@update');

		Route::get('contracts', 'ContractSettingsController@show');
		Route::post('contracts', 'ContractSettingsController@update');

		Route::get('projects', 'ProjectSettingsController@show');
		Route::post('projects', 'ProjectSettingsController@update');

		Route::get('invoices', 'InvoiceSettingsController@show');
		Route::post('invoices', 'InvoiceSettingsController@update');

		Route::get('payments', 'PaymentSettingsController@show');
		Route::post('payments', 'PaymentSettingsController@update');

		Route::get('expenses', 'ExpenseSettingsController@show');
		Route::post('expenses', 'ExpenseSettingsController@update');

		Route::get('vendors', 'VendorsSettingsController@show');
		Route::post('vendors', 'VendorsSettingsController@update');

		Route::get('web_payments', 'WebPaymentSettingsController@show');
		Route::post('web_payments', 'WebPaymentSettingsController@update');
	});

	Route::group(['prefix' => 'exports'], function() {
	    Route::get('contacts', 'ContactController@export');
	    Route::get('organizations', 'OrganizationController@export');
	    Route::get('items', 'ItemController@export');
	    Route::get('leads', 'LeadController@export');
	    Route::get('opportunities', 'OpportunityController@export');
	    Route::get('proposals', 'ProposalController@export');
	    Route::get('contracts', 'ContractController@export');
	    Route::get('projects', 'ProjectController@export');
	    Route::get('invoices', 'InvoiceController@export');
	    Route::get('payments', 'PaymentController@export');
	    Route::get('expenses', 'ExpenseController@export');
	    Route::get('vendors', 'VendorController@export');
	    Route::get('payment_requests', 'PaymentRequestCrudController@export');

	});

	Route::get('personal_settings', 'PersonalSettingsController@show');
	Route::post('personal_settings', 'PersonalSettingsController@update');

	Route::resource('recurring_exports', 'RecurringExportController');
	Route::resource('payment_requests', 'PaymentRequestCrudController');
	Route::get('emails', 'EmailController@compose');
	Route::post('emails', 'EmailController@sent');
	Route::resource('contacts', 'ContactController');
	Route::resource('organizations', 'OrganizationController');
	Route::resource('items', 'ItemController');
	Route::resource('leads', 'LeadController');
	Route::resource('opportunities', 'OpportunityController');
	Route::resource('images', 'ImageController');
	Route::post('templates/import', 'TemplateController@import');
	Route::get('templates/{template}/export', 'TemplateController@export');
	Route::get('templates/{template}/preview', 'TemplateController@preview');
	Route::resource('templates', 'TemplateController');
	Route::get('proposals/{proposal}/preview', 'ProposalController@preview');
	Route::resource('proposals', 'ProposalController');
	Route::get('contracts/{contract}/preview', 'ContractController@preview');
	Route::resource('contracts', 'ContractController');
	Route::resource('project_tasks', 'ProjectTaskController');
	Route::resource('projects', 'ProjectController');
	Route::get('invoices/{invoice}/preview', 'InvoiceController@preview');
	Route::post('invoices/{invoices}/payment-request', 'InvoiceController@createPaymentRequest');
	Route::resource('invoices', 'InvoiceController');
	// Route::resource('phone_calls', 'PhoneCallController');
	// Route::resource('activities', 'ActivityController');
	Route::get('payments/{payment}/preview', 'PaymentController@preview');
	Route::resource('payments', 'PaymentController');
	Route::get('expenses/{expense}/preview', 'ExpenseController@preview');
	Route::resource('expenses', 'ExpenseController');
	Route::resource('vendors', 'VendorController');
	Route::resource('user_metrics', 'UserMetricController');
	Route::get('overview', 'OverviewController@index');
});

Route::post('login', 'PageController@login')
    ->middleware('guest')
    ->name('login');

Route::get('logout', 'PageController@logout')
    ->middleware('auth');

Route::get('{vue?}', 'PageController@index')->where('vue', '[\/\w\.-]*')
        ->name('app');