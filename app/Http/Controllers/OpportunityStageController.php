<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Opportunity\Stage;
use DB;

class OpportunityStageController extends Controller
{
    public function typeahead()
    {
        $results = Stage::typeahead(['name']);

        return to_json([
            'results' => $results
        ]);
    }

    public function search()
    {
        return to_json([
            'collection' => Stage::search()
        ]);
    }

    public function index()
    {
        $this->authorize('access', 'settings.opportunities');
        return to_json([
            'collection' => Stage::filter()
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('access', 'settings.opportunities');
        $request->validate([
            'name' => 'required|unique:opportunity_stages,name',
            'color' => 'required|alpha_dash|unique:opportunity_stages,color',
            'locked' => 'required|boolean'
        ]);

        $item = new Stage;
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
        $this->authorize('access', 'settings.opportunities');
        $request->validate([
            'name' => 'required|unique:opportunity_stages,name,'.$id.',id',
            'color' => 'required|alpha_dash|unique:opportunity_stages,color,'.$id.',id',
            'locked' => 'required|boolean'
        ]);

        $item = Stage::findOrFail($id);
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
        $this->authorize('access', 'settings.opportunities');
        $model = Stage::findOrFail($id);

        if(DB::table('opportunities')->where('stage_id', $model->id)->count()) {
            return delete_first(__('lang.cannot_delete'));
        }

        // check settings
        if(settings()->check('opportunity_stage_on_create_id', $model->id)) {
            return delete_first(__('lang.cannot_delete'));
        }

        if(settings()->check('opportunity_stage_on_win_id', $model->id)) {
            return delete_first(__('lang.cannot_delete'));
        }

        if(settings()->check('opportunity_stage_on_win_id', $model->id)) {
            return delete_first(__('lang.cannot_delete'));
        }

        $model->delete();

        return to_json([
            'deleted' => true
        ]);
    }
}
