<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Opportunity\Opportunity;
use App\Opportunity\Stage;
use App\Opportunity\Category;
use App\Opportunity\Source;
use App\Contact\Contact;
use DB;
use App\Services\ExportCSV;

class OpportunityController extends Controller
{
	public function typeahead()
	{
	    $results = Opportunity::typeahead(['number', 'title']);

	    return to_json([
	        'results' => $results
	    ]);
	}

	public function search()
	{
	    return to_json([
	        'collection' => Opportunity::when(request('contact_id'), function($query) {
                return $query->where('contact_id', request('contact_id'));
            })->search()
	    ]);
	}

    public function export()
    {
        $this->authorize('access', 'opportunities.export');
        return (new ExportCSV(Opportunity::with('category', 'stage', 'contact', 'source'), 'opportunities'))
            ->download();
    }

    public function index()
    {
        $this->authorize('access', 'opportunities.index');
        $collection = Opportunity::with([
                'category:id,name', 'stage:id,name,color'
            ])->when(request('contact_id'), function($query) {
                return $query->where('contact_id', request('contact_id'));
            })->filter();

        return to_json([
            'collection' => $collection
        ]);
    }

    public function create()
    {
        $this->authorize('access', 'opportunities.create');
        $form = [
            'number' => __('lang.auto_generated'),
            'title' => '',
            'description' => '',
            'probability' => '50',
            'start_date' => date('Y-m-d'),
            'close_date' => null,
            'source_id' => null,
            'source' => null,
            'category' => null,
            'category_id' => null,
            'contact' => null,
            'contact_id' => null,
            'value' => 0,
            'custom_fields' => custom_fields('opportunities', "[]")
        ];

        if(request()->has('contact_id')) {
            $f = Contact::findOrFail(request()->contact_id);
            $form['contact_id'] = $f->id;
            $form['contact'] = [
                'name' => $f->name,
                'id' => $f->id
            ];
        }

        $id = settings()->get('default_opportunity_category_id');

        if($id) {
            $c = Category::findOrFail($id);
            $form['category'] = [
                'name' => $c->name,
                'id' => $c->id
            ];

            $form['category_id'] = $id;
        }

        $id = settings()->get('default_opportunity_source_id');

        if($id) {
            $c = Category::findOrFail($id);
            $form['source'] = [
                'name' => $c->name,
                'id' => $c->id
            ];

            $form['source_id'] = $id;
        }

        $id = settings()->get('default_probability');

        if($id) {
            $form['probability'] = $id;
        }

        $id = settings()->get('close_after_x_days');

        if($id) {
            $form['close_date'] = now()->addDays($id)->format('Y-m-d');
        }

        return to_json([
            'form' => $form
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('access', 'opportunities.create');
        $request->validate([
            'contact_id' => 'required|integer|exists:contacts,id',
        	'title' => 'required|string',
            'description' => 'required',
            'probability' => 'required|integer',
            'start_date' => 'required|date_format:Y-m-d',
            'close_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
            'source_id' => 'required|integer|exists:opportunity_sources,id',
            'category_id' => 'required|integer|exists:opportunity_categories,id',
            'value' => 'required|numeric|min:0',
            'custom_fields' => 'array'
        ]);

        $opportunity = new Opportunity;
        $opportunity->fill($request->all());
        $opportunity->custom_values = json_encode(custom_values($request->custom_fields));

        $id = settings()->get('opportunity_stage_on_create_id');

        if($id) {
            $c = Stage::findOrFail($id);
            $opportunity->stage_id = $c->id;
        } else {
            $opportunity->stage_id = Stage::first()->id;
        }

        $opportunity->status_id = 'open';

        $opportunity = DB::transaction(function() use ($opportunity) {
            $c = counter('opportunity');
            $opportunity->number = $c->number;
            $opportunity->save();
            $c->increment('value');

            return $opportunity;
        });

        return to_json([
            'saved' => true,
            'id' => $opportunity->id
        ]);
    }

    public function show($id)
    {
        $this->authorize('access', 'opportunities.show');
        $opportunity = Opportunity::with([
        	'category:id,name', 'stage:id,name,color', 'source:id,name',
            'contact:id,name,number'
        	])
        	->findOrFail($id);

        $opportunity->all_stages = Stage::where('locked', 0)->get();
        $opportunity->custom_fields = custom_fields_preview('opportunities', $opportunity->custom_values);

        return to_json([
            'model' => $opportunity
        ]);
    }

    public function edit($id)
    {
        $this->authorize('access', 'opportunities.update');
        $opportunity = Opportunity::with([
            'category:id,name', 'stage:id,name,color', 'source:id,name',
            'contact:id,name,number'
            ])
            ->findOrFail($id);

        $opportunity->custom_fields = custom_fields('opportunities', $opportunity->custom_values);

        return to_json([
            'form' => $opportunity
        ]);
    }

    public function update($id, Request $request)
    {
        $this->authorize('access', 'opportunities.update');
        $request->validate([
            'contact_id' => 'required|integer|exists:contacts,id',
            'title' => 'required|string',
            'description' => 'required',
            'probability' => 'required|integer',
            'start_date' => 'required|date_format:Y-m-d',
            'close_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
            'source_id' => 'required|integer|exists:opportunity_sources,id',
            'category_id' => 'required|integer|exists:opportunity_categories,id',
            'value' => 'required|numeric|min:0',
            'custom_fields' => 'array'
        ]);

        $opportunity = Opportunity::findOrFail($id);
        $opportunity->fill($request->all());
        $opportunity->custom_values = json_encode(custom_values($request->custom_fields));
        $opportunity->save();

        return to_json([
            'saved' => true,
            'id' => $opportunity->id
        ]);
    }

    public function markAs($id, Request $request)
    {
        $this->authorize('access', 'opportunities.change_stage');
        $request->validate([
            'type' => 'required|integer|exists:opportunity_stages,id,locked,0'
        ]);

        $lead = Opportunity::findOrFail($id);
        $lead->stage_id = $request->type;
        $lead->save();

        return to_json([
            'saved' => true,
            'id' => $lead->id,
            'stage' => $lead->stage
        ]);
    }

    public function markStatusAs($id, Request $request)
    {
        $this->authorize('access', 'opportunities.change_status');
        $request->validate([
            'type' => 'required|in:won,lost'
        ]);

        $lead = Opportunity::findOrFail($id);
        $lead->status_id = $request->type;
        switch ($request->type) {
            case 'won':
                $id = settings()->get('opportunity_stage_on_win_id');

                if($id) {
                    $c = Stage::findOrFail($id);
                    $lead->stage_id = $c->id;
                }
                break;

            case 'lost':
                $id = settings()->get('opportunity_stage_on_lost_id');

                if($id) {
                    $c = Stage::findOrFail($id);
                    $lead->stage_id = $c->id;
                }
                break;

            default:
                break;
        }
        $lead->save();

        return to_json([
            'saved' => true,
            'id' => $lead->id,
            'status_id' => $lead->status_id,
            'stage' => $lead->stage
        ]);
    }

    public function destroy($id)
    {
        $this->authorize('access', 'opportunities.delete');
        $model = Opportunity::findOrFail($id);

        if(DB::table('proposals')->where('opportunity_id', $model->id)->count()) {
            return delete_first(__('lang.cannot_delete'));
        }

        if(DB::table('expenses')->where('opportunity_id', $model->id)->count()) {
            return delete_first(__('lang.cannot_delete'));
        }

        $model->delete();

        return to_json([
            'deleted' => true
        ]);
    }
}
