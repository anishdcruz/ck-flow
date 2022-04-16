<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Payment\Deposit;
use App\Payment\Method;

class WebPaymentSettingsController extends Controller
{
        public function show()
        {
            $this->authorize('access', 'settings.web_payments');
        	$form = [
        		'payment_notification_email' => settings()->get('payment_notification_email'),
        		'active_payment_gateway' => settings()->get('active_payment_gateway'),
        		'payment_success_email' => settings()->get('payment_success_email'), // todo
                'web_payment_email_subject' => settings()->get('web_payment_email_subject'),

                'payment_request_email_template' => settings()->get('payment_request_email_template'),
                'payment_request_email_subject' => settings()->get('payment_request_email_subject'),

                'web_payment_notification_email_template' => settings()->get('web_payment_notification_email_template'),
                'web_payment_notification_email_subject' => settings()->get('web_payment_notification_email_subject'),

                'enable_stripe' => (int) settings()->get('enable_stripe') ?? 0,
        		'stripe_publishable_key' => settings()->get('stripe_publishable_key'),
        		'stripe_secret_key' => settings()->get('stripe_secret_key'),
        		'stripe_title' => settings()->get('stripe_title'),
        		'stripe_description' => settings()->get('stripe_description'),
        		'stripe_logo_url' => settings()->get('stripe_logo_url'),
        		'stripe_payment_method_id' => null,
        		'stripe_payment_method' => null,
        		'stripe_payment_deposit_id' => null,
        		'stripe_payment_deposit' => null,

                'enable_razorpay' => (int) settings()->get('enable_razorpay') ?? 0,
        		'razorpay_api_key' => settings()->get('razorpay_api_key'),
        		'razorpay_secret_key' => settings()->get('razorpay_secret_key'),
        		'razorpay_title' => settings()->get('razorpay_title'),
        		'razorpay_description' => settings()->get('razorpay_description'),
        		'razorpay_logo_url' => settings()->get('razorpay_logo_url'),
        		'razorpay_payment_method_id' => null,
        		'razorpay_payment_method' => null,
        		'razorpay_payment_deposit_id' => null,
        		'razorpay_payment_deposit' => null,

                'enable_paypal' => (int) settings()->get('enable_paypal') ?? 0,
                'paypal_payment_deposit_id' => null,
                'paypal_payment_deposit' => null,
                'paypal_payment_method_id' => null,
                'paypal_payment_method' => null,

                'paypal_mode' => settings()->get('paypal_mode'),
                'paypal_sandbox_client_id' => settings()->get('paypal_sandbox_client_id'),
                'paypal_sandbox_secret' => settings()->get('paypal_sandbox_secret'),
                'paypal_production_client_id' => settings()->get('paypal_production_client_id'),
                'paypal_production_secret' => settings()->get('paypal_production_secret'),
                'paypal_locale' => settings()->get('paypal_locale'),
                'paypal_size' => settings()->get('paypal_size'),
                'paypal_color' => settings()->get('paypal_color'),
                'paypal_shape' => settings()->get('paypal_shape')
        	];

        	$id = settings()->get('stripe_payment_method_id');

        	if($id) {
        		$c = Method::findOrFail($id);
        		$form['stripe_payment_method'] = [
        			'name' => $c->name,
        			'id' => $c->id
        		];

        		$form['stripe_payment_method_id'] = $id;
        	}

        	$id = settings()->get('stripe_payment_deposit_id');

        	if($id) {
        		$c = Deposit::findOrFail($id);
        		$form['stripe_payment_deposit'] = [
        			'name' => $c->name,
        			'id' => $c->id
        		];

        		$form['stripe_payment_deposit_id'] = $id;
        	}

            $id = settings()->get('paypal_payment_method_id');

            if($id) {
                $c = Method::findOrFail($id);
                $form['paypal_payment_method'] = [
                    'name' => $c->name,
                    'id' => $c->id
                ];

                $form['paypal_payment_method_id'] = $id;
            }

            $id = settings()->get('paypal_payment_deposit_id');

            if($id) {
                $c = Deposit::findOrFail($id);
                $form['paypal_payment_deposit'] = [
                    'name' => $c->name,
                    'id' => $c->id
                ];

                $form['paypal_payment_deposit_id'] = $id;
            }

        	$id = settings()->get('razorpay_payment_method_id');

        	if($id) {
        		$c = Method::findOrFail($id);
        		$form['razorpay_payment_method'] = [
        			'name' => $c->name,
        			'id' => $c->id
        		];

        		$form['razorpay_payment_method_id'] = $id;
        	}

        	$id = settings()->get('razorpay_payment_deposit_id');

        	if($id) {
        		$c = Deposit::findOrFail($id);
        		$form['razorpay_payment_deposit'] = [
        			'name' => $c->name,
        			'id' => $c->id
        		];

        		$form['razorpay_payment_deposit_id'] = $id;
        	}

        	return to_json([
        		'form' => $form
        	]);
        }

