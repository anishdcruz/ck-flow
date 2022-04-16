<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lead\Lead;
use App\Lead\Status;
use DB;
use App\Services\ExportCSV;

class LeadController extends Controller
{
	public function typeahead()
	{
	    $results = Lead::typeahead(['number', 'person', 'company']);

	    return to_json([
	        'results' => $results
	    ]);
	}

	public function search()
	{
	    return to_json([
	        'collection' => Lead::search()
	    ]);
	}

    public function export()
    {
        $this->authorize('access', 'leads.export');
        return (new ExportCSV(Lead::with('status'), 'leads'))
            ->download();
    }

    public function index()
    {
        $this->authorize('access', 'leads.index');
        return to_json([
            'collection' => Lead::with('status:id,name,color')->filter()
        ]);
    }

    public function create()
    {
        $this->authorize('access', 'leads.create');
        $lead = [
            'person' => '',
            'organization' => null,
            'email' => '',
            'title' => '',
            'department' => '',
            'mobile' => '',
            'phone' => '',
            'fax' => '',
            'website' => '',
            'primary_address' => '',
            'other_address' => '',
            'number' => __('lang.auto_generated'),
            'custom_fields' => custom_fields('leads', "[]")
        ];

        return to_json([
            'form' => $lead
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('access', 'leads.create');
        $request->validate([
            'person' => 'required|string',
            'organization' => 'nullable',
            'email' => 'required|email',
            'title' => 'nullable|string',
            'department' => 'nullable|string',
            'mobile' => 'nullable|string',
            'phone' => 'nullable|string',
            'fax' => 'nullable|string',
            'website' => 'nullable|string',
            'primary_address' => 'required|string',
            'other_address' => 'nullable|string',
            'custom_fields' => 'array'
        ]);

        $lead = new Lead;
        $lead->fill($request->all());
        $lead->custom_values = json_encode(custom_values($request->custom_fields));

        $id = settings()->get('lead_status_on_create_id');


        if($id) {
            $c = Status::findOrFail($id);
            $lead->status_id = $c->id;
        } else {
            $lead->status_id = Status::first()->id;
        }

        $lead = DB::transaction(function() use ($lead) {
            $c = counter('lead');
            $lead->number = $c->number;
            $lead->save();
            $c->increment('value');

            return $lead;
        });

        return to_json([
            'saved' => true,
            'id' => $lead->id
        ]);
    }

    public function show($id)
    {
        $this->authorize('access', 'leads.show');
        $lead = Lead::with('status:id,name,color')->findOrFail($id);
        $lead->all_status = Status::whereLocked(0)->get();
        $lead->custom_fields = custom_fields_preview('leads', $lead->custom_values);

        return to_json([
            'model' => $lead,
        ]);
    }

    public function edit($id)
    {
        $this->authorize('access', 'leads.update');
        $lead = Lead::with('status:id,name,color')->findOrFail($id);
        $lead->custom_fields = custom_fields('leads', $lead->custom_values);

        return to_json([
            'form' => $lead
        ]);
    }

    public function update($id, Request $request)
    {
        $this->authorize('access', 'leads.update');
        $request->validate([
            'person' => 'required|string',
            'organization' => 'nullable',
            'email' => 'required|email',
            'title' => 'nullable|string',
            'department' => 'nullable|string',
            'mobile' => 'nullable|string',
            'phone' => 'nullable|string',
            'fax' => 'nullable|string',
            'website' => 'nullable|string',
            'primary_address' => 'required|string',
            'other_address' => 'nullable|string',
            'custom_fields' => 'array'
        ]);

        $lead = Lead::findOrFail($id);
        $lead->fill($request->all());
        $lead->custom_values = json_encode(custom_values($request->custom_fields));
        $lead->save();

        return to_json([
            'saved' => true,
            'id' => $lead->id
        ]);
    }

    public function markAs($id, Request $request)
    {
        $this->authorize('access', 'leads.change_status');
        $request->validate([
            'type' => 'required|integer|exists:lead_statuses,id,locked,0'
        ]);

        $lead = Lead::findOrFail($id);
        $lead->status_id = $request->type;
        $lead->save();

        return to_json([
            'saved' => true,
            'id' => $lead->id,
            'status_id' => $lead->status_id,
            'status' => $lead->status
        ]);
    }

    public function destroy($id)
    {
        $this->authorize('access', 'leads.delete');
        $model = Lead::findOrFail($id);

        $model->delete();

        return to_json([
            'deleted' => true
        ]);
    }
}
