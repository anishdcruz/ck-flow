<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lead\Status;
use DB;

class LeadStatusController extends Controller
{
    public function typeahead()
    {
        $results = Status::typeahead(['name']);

        return to_json([
            'results' => $results
        ]);
    }

    public function search()
    {
        return to_json([
            'collection' => Status::search()
        ]);
    }

    public function index()
    {
        $this->authorize('access', 'settings.leads');
        return to_json([
            'collection' => Status::filter()
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('access', 'settings.leads');
        $request->validate([
            'name' => 'required|unique:lead_statuses,name',
            'color' => 'required|alpha_dash|unique:lead_statuses,color',
            'locked' => 'required|boolean'
        ]);

        $item = new Status;
        $item->name = $request->name;
        $item->color = $request->color;
        $item->locked = $request->locked;
        $item->save();

        return to_json([
            'saved' => true,
            'item' => $item
        ]);
    }

    public function update($id, Request $request)
    {
        $this->authorize('access', 'settings.leads');
        $request->validate([
            'name' => 'required|unique:lead_statuses,name,'.$id.',id',
            'color' => 'required|alpha_dash|unique:lead_statuses,color,'.$id.',id',
            'locked' => 'required|boolean'
        ]);

        $item = Status::findOrFail($id);
        $item->name = $request->name;
        $item->color = $request->color;
        $item->locked = $request->locked;
        $item->save();

        return to_json([
            'saved' => true,
            'item' => $item
        ]);
    }

    public function destroy($id)
    {
        $this->authorize('access', 'settings.leads');
        $model = Status::findOrFail($id);

        if(DB::table('leads')->where('status_id', $model->id)->count()) {
            return delete_first(__('lang.cannot_delete'));
        }

        // check settings
        if(settings()->check('lead_status_on_create_id', $model->id)) {
            return delete_first(__('lang.cannot_delete'));
        }

        $model->delete();

        return to_json([
            'deleted' => true
        ]);
    }
}
