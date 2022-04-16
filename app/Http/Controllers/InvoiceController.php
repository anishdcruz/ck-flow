<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Invoice\Invoice;
use App\Invoice\Status;
use App\Invoice\Line;
use App\Proposal\Proposal;
use App\Contract\Contract;
use App\Contact\Contact;
use App\Template\Template;
use App\Services\TemplateFields;
use App\Services\TemplateParser;
use App\Services\ExportCSV;
use DB;
use App\Payment\Request as PaymentRequest;
use Mail;
use App\Mail\SendNotification;

class InvoiceController extends Controller
{
    public function typeahead()
	{
	    $results = Invoice::typeahead(['number']);

	    return to_json([
	        'results' => $results
	    ]);
	}

	public function search()
	{
	    return to_json([
	        'collection' => Invoice::search()
	    ]);
	}

    public function index()
    {
        $this->authorize('access', 'invoices.index');
        $collection = Invoice::with([
                'contact:id,name', 'status:id,name,color',
                // 'proposal:id,number', 'contract:id,number',
            ])->when(request('contact_id'), function($query) {
                return $query->where('contact_id', request('contact_id'));
            })->when(request('proposal_id'), function($query) {
                return $query->where('proposal_id', request('proposal_id'));
            })->filter();

        return to_json([
            'collection' => $collection
        ]);
    }

    public function export()
    {
        $this->authorize('access', 'invoices.export');
        return (new ExportCSV(
            Invoice::with([
                'contact', 'status', 'template', 'proposal', 'contract'
            ]), 'invoices')
        )->download();
    }

    public function create()
    {
        $this->authorize('access', 'invoices.create');
        $form = [
            'number' => __('lang.auto_generated'),
            'contact_id' => null,
            'contact' => null,
            'template_id' => null,
            'template' => null,
            'proposal_id' => null,
            'proposal' => null,
            'contract_id' => null,
            'contract' => null,
            'issue_date' => date('Y-m-d'),
            'due_date' => null,
            'reference' => null,
            'custom_fields' => null,
            'custom_fields_2' => custom_fields('invoices', "[]")
        ];

        if(request()->has('contact_id')) {
            $f = Contact::findOrFail(request()->contact_id);
            $form['contact_id'] = $f->id;
            $form['contact'] = [
                'name' => $f->name,
                'id' => $f->id
            ];
        }

        if(request()->has('proposal_id')) {
            $f = Proposal::findOrFail(request()->proposal_id);
            $form['proposal_id'] = $f->id;
            $form['proposal'] = [
                'number' => $f->number,
                'id' => $f->id
            ];

            $c = $f->contact;

            $form['contact_id'] = $c->id;
            $form['contact'] = [
                'name' => $c->name,
                'id' => $c->id
            ];
        }

        if(request()->has('contract_id')) {
            $f = Contract::findOrFail(request()->contract_id);
            $form['contract_id'] = $f->id;
            $form['contract'] = [
                'number' => $f->number,
                'id' => $f->id
            ];

            $c = $f->contact;

            $form['contact_id'] = $c->id;
            $form['contact'] = [
                'name' => $c->name,
                'id' => $c->id
            ];
        }

        $id = settings()->get('default_invoice_template_id');

        if($id) {
            $c = Template::findOrFail($id);
            $form['template'] = [
                'name' => $c->name,
                'id' => $c->id
            ];
            $form['custom_fields'] = (new TemplateFields($c, []))->getFields();
            $form['template_id'] = $id;
        }

        $id = settings()->get('due_date_after_days');

        if($id) {
            $form['due_date'] = now()->addDays($id)->format('Y-m-d');
        }



        return to_json([
            'form' => $form
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('access', 'invoices.create');
        $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'template_id' => 'required|exists:templates,id',
            'proposal_id' => 'nullable|exists:proposals,id',
            'contract_id' => 'nullable|exists:contracts,id',
            'issue_date' => 'required|date_format:Y-m-d',
            'due_date' => 'required|date_format:Y-m-d|after_or_equal:issue_date',
            'reference' => 'nullable',
            'custom_fields' => 'required|array',
            'custom_fields_2' => 'array'
        ]);

        $d = new Invoice;
        $d->fill($request->except('custom_fields'));

        $pages = [];
        $grandTotal = 0;
        $subTotal = 0;

        foreach($request->custom_fields as $page) {
            foreach($page['user_fields'] as $section) {
                foreach($section['fields'] as $field) {
                    $k = $page['name'].'.'.__('lang.uf').'.'.$section['name'].'.'.$field['name'];
                    switch ($field['type']) {
                        case 'list':
                            $pages[$k] = [
                                'list_model' => $field['list_model'],
                                'class_name' => isset($field['class_name']) ? $field['class_name'] : ''
                            ];
                            break;

                        case 'table':
                            $pages[$k] = [
                                'thead' => $field['thead'],
                                'tbody' => $field['tbody'],
                                'tfoot' => $field['tfoot'],
                                'currency' => $field['currency'],
                                'colspan' => $field['colspan'],
                                'class_name' => isset($field['class_name']) ? $field['class_name'] : ''
                            ];
                            if(isset($field['invoice'])) {
                                if($field['invoice']['sub_total']) {
                                    $ke = $field['invoice']['sub_total'];
                                    $first = array_first($field['tfoot'], function ($value, $key) use($ke) {
                                        return $value['name'] == $ke;
                                    });

                                    if($first) {
                                        $subTotal = $first['model'];
                                    }
                                }

                                if($field['invoice']['grand_total']) {
                                    $ke = $field['invoice']['grand_total'];
                                    $first = array_first($field['tfoot'], function ($value, $key) use($ke) {
                                        return $value['name'] == $ke;
                                    });

                                    if($first) {
                                        $grandTotal = $first['model'];
                                    }
                                }
                            }
                            break;

                        default:
                            $pages[$k] = $field['model'];
                            break;
                    }
                }
            }
        }

        $d->grand_total = $grandTotal;
        $d->sub_total = $subTotal;

        $d->custom_values = json_encode($pages);
        $d->custom_values_2 = json_encode(custom_values($request->custom_fields_2));

        $id = settings()->get('invoice_status_on_create_id');

        if($id) {
            $c = Status::findOrFail($id);
            $d->status_id = $c->id;
        } else {
            $d->status_id = Status::first()->id;
        }

        $d = DB::transaction(function() use ($d) {
            $c = counter('invoice');
            $d->number = $c->number;
            $d->save();
            $c->increment('value');

            return $d;
        });

        return response()
            ->json([
                'saved' => true,
                'id' => $d->id
            ]);
    }