        public function update(Request $request)
        {
            $this->authorize('access', 'settings.web_payments');
            $request->validate([
            	'payment_notification_email' => 'nullable|email',
            	'active_payment_gateway' => 'required',
            	'payment_success_email' => 'required',
                'web_payment_email_subject' => 'required',
                'payment_request_email_subject' => 'required',
                'payment_request_email_template' => 'required',
                'web_payment_notification_email_template' => 'required',
                'web_payment_notification_email_subject' => 'required',

                'enable_stripe' => 'required|boolean',
            	'stripe_publishable_key' => 'required_with:enable_stripe',
            	'stripe_secret_key' => 'required_with:enable_stripe',
            	'stripe_title' => 'required_with:enable_stripe',
            	'stripe_description' => 'required_with:enable_stripe',
            	'stripe_logo_url' => 'required_with:enable_stripe',
            	'stripe_payment_method_id' => 'required_with:enable_stripe|integer',
            	'stripe_payment_deposit_id' => 'required_with:enable_stripe|integer',

                'enable_razorpay' => 'required|boolean',
            	'razorpay_api_key' => 'required_with:enable_razorpay',
            	'razorpay_secret_key' => 'required_with:enable_razorpay',
            	'razorpay_title' => 'required_with:enable_razorpay',
            	'razorpay_description' => 'required_with:enable_razorpay',
            	'razorpay_logo_url' => 'required_with:enable_razorpay',
            	'razorpay_payment_method_id' => 'required_with:enable_razorpay|integer',
            	'razorpay_payment_deposit_id' => 'required_with:enable_razorpay|integer',

                'enable_paypal' => 'required|boolean',
                'paypal_payment_deposit_id' => 'required_with:enable_paypal',
                'paypal_payment_method_id' => 'required_with:enable_paypal',
                'paypal_mode' => 'required_with:enable_paypal',
                'paypal_sandbox_client_id' => 'required_with:enable_paypal',
                'paypal_sandbox_secret' => 'required_with:enable_paypal',
                'paypal_production_client_id' => 'required_with:enable_paypal',
                'paypal_production_secret' => 'required_with:enable_paypal',
                'paypal_locale' => 'required_with:enable_paypal',
                'paypal_size' => 'required_with:enable_paypal',
                'paypal_color' => 'required_with:enable_paypal',
                'paypal_shape' => 'required_with:enable_paypal'
            ]);

            settings()->setMany(
            	$request->only([
    	    		'payment_notification_email',
    	    		'active_payment_gateway',
    	    		'payment_success_email',
                    'web_payment_email_subject',
                    'payment_request_email_subject',
                    'payment_request_email_template',
                    'web_payment_notification_email_subject',
                    'web_payment_notification_email_template',

                    'enable_stripe',
    	    		'stripe_publishable_key',
    	    		'stripe_secret_key',
    	    		'stripe_title',
    	    		'stripe_description',
    	    		'stripe_logo_url',
    	    		'stripe_payment_method_id',
    	    		'stripe_payment_deposit_id',

                    'enable_razorpay',
    	    		'razorpay_api_key',
    	    		'razorpay_secret_key',
    	    		'razorpay_title',
    	    		'razorpay_description',
    	    		'razorpay_logo_url',
    	    		'razorpay_payment_method_id',
    	    		'razorpay_payment_deposit_id',

                    'enable_paypal',
                    'paypal_payment_deposit_id',
                    'paypal_payment_method_id',
                    'paypal_mode',
                    'paypal_sandbox_client_id',
                    'paypal_sandbox_secret',
                    'paypal_production_client_id',
                    'paypal_production_secret',
                    'paypal_locale',
                    'paypal_size',
                    'paypal_color',
                    'paypal_shape'
            	])
            );

            return to_json([
                'saved' => true
            ]);
        }
}
