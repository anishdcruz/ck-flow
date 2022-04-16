<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Payment\Payment;
use App\Payment\Line;
use App\Payment\Method;
use App\Payment\Deposit;
use App\Services\ExportCSV;
use DB;
use App\Invoice\Status;
use App\Contact\Contact;
use App\Invoice\Invoice;
use App\Services\TemplateParser;
use App\Template\Template;

class PaymentController extends Controller
{
    public function typeahead()
	{
	    $results = Payment::typeahead(['number']);

	    return to_json([
	        'results' => $results
	    ]);
	}

	public function search()
	{
	    return to_json([
	        'collection' => Payment::search()
	    ]);
	}

    public function index()
    {
        $this->authorize('access', 'payments.index');
        $collection = Payment::with([
                'contact:id,name', 'deposit:id,name'
            ])->when(request('contact_id'), function($query) {
                return $query->where('contact_id', request('contact_id'));
            })->filter();

        return to_json([
            'collection' => $collection
        ]);
    }

    public function export()
    {
        $this->authorize('access', 'payments.export');
        return (new ExportCSV(
            Payment::with([
                'contact', 'deposit', 'method'
            ]), 'payments')
        )->download();
    }

    public function create()
    {
        $this->authorize('access', 'payments.create');
        $form = [
            'number' => __('lang.auto_generated'),
            'contact_id' => null,
            'contact' => null,
            'payment_date' => date('Y-m-d'),
            'method' => null,
            'method_id' => null,
            'deposit' => null,
            'deposit_id' => null,
            'reference' => null,
            'amount_received' => 0,
            'bank_fees' => 0,
            'note' => null,
            'amount_applied' => null,
            'lines' => [],
            'custom_fields' => custom_fields('payments', "[]")
        ];

        if(request()->has('contact_id')) {
            $f = Contact::findOrFail(request()->contact_id);
            $form['contact_id'] = $f->id;

            $st = json_decode(settings()->get('receive_payment_on_status_ids'), true);

            $invoices = Invoice::select(['amount_paid', 'grand_total', 'due_date', 'issue_date', 'number', 'id'])
                ->where('contact_id', $f->id)
                ->whereIn('status_id', $st)
                ->get()
                ->map(function($i) {
                    $i->invoice_id = $i->id;
                    $i->amount_applied = 0;
                    $i->balance = $i->grand_total - $i->amount_paid;
                    return $i;
                });

            $form['contact'] = [
                'name' => $f->name,
                'id' => $f->id
            ];

            $form['lines'] = $invoices;
        }

        $id = settings()->get('default_payment_method_id');

        if($id) {
            $c = Method::findOrFail($id);
            $form['method'] = [
                'name' => $c->name,
                'id' => $c->id
            ];

            $form['method_id'] = $id;
        }

        $id = settings()->get('default_payment_deposit_id');

        if($id) {
            $c = Deposit::findOrFail($id);
            $form['deposit'] = [
                'name' => $c->name,
                'id' => $c->id
            ];

            $form['deposit_id'] = $id;
        }

        return to_json([
            'form' => $form
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('access', 'payments.create');
        $request->validate([
            'contact_id' => 'required|integer|exists:contacts,id',
            'payment_date' => 'required|date_format:Y-m-d',
            'method_id' => 'required|integer|exists:payment_methods,id',
            'reference' => 'required',
            'deposit_id' => 'required|integer|exists:payment_deposits,id',
            'amount_received' => 'required|numeric|min:0',
            'bank_fees' => 'required|numeric|min:0',
            'lines' => 'required|array|min:1',
            'lines.*.invoice_id' => 'required|integer|exists:invoices,id',
            'lines.*.amount_applied' => 'numeric|min:0',
            'custom_fields' => 'array'
        ]);

        $payment = new Payment;
        $payment->fill($request->except('lines', 'custom_values'));
        $payment->custom_values = json_encode(custom_values($request->custom_fields));

        $l = collect($request->lines)->map(function($item) {
            if($item['amount_applied'] > 0) {
                return $item;
            }
        })->reject(function($item) {
            return is_null($item);
        });

        $lines = $l->map(function($line) {
            return new Line([
                'invoice_id' => $line['invoice_id'],
                'amount_applied' => $line['amount_applied']
            ]);
        });


        $payment->amount_applied = $l->sum('amount_applied');
        $payment->net_amount = $payment->amount_applied - $request->bank_fees;

        $payment = DB::transaction(function() use ($payment, $lines) {
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

            return $payment;
        });

        return to_json([
            'saved' => true,
            'id' => $payment->id
        ]);
    }

    public function show($id)
    {
        $this->authorize('access', 'payments.show');
        $pp = Payment::with([
        	'contact:id,name', 'deposit:id,name', 'method:id,name',
        	'lines.invoice'
        	])
        	->findOrFail($id);

        $pp->custom_fields = custom_fields_preview('payments', $pp->custom_values);

        return to_json([
            'model' => $pp
        ]);
    }

    public function preview($id, TemplateParser $preview)
    {
        $this->authorize('access', 'payments.show');
        $p = Payment::with([
            'contact.organization', 'deposit:id,name', 'method:id,name',
            'lines.invoice'
            ])->findOrFail($id);

        $p->applied_invoices_table = render_applied_invoices_table($p);

        $id = settings()->get('default_payment_template_id');

        if($id) {
            $template = Template::with('pages')
                ->findOrFail($id);

            return $preview->documentPreview($template, $p)
                ->output();
        }

        abort(404, 'Payment Template Not Found!');
    }

    public function destroy($id)
    {
        $this->authorize('access', 'payments.delete');
        $model = Payment::findOrFail($id);

        // reduce invoice amount paid
        $model->lines->each(function($item) {
            $invoice = $item->invoice;
            $amount = $invoice->amount_paid - $item->amount_applied;

            $invoice->amount_paid = $amount;

            $id = settings()->get('invoice_status_on_partial_payment_id');

            if($id) {
                $c = Status::findOrFail($id);
                $invoice->status_id = $c->id;
            }

            $invoice->save();
        });

        $contact = $model->contact;
        $contact->total_revenue = $contact->total_revenue - $model->amount_received;
        $contact->save();

        $model->lines()->delete();
        $model->delete();

        return to_json([
            'deleted' => true
        ]);
    }
}
