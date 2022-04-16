<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Payment\Request as PaymentRequest;
use App\Services\TemplateParser;
use App\Invoice\Invoice;
use App\Template\Template;
use App\Payment\Token;
use Stripe\Stripe;
use Stripe\Charge;
use App\Payment\Payment;
use App\Payment\Line;
use App\Invoice\Status;
use DB;
use Exception;
use Razorpay\Api\Api as Razorpay;
use Mail;
use App\Mail\SendDocument;
use App\Mail\SendSimpleMail;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Payment as PaypalPayment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Transaction;


class PaymentRequestController extends Controller
{
	public function previewInvoice($id, TemplateParser $preview)
	{
		$pay = PaymentRequest::where('expiry_at', '>', now())
            ->whereNull('payment_received_at')
			->where('uuid', $id)->firstOrFail();

		$p = Invoice::with([
		    'status:id,name,color', 'proposal',
            'contract', 'contact.organization'
		    ])->findOrFail($pay->invoice_id);

		$template = Template::with('pages')
		    ->findOrFail($p->template_id);

        return $preview->documentPreview($template, $p)
            ->output();
	}

    public function show($id)
    {
    	$pay = PaymentRequest::where('expiry_at', '>', now())
            ->whereNull('payment_received_at')
    		->where('uuid', $id)->firstOrFail();

        $g = settings()->get('active_payment_gateway');

        if(!$g) {
            abort(404, 'Payment gateway not found!');
        }

        if($g == 'stripe') {
            $config = [
                'key' => settings()->get('stripe_publishable_key'),
                'title' => settings()->get('stripe_title'),
                'description' => settings()->get('stripe_description'),
                'logo_url' => settings()->get('stripe_logo_url'),
                'currency' => settings()->get('currency_code'),
                'gateway' => 'payments.stripe'
            ];
        }

        else if($g == 'paypal') {
            $config = [
                'sandbox_client_id' => settings()->get('paypal_sandbox_client_id'),
                'production_client_id' => settings()->get('paypal_production_client_id'),
                'paypal_locale' => settings()->get('paypal_locale'),
                'paypal_size' => settings()->get('paypal_size'),
                'paypal_color' => settings()->get('paypal_color'),
                'paypal_shape' => settings()->get('paypal_shape'),
                'paypal_mode' => settings()->get('paypal_mode'),
                'currency' => settings()->get('currency_code'),
                'gateway' => 'payments.paypal'
            ];
        }

        else if($g == 'razorpay') {
            $config = [
                'key' => settings()->get('stripe_publishable_key'),
                'title' => settings()->get('stripe_title'),
                'description' => settings()->get('stripe_description'),
                'logo_url' => settings()->get('stripe_logo_url'),
                'currency' => settings()->get('currency_code'),
                'gateway' => 'payments.razorpay'
            ];
        }

    	return view('payment', ['pay' => $pay, 'config' => $config]);
    }

