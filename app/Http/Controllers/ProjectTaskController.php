<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Project\Task;
use App\Project\Project;
use DB;

class ProjectTaskController extends Controller
{
    public function index()
    {
        $this->authorize('access', 'projects.show');
        return to_json([
            'collection' => Task::with('stage')
                ->when(request('project_id'), function($query) {
                return $query->where('project_id', request('project_id'));
            })->filter()
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('access', 'projects.add_tasks');
        $request->validate([
        	'project_id' => 'required|integer|exists:projects,id',
            'title' => 'required',
            'start_date' => 'required|date_format:Y-m-d',
            'due_date' => 'nullable|date_format:Y-m-d',
            'description' => 'required',
            'stage_id' => 'required|exists:project_stages,id'
        ]);

        $projects = Project::findOrFail($request->project_id);

        $item = new Task($request->all());
        $projects->tasks()->save($item);

        return to_json([
            'saved' => true,
            'item' => $item
        ]);
    }

    public function update($id, Request $request)
    {
        $this->authorize('access', 'projects.update_tasks');
        $request->validate([
        	'project_id' => 'required|integer|exists:projects,id',
            'title' => 'required',
            'start_date' => 'required|date_format:Y-m-d',
            'due_date' => 'nullable|date_format:Y-m-d',
            'description' => 'required',
            'stage_id' => 'required|exists:project_stages,id'
        ]);

        $task = Task::whereId($id)
        	->where('project_id', $request->project_id)
        	->firstOrFail();

        $task->fill($request->all());
        $task->save();

        return to_json([
            'saved' => true,
            'item' => $task
        ]);
    }

    public function destroy($id)
    {
        $this->authorize('access', 'project.delete_tasks');
        $model = Task::findOrFail($id);

        $model->delete();

        return to_json([
            'deleted' => true
        ]);
    }
}
