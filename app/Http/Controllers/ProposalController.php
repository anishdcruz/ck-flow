<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Proposal\Proposal;
use App\Proposal\Status;
use App\Services\TemplateFields;
use App\Template\Template;
use App\Services\TemplateParser;
use App\Services\ExportCSV;
use App\Contact\Contact;
use App\Opportunity\Opportunity;

use DB;

class ProposalController extends Controller
{
    public function typeahead()
	{
	    $results = Proposal::typeahead(['number']);

	    return to_json([
	        'results' => $results
	    ]);
	}

	public function search()
	{
	    return to_json([
	        'collection' => Proposal::when(request('contact_id'), function($query) {
                return $query->where('contact_id', request('contact_id'));
            })->search()
	    ]);
	}

    public function index()
    {
        $this->authorize('access', 'proposals.index');
        $collection = Proposal::with([
                'contact:id,name', 'status:id,name,color',
                'opportunity:id,number'
            ])->when(request('contact_id'), function($query) {
                return $query->where('contact_id', request('contact_id'));
            })->when(request('opportunity_id'), function($query) {
                return $query->where('opportunity_id', request('opportunity_id'));
            })->filter();

        return to_json([
            'collection' => $collection
        ]);
    }

    public function export()
    {
        $this->authorize('access', 'proposals.export');
        return (new ExportCSV(Proposal::with('contact', 'status', 'opportunity'), 'proposals'))
            ->download();
    }

    public function create()
    {
        $this->authorize('access', 'proposals.create');
        $form = [
            'number' => __('lang.auto_generated'),
            'contact_id' => null,
            'contact' => null,
            'template_id' => null,
            'template' => null,
            'opportunity_id' => null,
            'opportunity' => null,
            'issue_date' => date('Y-m-d'),
            'expiry_date' => null,
            'custom_fields' => null,
            'custom_fields_2' => custom_fields('proposals', "[]")
        ];

        if(request()->has('contact_id')) {
            $f = Contact::findOrFail(request()->contact_id);
            $form['contact_id'] = $f->id;
            $form['contact'] = [
                'name' => $f->name,
                'id' => $f->id
            ];
        }

        if(request()->has('opportunity_id')) {
            $f = Opportunity::findOrFail(request()->opportunity_id);
            $form['opportunity_id'] = $f->id;
            $form['opportunity'] = [
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

        $id = settings()->get('default_proposal_template_id');

        if($id) {
            $c = Template::findOrFail($id);
            $form['template'] = [
                'name' => $c->name,
                'id' => $c->id
            ];
            $form['custom_fields'] = (new TemplateFields($c, []))->getFields();
            $form['template_id'] = $id;
        }

        $id = settings()->get('close_proposal_after_days');

        if($id) {
            $form['expiry_date'] = now()->addDays($id)->format('Y-m-d');
        }

        return to_json([
            'form' => $form
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('access', 'proposals.create');
        $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'template_id' => 'required|exists:templates,id',
            'opportunity_id' => 'nullable|exists:opportunities,id',
            'issue_date' => 'required|date_format:Y-m-d',
            'expiry_date' => 'required|date_format:Y-m-d|after_or_equal:issue_date',
            'custom_fields' => 'array',
            'custom_fields_2' => 'array'
        ]);

        $d = new Proposal;
        $d->contact_id = $request->contact_id;
        $d->template_id = $request->template_id;
        $d->opportunity_id = $request->opportunity_id;
        $d->issue_date = $request->issue_date;
        $d->expiry_date = $request->expiry_date;

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

        $id = settings()->get('proposal_status_on_create_id');

        if($id) {
            $c = Status::findOrFail($id);
            $d->status_id = $c->id;
        } else {
            $d->status_id = Status::first()->id;
        }

        $d = DB::transaction(function() use ($d) {
            $c = counter('proposal');
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
        $this->authorize('access', 'proposals.show');
        $pp = Proposal::with([
        	'status:id,name,color', 'opportunity:id,number',
            'contact:id,name,number', 'template:id,name'
        	])
        	->findOrFail($id);

        $pp->all_statuses = Status::where('locked', 0)->get();
        $pp->custom_fields_2 = custom_fields_preview('proposals', $pp->custom_values_2);

        return to_json([
            'model' => $pp
        ]);
    }

    public function edit($id)
    {
        $this->authorize('access', 'proposals.update');
        $pp = Proposal::with([
        	'status:id,name,color', 'opportunity:id,number',
            'contact:id,name,number', 'template:id,type_id,name'
        	])
        	->findOrFail($id);
        $template = Template::with('pages')->findOrFail($pp->template_id);
        $pp->custom_fields = (new TemplateFields($template, json_decode($pp->custom_values, true)))->getFields();
        $pp->custom_fields_2 = custom_fields('proposals', $pp->custom_values_2);
        return to_json([
            'form' => $pp
        ]);
    }

    public function update($id, Request $request)
    {
        $this->authorize('access', 'proposals.update');
        $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'template_id' => 'required|exists:templates,id',
            'opportunity_id' => 'nullable|exists:opportunities,id',
            'issue_date' => 'required|date_format:Y-m-d',
            'expiry_date' => 'required|date_format:Y-m-d|after_or_equal:issue_date',
            'custom_fields' => 'required|array',
            'custom_fields_2' => 'array'
        ]);

        $d = Proposal::findOrFail($id);
        $d->contact_id = $request->contact_id;
        $d->template_id = $request->template_id;
        $d->opportunity_id = $request->opportunity_id;
        $d->issue_date = $request->issue_date;
        $d->expiry_date = $request->expiry_date;

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
        $this->authorize('access', 'proposals.show');
        $p = Proposal::with([
            'status:id,name,color',
            'opportunity',
            'contact.organization'
            ])->findOrFail($id);

        $template = Template::with('pages')
            ->findOrFail($p->template_id);

        return $preview->documentPreview($template, $p)
            ->output();
    }

    public function markAs($id, Request $request)
    {
        $this->authorize('access', 'proposals.change_status');
        $request->validate([
            'type' => 'required|integer|exists:proposal_statuses,id,locked,0'
        ]);

        $lead = Proposal::findOrFail($id);
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
        $this->authorize('access', 'proposals.delete');
        $model = Proposal::findOrFail($id);

        if(DB::table('contracts')->where('proposal_id', $model->id)->count()) {
            return delete_first(__('lang.cannot_delete'));
        }

        if(DB::table('invoices')->where('proposal_id', $model->id)->count()) {
            return delete_first(__('lang.cannot_delete'));
        }

        $model->delete();

        return to_json([
            'deleted' => true
        ]);
    }
}
