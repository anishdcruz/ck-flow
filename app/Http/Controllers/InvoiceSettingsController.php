<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Template\Template;
use App\Invoice\Status;
use App\Invoice\Invoice;

class InvoiceSettingsController extends Controller
{
    public function show()
    {
        $this->authorize('access', 'settings.invoices');

        $options = [
            'email_variables' => Invoice::emailVariables()
        ];

    	$form = [
    		'invoice_status_on_create_id' => null,
    		'invoice_status_on_create' => null,
    		'invoice_status_on_email_sent_id' => null,
    		'invoice_status_on_email_sent' => null,
            'invoice_status_on_payment_request_id' => null,
            'invoice_status_on_payment_request' => null,
		    'invoice_status_on_partial_payment_id' => null,
		    'invoice_status_on_partial_payment' => null,
		    'invoice_status_on_complete_payment_id' => null,
		    'invoice_status_on_complete_payment' => null,
		    'default_invoice_template_id' => null,
		    'default_invoice_template' => null,
		    'receive_payment_on_status_ids' => [],
		    'due_date_after_days' => settings()->get('due_date_after_days'),
            'invoice_email_template' => settings()->get('invoice_email_template'),
            'invoice_email_subject' => settings()->get('invoice_email_subject')
    	];

    	$ids = json_decode(settings()->get('receive_payment_on_status_ids'), true);

    	if($ids) {
    		$statuses = Status::whereIn('id', $ids)->get();

    		$form['receive_payment_on_status_ids'] = $statuses;
    	}

    	$id = settings()->get('invoice_status_on_create_id');

    	if($id) {
    		$c = Status::findOrFail($id);
    		$form['invoice_status_on_create'] = [
    			'name' => $c->name,
    			'id' => $c->id
    		];

    		$form['invoice_status_on_create_id'] = $id;
    	}

    	$id = settings()->get('invoice_status_on_email_sent_id');

    	if($id) {
    		$c = Status::findOrFail($id);
    		$form['invoice_status_on_email_sent'] = [
    			'name' => $c->name,
    			'id' => $c->id
    		];

    		$form['invoice_status_on_email_sent_id'] = $id;
    	}

        $id = settings()->get('invoice_status_on_payment_request_id');

        if($id) {
            $c = Status::findOrFail($id);
            $form['invoice_status_on_payment_request'] = [
                'name' => $c->name,
                'id' => $c->id
            ];

            $form['invoice_status_on_payment_request_id'] = $id;
        }

    	$id = settings()->get('invoice_status_on_partial_payment_id');

    	if($id) {
    		$c = Status::findOrFail($id);
    		$form['invoice_status_on_partial_payment'] = [
    			'name' => $c->name,
    			'id' => $c->id
    		];

    		$form['invoice_status_on_partial_payment_id'] = $id;
    	}


    	$id = settings()->get('invoice_status_on_complete_payment_id');

    	if($id) {
    		$c = Status::findOrFail($id);
    		$form['invoice_status_on_complete_payment'] = [
    			'name' => $c->name,
    			'id' => $c->id
    		];

    		$form['invoice_status_on_complete_payment_id'] = $id;
    	}

    	$id = settings()->get('default_invoice_template_id');

    	if($id) {
    		$c = Template::findOrFail($id);
    		$form['default_invoice_template'] = [
    			'name' => $c->name,
    			'id' => $c->id
    		];

    		$form['default_invoice_template_id'] = $id;
    	}

    	return to_json([
    		'form' => $form,
            'options' => $options
    	]);
    }

    public function update(Request $request)
    {
        $this->authorize('access', 'settings.invoices');
        $request->validate([
    		'invoice_status_on_create_id' => 'required|integer',
    		'invoice_status_on_email_sent_id' => 'required|integer',
            'invoice_status_on_payment_request_id' => 'required|integer',
		    'invoice_status_on_partial_payment_id' => 'required|integer',
		    'invoice_status_on_complete_payment_id' => 'required|integer',
		    'default_invoice_template_id' => 'required|integer',
		    'due_date_after_days' => 'nullable|integer',
		    'receive_payment_on_status_ids' => 'required|array',
            'invoice_email_template' => 'required',
            'invoice_email_subject' => 'required'
        ]);

        $ids = [];

        foreach($request->receive_payment_on_status_ids as $item) {
        	$ids[] = $item['id'];
        }

        settings()->set('receive_payment_on_status_ids', json_encode($ids));

        settings()->setMany(
        	$request->only([
        		'invoice_status_on_create_id',
        		'invoice_status_on_partial_payment_id',
        		'invoice_status_on_complete_payment_id',
                'invoice_status_on_payment_request_id',
        		'default_invoice_template_id',
        		'due_date_after_days',
        		'invoice_status_on_email_sent_id',
                'invoice_email_template',
                'invoice_email_subject'
        	])
        );

        return to_json([
            'saved' => true
        ]);
    }
}
