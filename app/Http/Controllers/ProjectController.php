<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Project\Project;
use App\Project\Stage;
use App\Project\Category;
use DB;
use App\Services\ExportCSV;
use App\Contact\Contact;
use App\Proposal\Proposal;

class ProjectController extends Controller
{
    public function typeahead()
	{
	    $results = Project::typeahead(['number']);

	    return to_json([
	        'results' => $results
	    ]);
	}

	public function search()
	{
	    return to_json([
	        'collection' => Project::when(request('contact_id'), function($query) {
                return $query->where('contact_id', request('contact_id'));
            })->search()
	    ]);
	}

    public function index()
    {
        $this->authorize('access', 'projects.index');
        $collection = Project::with([
                'contact:id,name', 'stage:id,name,color','category:id,name'
            ])->when(request('contact_id'), function($query) {
                return $query->where('contact_id', request('contact_id'));
            })->filter();

        return to_json([
            'collection' => $collection
        ]);
    }

    public function export()
    {
        $this->authorize('access', 'projects.export');
        return (new ExportCSV(Project::with('contact', 'stage', 'category', 'proposal'), 'projects'))
            ->download();
    }

    public function create()
    {
        $this->authorize('access', 'projects.create');
        $form = [
            'number' => __('lang.auto_generated'),
            'contact_id' => null,
            'contact' => null,
            'title' => '',
            'description' => '',
            'category_id' => null,
            'category' => null,
            'proposal_id' => null,
            'proposal' => null,
            'start_date' => date('Y-m-d'),
            'estimated_finish_date' => null,
            'deadline_date' => null,
            'actual_start_date' => null,
            'actual_end_date' => null,
            'estimated_cost' => 0,
            'custom_fields' => custom_fields('projects', "[]")
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

        $id = settings()->get('default_project_category_id');

        if($id) {
            $c = Category::findOrFail($id);

            $form['category'] = [
                'name' => $c->name,
                'id' => $c->id
            ];

            $form['category_id'] = $id;
        }

        return to_json([
            'form' => $form
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('access', 'projects.create');
        $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'title' => 'required|string',
            'description' => 'required',
            'category_id' => 'required|exists:project_categories,id',
            'proposal_id' => 'nullable|exists:proposals,id',
            'start_date' => 'required|date_format:Y-m-d',
            'estimated_finish_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
            'deadline_date' => 'nullable|date_format:Y-m-d',
            'actual_start_date' => 'nullable|date_format:Y-m-d',
            'actual_end_date' => 'nullable|date_format:Y-m-d',
            'estimated_cost' => 'required|numeric|min:0',
            'custom_fields' => 'array'
        ]);

        $d = new Project;
        $d->fill($request->except('custom_fields'));
        $d->custom_values = json_encode(custom_values($request->custom_fields));

        $id = settings()->get('project_status_on_create_id');

        if($id) {
            $c = Stage::findOrFail($id);
            $d->stage_id = $c->id;
        } else {
            $d->stage_id = Stage::first()->id;
        }

        $d = DB::transaction(function() use ($d) {
            $c = counter('project');
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
        $this->authorize('access', 'projects.show');
        $pp = Project::with([
        	'stage:id,name,color', 'proposal:id,number',
            'contact:id,name,number', 'category:id,name'
        	])
        	->findOrFail($id);

        $pp->all_stages = Stage::where('locked', 0)->get();
        $pp->custom_fields = custom_fields_preview('contacts', $pp->custom_values);

        return to_json([
            'model' => $pp
        ]);
    }

    public function edit($id)
    {
        $this->authorize('access', 'projects.update');
        $pp = Project::with([
            'stage:id,name,color', 'proposal:id,number',
            'contact:id,name,number', 'category:id,name'
            ])
            ->findOrFail($id);

        $pp->custom_fields = custom_fields('projects', $pp->custom_values);

        return to_json([
            'form' => $pp
        ]);
    }

    public function update($id, Request $request)
    {
        $this->authorize('access', 'projects.update');
        $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'title' => 'required|string',
            'description' => 'required',
            'category_id' => 'required|exists:project_categories,id',
            'proposal_id' => 'nullable|exists:proposals,id',
            'start_date' => 'required|date_format:Y-m-d',
            'estimated_finish_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
            'deadline_date' => 'nullable|date_format:Y-m-d',
            'actual_start_date' => 'nullable|date_format:Y-m-d',
            'actual_end_date' => 'nullable|date_format:Y-m-d',
            'estimated_cost' => 'required|numeric|min:0',
            'custom_fields' => 'array'
        ]);

        $d = Project::findOrFail($id);
        $d->fill($request->except('custom_fields'));
        $d->custom_values = json_encode(custom_values($request->custom_fields));
        $d->save();

        return response()
            ->json([
                'saved' => true,
                'id' => $d->id
            ]);
    }

    public function markAs($id, Request $request)
    {
        $this->authorize('access', 'projects.change_stage');
        $request->validate([
            'type' => 'required|integer|exists:project_stages,id,locked,0'
        ]);

        $lead = Project::findOrFail($id);
        $lead->stage_id = $request->type;
        $lead->save();

        return to_json([
            'saved' => true,
            'id' => $lead->id,
            'stage' => $lead->stage
        ]);
    }

    public function destroy($id)
    {
        $this->authorize('access', 'projects.delete');
        $model = Project::findOrFail($id);

        if(DB::table('project_tasks')->where('project_id', $model->id)->count()) {
            return delete_first(__('lang.cannot_delete'));
        }

        if(DB::table('expenses')->where('project_id', $model->id)->count()) {
            return delete_first(__('lang.cannot_delete'));
        }

        $model->delete();

        return to_json([
            'deleted' => true
        ]);
    }
}
