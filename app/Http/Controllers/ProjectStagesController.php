<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Project\Stage;
use DB;

class ProjectStagesController extends Controller
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
        $this->authorize('access', 'settings.projects');
        return to_json([
            'collection' => Stage::filter()
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('access', 'settings.projects');
        $request->validate([
            'name' => 'required|unique:project_stages,name',
            'color' => 'required|alpha_dash|unique:project_stages,color',
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
        $this->authorize('access', 'settings.projects');
        $request->validate([
            'name' => 'required|unique:project_stages,name,'.$id.',id',
            'color' => 'required|alpha_dash|unique:project_stages,color,'.$id.',id',
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
        $this->authorize('access', 'settings.projects');
        $model = Stage::findOrFail($id);

        if(DB::table('projects')->where('stage_id', $model->id)->count()) {
            return delete_first(__('lang.cannot_delete'));
        }

        // check settings
        if(settings()->check('project_stage_on_create_id', $model->id)) {
            return delete_first(__('lang.cannot_delete'));
        }

        $model->delete();

        return to_json([
            'deleted' => true
        ]);
    }
}
