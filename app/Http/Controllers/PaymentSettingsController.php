<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Payment\Method;
use App\Payment\Deposit;
use App\Template\Template;
use App\Payment\Payment;

class PaymentSettingsController extends Controller
{
    public function show()
    {
        $this->authorize('access', 'settings.payments');
        $options = [
            'email_variables' => Payment::emailVariables()
        ];
    	$form = [
    		'default_payment_method_id' => null,
    		'default_payment_method' => null,
    		'default_payment_deposit_id' => null,
    		'default_payment_deposit' => null,
            'default_payment_template_id' => null,
            'default_payment_template' => null,
            'payment_email_template' => settings()->get('payment_email_template'),
            'payment_email_subject' => settings()->get('payment_email_subject')
    	];

    	$id = settings()->get('default_payment_template_id');

    	if($id) {
    		$c = Template::findOrFail($id);
    		$form['default_payment_template'] = [
    			'name' => $c->name,
    			'id' => $c->id
    		];

    		$form['default_payment_template_id'] = $id;
    	}

        $id = settings()->get('default_payment_method_id');

        if($id) {
            $c = Method::findOrFail($id);
            $form['default_payment_method'] = [
                'name' => $c->name,
                'id' => $c->id
            ];

            $form['default_payment_method_id'] = $id;
        }

    	$id = settings()->get('default_payment_deposit_id');

    	if($id) {
    		$c = Deposit::findOrFail($id);
    		$form['default_payment_deposit'] = [
    			'name' => $c->name,
    			'id' => $c->id
    		];

    		$form['default_payment_deposit_id'] = $id;
    	}

    	return to_json([
    		'form' => $form,
            'options' => $options
    	]);
    }

    public function update(Request $request)
    {
        $this->authorize('access', 'settings.payments');
        $request->validate([
    		'default_payment_method_id' => 'required|integer',
		    'default_payment_deposit_id' => 'required|integer',
            'default_payment_template_id' => 'required|integer',
            'payment_email_template' => 'required',
            'payment_email_subject' => 'required'
        ]);

        settings()->setMany(
        	$request->only([
        		'default_payment_method_id',
        		'default_payment_deposit_id',
                'default_payment_template_id',
                'payment_email_template',
                'payment_email_subject'
        	])
        );

        return to_json([
            'saved' => true
        ]);
    }
}
