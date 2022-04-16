<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contract\Contract;
use App\Contract\Type;
use App\Contract\Status;
use App\Services\TemplateFields;
use App\Template\Template;
use App\Services\TemplateParser;
use DB;
use App\Services\ExportCSV;
use App\Contact\Contact;
use App\Proposal\Proposal;

class ContractController extends Controller
{
    public function typeahead()
	{
	    $results = Contract::typeahead(['number']);

	    return to_json([
	        'results' => $results
	    ]);
	}

	public function search()
	{
	    return to_json([
	        'collection' => Contract::when(request('contact_id'), function($query) {
                return $query->where('contact_id', request('contact_id'));
            })->search()
	    ]);
	}

    public function export()
    {
        $this->authorize('access', 'contracts.export');
        return (new ExportCSV(Contract::with('contact', 'status', 'proposal', 'template', 'type'), 'contracts'))
            ->download();
    }

    public function index()
    {
        $this->authorize('access', 'contracts.index');
        return to_json([
            'collection' => Contract::with([
            	'contact:id,name', 'status:id,name,color',
            	'proposal:id,number'
            ])->when(request('contact_id'), function($query) {
                return $query->where('contact_id', request('contact_id'));
            })->when(request('proposal_id'), function($query) {
                return $query->where('proposal_id', request('proposal_id'));
            })->filter()
        ]);
    }

    public function create()
    {
        $this->authorize('access', 'contracts.create');
        $form = [
            'number' => __('lang.auto_generated'),
            'value' => 0,
            'auto_renewal' => 0,
            'no_of_months' => 0,
            'contact_id' => null,
            'contact' => null,
            'template_id' => null,
            'template' => null,
            'type_id' => null,
            'type' => null,
            'proposal_id' => null,
            'proposal' => null,
            'start_date' => date('Y-m-d'),
            'expiry_date' => null,
            'custom_fields' => null,
            'custom_fields_2' => custom_fields('contracts', "[]")
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

        $id = settings()->get('default_contract_template_id');

        if($id) {
            $c = Template::findOrFail($id);
            $form['template'] = [
                'name' => $c->name,
                'id' => $c->id
            ];
            $form['custom_fields'] = (new TemplateFields($c, []))->getFields();
            $form['template_id'] = $id;
        }

        $id = settings()->get('default_contract_type_id');

        if($id) {
            $c = Type::findOrFail($id);

            $form['type'] = [
                'name' => $c->name,
                'id' => $c->id
            ];

            $form['type_id'] = $id;
        }

        return to_json([
            'form' => $form
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('access', 'contracts.create');
        $request->validate([
            'title' => 'required',
            'contact_id' => 'required|exists:contacts,id',
            'template_id' => 'required|exists:templates,id',
            'proposal_id' => 'nullable|exists:proposals,id',
            'type_id' => 'required|exists:contract_types,id',
            'start_date' => 'required|date_format:Y-m-d',
            'expiry_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
            'value' => 'required|numeric|min:0',
            'auto_renewal' => 'required|boolean',
            'no_of_months' => 'required_if:auto_renewal,1|integer',
            'custom_fields' => 'required|array',
            'custom_fields_2' => 'array'
        ]);

        $d = new Contract;
        $d->fill($request->except('custom_fields'));

        $pages = [];

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
                            break;

                        default:
                            $pages[$k] = $field['model'];
                            break;
                    }
                }
            }
        }

        $d->custom_values = json_encode($pages);
        $d->custom_values_2 = json_encode(custom_values($request->custom_fields_2));
        $id = settings()->get('contract_status_on_create_id');

        if($id) {
            $c = Status::findOrFail($id);
            $d->status_id = $c->id;
        } else {
            $d->status_id = Status::first()->id;
        }

        $d = DB::transaction(function() use ($d) {
            $c = counter('contract');
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
        $this->authorize('access', 'contracts.show');
        $pp = Contract::with([
        	'status:id,name,color', 'proposal:id,number',
            'contact:id,name,number', 'type:id,name',
            'template:id,name'
        	])
        	->findOrFail($id);


        $pp->all_statuses = Status::where('locked', 0)->get();
        $pp->custom_fields_2 = custom_fields_preview('contracts', $pp->custom_values_2);

        return to_json([
            'model' => $pp
        ]);
    }

    public function edit($id)
    {
        $this->authorize('access', 'contracts.update');
        $pp = Contract::with([
        	'status:id,name,color', 'proposal:id,number',
            'contact:id,name,number', 'template:id,type_id,name',
            'type:id,name'
        	])
        	->findOrFail($id);
        $template = Template::with('pages')->findOrFail($pp->template_id);
        $pp->custom_fields = (new TemplateFields($template, json_decode($pp->custom_values, true)))->getFields();
        $pp->custom_fields_2 = custom_fields_preview('contracts', $pp->custom_values_2);

        return to_json([
            'form' => $pp
        ]);
    }

    public function update($id, Request $request)
    {
        $this->authorize('access', 'contracts.update');
        $request->validate([
            'title' => 'required',
            'contact_id' => 'required|exists:contacts,id',
            'template_id' => 'required|exists:templates,id',
            'proposal_id' => 'nullable|exists:proposals,id',
            'type_id' => 'required|exists:contract_types,id',
            'start_date' => 'required|date_format:Y-m-d',
            'expiry_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
            'value' => 'required|numeric|min:0',
            'auto_renewal' => 'required|boolean',
            'no_of_months' => 'required_if:auto_renewal,1|integer',
            'custom_fields' => 'required|array',
            'custom_fields_2' => 'array'
        ]);

        $d = Contract::findOrFail($id);
        $d->fill($request->except('custom_fields'));

        $pages = [];

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
                            break;

                        default:
                            $pages[$k] = $field['model'];
                            break;
                    }
                }
            }
        }

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
        $this->authorize('access', 'contracts.show');
        $p = Contract::with([
            'status:id,name,color', 'proposal',
            'contact.organization'
            ])->findOrFail($id);

        $template = Template::with('pages')
            ->findOrFail($p->template_id);

        return $preview->documentPreview($template, $p)
            ->output();
    }

    public function markAs($id, Request $request)
    {
        $this->authorize('access', 'contracts.change_status');
        $request->validate([
            'type' => 'required|integer|exists:contract_statuses,id,locked,0'
        ]);

        $lead = Contract::findOrFail($id);
        $lead->status_id = $request->type;
        $lead->save();

        return to_json([
            'saved' => true,
            'id' => $lead->id,
            'status' => $lead->status
        ]);
    }

    public function destroy($id)
    {
        $this->authorize('access', 'contracts.delete');
        $model = Contract::findOrFail($id);

        if(DB::table('invoices')->where('contract_id', $model->id)->count()) {
            return delete_first(__('lang.cannot_delete'));
        }

        $model->delete();

        return to_json([
            'deleted' => true
        ]);
    }
}