    public function show($id)
    {
        $this->authorize('access', 'invoices.show');
        $pp = Invoice::with([
        	'status:id,name,color', 'proposal:id,number',
            'contact:id,name,number', 'template:id,name',
            'contract:id,number', 'lines'
        	])
        	->findOrFail($id);

        $pp->all_statuses = Status::where('locked', 0)->get();
        $pp->custom_fields_2 = custom_fields_preview('invoices', $pp->custom_values_2);

        return to_json([
            'model' => $pp
        ]);
    }

    public function edit($id)
    {
        $this->authorize('access', 'invoices.update');
        $pp = Invoice::with([
        	'status:id,name,color', 'proposal:id,number',
            'contact:id,name,number', 'template:id,name',
            'contract:id,number'
        	])
        	->findOrFail($id);

        $template = Template::with('pages')->findOrFail($pp->template_id);
        $pp->custom_fields = (new TemplateFields($template, json_decode($pp->custom_values, true)))
        	->getFields();
        $pp->custom_fields_2 = custom_fields('invoices', $pp->custom_values_2);

        return to_json([
            'form' => $pp
        ]);
    }

    public function update($id, Request $request)
    {
        $this->authorize('access', 'invoices.update');
        $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'template_id' => 'required|exists:templates,id',
            'proposal_id' => 'nullable|exists:proposals,id',
            'contract_id' => 'nullable|exists:contracts,id',
            'issue_date' => 'required|date_format:Y-m-d',
            'due_date' => 'required|date_format:Y-m-d|after_or_equal:issue_date',
            'reference' => 'nullable',
            'custom_fields' => 'required|array',
            'custom_fields_2' => 'array'
        ]);

        $d = Invoice::findOrFail($id);
        $d->fill($request->except('custom_fields'));

        $pages = [];
        $grandTotal = 0;
        $subTotal = 0;

        foreach($request->custom_fields as $page) {
            foreach($page['user_fields'] as $section) {
                foreach($section['fields'] as $field) {
                    $k = $page['name'].'.'.__('lang.uf').'.'.$section['name'].'.'.$field['name'];
                    switch ($field['type']) {
                        case 'list':
                            $pages[$k] = [
                                'list_model' => $field['list_model'],
                                'class_name' => isset($field['class_name']) ? $field['class_name'] : ''
                            ];
                            break;

                        case 'table':
                            $pages[$k] = [
                                'thead' => $field['thead'],
                                'tbody' => $field['tbody'],
                                'tfoot' => $field['tfoot'],
                                'currency' => $field['currency'],
                                'colspan' => $field['colspan'],
                                'class_name' => isset($field['class_name']) ? $field['class_name'] : ''
                            ];
                            if(isset($field['invoice'])) {
                                if($field['invoice']['sub_total']) {
                                    $ke = $field['invoice']['sub_total'];
                                    $first = array_first($field['tfoot'], function ($value, $key) use($ke) {
                                        return $value['name'] == $ke;
                                    });

                                    if($first) {
                                        $subTotal = $first['model'];
                                    }
                                }

                                if($field['invoice']['grand_total']) {
                                    $ke = $field['invoice']['grand_total'];
                                    $first = array_first($field['tfoot'], function ($value, $key) use($ke) {
                                        return $value['name'] == $ke;
                                    });

                                    if($first) {
                                        $grandTotal = $first['model'];
                                    }
                                }
                            }
                            break;

                        default:
                            $pages[$k] = $field['model'];
                            break;
                    }
                }
            }
        }

        $d->grand_total = $grandTotal;
        $d->sub_total = $subTotal;

        $d->custom_values = json_encode($pages);
        $d->custom_values_2 = json_encode(custom_values($request->custom_fields_2));
        $d->save();

        return response()
            ->json([
                'saved' => true,
                'id' => $d->id
            ]);
    }

    public function preview($id, TemplateParser $preview)
    {
        $this->authorize('access', 'invoices.show');
        $p = Invoice::with([
            'status:id,name,color', 'proposal',
            'contract', 'contact.organization'
            ])->findOrFail($id);

        $template = Template::with('pages')
            ->findOrFail($p->template_id);

        return $preview->documentPreview($template, $p)
            ->output();
    }

    public function markAs($id, Request $request)
    {
        $this->authorize('access', 'invoices.change_status');
        $request->validate([
            'type' => 'required|integer|exists:invoice_statuses,id,locked,0'
        ]);

        $lead = Invoice::findOrFail($id);
        $lead->status_id = $request->type;
        $lead->save();

        return to_json([
            'saved' => true,
            'id' => $lead->id,
            'status' => $lead->status
        ]);
    }

    public function createPaymentRequest($id)
    {
        $this->authorize('access', 'invoices.send_payment_request');
        $invoice = Invoice::findOrFail($id);

        $d = new PaymentRequest;
        $d->contact_id = $invoice->contact_id;
        $d->invoice_id = $invoice->id;
        $d->email = $invoice->contact->email;
        $d->uuid = (string) Str::uuid();
        $d->expiry_at = now()->addDays(3); // todo settings

        $id = settings()->get('invoice_status_on_payment_request_id');

        if($id) {
            $k = Status::findOrFail($id);
            $invoice->status_id = $k->id;
        }

        $d = DB::transaction(function() use ($d, $invoice) {
            $c = counter('payment_request');
            $d->number = $c->number;
            $d->save();
            $c->increment('value');
            $invoice->save();
            return $d;
        });

        // sent email

        $info = [
            'email_to' => $d->contact->email,
            'bcc' => [],
            'subject' => '',
            'message' => ''
        ];

        $d->load(['contact.organization', 'invoice']);
        // dd($d->toArray());
        $vars = PaymentRequest::emailVariables();

        $info['subject'] = parseEmailTemplate(settings()
            ->get('payment_request_email_subject'), $vars, $d);
        $info['message'] = parseEmailTemplate(settings()
            ->get('payment_request_email_template'), $vars, $d);

        Mail::send(new SendNotification($d, $info));

        return to_json([
            'saved' => true,
            'id' => $d->id
        ]);
    }

    public function destroy($id)
    {
        $this->authorize('access', 'invoices.delete');
        $model = Invoice::findOrFail($id);

        if(DB::table('payment_lines')->where('invoice_id', $model->id)->count()) {
            return delete_first(__('lang.cannot_delete'));
        }

        if(DB::table('payment_requests')->where('invoice_id', $model->id)->count()) {
            return delete_first(__('lang.cannot_delete'));
        }

        $model->delete();

        return to_json([
            'deleted' => true
        ]);
    }
}