    public function store($id, Request $request)
    {
    	$pay = PaymentRequest::where('expiry_at', '>', now())
            ->whereNull('payment_received_at')
    		->where('uuid', $id)->firstOrFail();

        $g = settings()->get('active_payment_gateway');

        if(!$g) {
            abort(404, 'Payment gateway not found!');
        }


    	if($g == 'razorpay') {
            // razorpay
            $request->validate([
                'razorpay_payment_id' => 'required|alpha_dash|unique:payment_tokens,value',
            ]);

            // log token
            Token::create([
                'request_id' => $pay->id,
                'gateway' => 'razorpay',
                'value' => $request->razorpay_payment_id,
                'type' => 'none',
                'email' => null,
                'other' => '[]'
            ]);

            try {
                $api = new Razorpay(
                    settings()->get('razorpay_api_key'),
                    settings()->get('razorpay_secret_key')
                );

                $charge  = $api->payment
                    ->fetch($request->razorpay_payment_id)
                    ->capture([
                        'amount' => getStripeAmount($pay->invoice->balance)
                    ]);

            } catch (Exception $ex) {
                return view('payment_error');
            }

            $invoice = $pay->invoice;

            // payment
            $payment = new Payment;
            $payment->contact_id = $invoice->contact_id;
            $payment->payment_date = now()->format('Y-m-d');
            $payment->method_id = settings()->get('razorpay_payment_method_id'); // todo change
            $payment->reference = $charge->id;
            $payment->deposit_id = settings()->get('razorpay_payment_deposit_id'); // todo change
            $payment->amount_applied = fromStripeAmount($charge['amount']);
            $payment->amount_received = fromStripeAmount($charge['amount']);
            $payment->bank_fees = fromStripeAmount($charge['fee']);
            $payment->net_amount = fromStripeAmount($charge['amount']) - fromStripeAmount($charge['fee']);
            $payment->custom_values = '[]';
        }

        elseif($g == 'stripe') {
            // stripe
            $request->validate([
                'stripeToken' => 'required|alpha_dash|unique:payment_tokens,value',
                'stripeEmail' => 'required|email',
                'stripeTokenType' => 'required'
            ]);


            // log token
            Token::create([
                'request_id' => $pay->id,
                'gateway' => 'stripe',
                'value' => $request->stripeToken,
                'type' => $request->stripeTokenType,
                'email' => $request->stripeEmail,
                'other' => '[]'
            ]);

            // charge

            Stripe::setApiKey(settings()->get('stripe_secret_key'));

            try {
                $charge = Charge::create([
                    'amount' => getStripeAmount($pay->invoice->balance),
                    'currency' => settings()->get('currency_code'),
                    'description' => 'Payment for Invoice '.$pay->invoice->number,
                    'source' => $request->stripeToken,
                    'expand' => ['balance_transaction']
                ]);

            } catch (Exception $ex) {
                // dd($ex);
                return view('payment_error');
            }

            $invoice = $pay->invoice;

            // payment
            $payment = new Payment;
            $payment->contact_id = $invoice->contact_id;
            $payment->payment_date = now()->format('Y-m-d');
            $payment->method_id = settings()->get('stripe_payment_method_id');
            $payment->reference = $charge->id;
            $payment->deposit_id = settings()->get('stripe_payment_deposit_id');
            $payment->amount_applied = fromStripeAmount($charge->amount);
            $payment->amount_received = fromStripeAmount($charge->amount);
            $payment->bank_fees = fromStripeAmount($charge->balance_transaction->fee);
            $payment->net_amount = fromStripeAmount($charge->balance_transaction->net);
            $payment->custom_values = '[]';
        }

        elseif($g == 'paypal') {
            // paypal
            $request->validate([
                'paymentID' => 'required|alpha_dash|unique:payment_tokens,value',
                'payerID' => 'required'
            ]);


            // log token
            Token::create([
                'request_id' => $pay->id,
                'gateway' => 'paypal',
                'value' => $request->paymentID,
                'type' => 'none',
                'email' => $request->payerID,
                'other' => '[]'
            ]);

            // charge
            $clientId = settings()->get('paypal_mode') == 'sandbox'
                ? settings()->get('paypal_sandbox_client_id')
                : settings()->get('paypal_production_client_id');

            $secret = settings()->get('paypal_mode') == 'sandbox'
                ? settings()->get('paypal_sandbox_secret')
                : settings()->get('paypal_production_secret');

            $apiContext = new ApiContext(
                new OAuthTokenCredential(
                    $clientId,
                    $secret
                )
            );

            try {
                $charge = PaypalPayment::get($request->paymentID, $apiContext);
                $tr = $charge->transactions[0];
                $rr = $tr->related_resources[0];
                $sale = $rr->sale;

                $amount = $sale->amount->total;
                $fees = $sale->transaction_fee->value;
                $net = $amount - $fees;

            } catch (Exception $ex) {
                return view('payment_error');
            }

            $invoice = $pay->invoice;

            // payment
            $payment = new Payment;
            $payment->contact_id = $invoice->contact_id;
            $payment->payment_date = now()->format('Y-m-d');
            $payment->method_id = settings()->get('stripe_payment_method_id');
            $payment->reference = $charge->id;
            $payment->deposit_id = settings()->get('stripe_payment_deposit_id');
            $payment->amount_applied = $amount;
            $payment->amount_received = $amount;
            $payment->bank_fees = $fees;
            $payment->net_amount = $net;
            $payment->custom_values = '[]';
        }

    	$lines[] = new Line([
    		'invoice_id' => $invoice->id,
    		'amount_applied' => $payment->amount_applied
    	]);

    	$payment = DB::transaction(function() use ($payment, $lines, $pay) {
            $c = counter('payment');
            $payment->number = $c->number;
            $payment->save();
            $payment->lines()->saveMany($lines);
            $c->increment('value');

            $payment->lines->each(function($item) {
                $invoice = $item->invoice;
                $amount = $invoice->amount_paid + $item->amount_applied;

                $invoice->amount_paid = $amount;

                $id = settings()->get('invoice_status_on_partial_payment_id');

                if($id) {
                    $c = Status::findOrFail($id);
                    $invoice->status_id = $c->id;
                }


                if($invoice->amount_paid == $invoice->grand_total) {
                    $id = settings()->get('invoice_status_on_complete_payment_id');

                    if($id) {
                        $c = Status::findOrFail($id);
                        $invoice->status_id = $c->id;
                    }
                }

                $invoice->save();
            });

            //  2. update contact revenue
            $contact = $payment->contact;
            $contact->last_payment = now();
            $contact->total_revenue = $contact->total_revenue + $payment->amount_received;
            $contact->save();


            // 2. update request as completed
            $pay->payment_received_at = now();
            $pay->save();
            return $payment;
        });

        $payment->load(['contact.organization', 'deposit:id,name', 'method:id,name',
            'lines.invoice']);

        // sent email receipt
        $f = settings()->get('payment_notification_email');
        if($f) {
            // send notification
            $info = [
                'email_to' => $f,
                'bcc' => [],
                'subject' => '',
                'message' => ''
            ];

            $vars = [
                'contact.name',
                'amount',
                'number',
                'payment_page_url'
            ];

            $d = [
                'payment_date' => $payment->payment_date,
                'amount_received' => $payment->amount_received,
                'contact.name' => $payment->contact->name,
                'amount' => $payment->amount_received,
                'number' => $payment->number,
                'payment_page_url' => url('payments/'.$payment->id)
            ];

            $info['subject'] = parseSimpleTemplate(settings()->get('web_payment_notification_email_subject'), $vars, $d);
            $info['message'] = parseSimpleTemplate(settings()->get('web_payment_notification_email_template'), $vars, $d);

            Mail::send(new SendSimpleMail($info));
        }

        // email receipt to contact
        $info = [
            'email_to' => $payment->contact->email,
            'bcc' => [],
            'subject' => '',
            'message' => ''
        ];

        $vars = Payment::emailVariables();
        $info['subject'] = parseEmailTemplate(settings()->get('web_payment_email_subject'), $vars, $payment, 'payments');
        $info['message'] = parseEmailTemplate(settings()->get('payment_success_email'), $vars, $payment, 'payments');

        Mail::send(new SendDocument($payment, $info, 'payment'));

        // change status
        $id = settings()->get('payment_status_on_email_sent_id');

        if($id) {
            $payment->status_id = $id;
            $payment->save();
        }

        return view('payment_received');
    }
}
