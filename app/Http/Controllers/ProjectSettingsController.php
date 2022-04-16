<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Project\Stage;
use App\Project\Category;

class ProjectSettingsController extends Controller
{
        public function show()
        {
            $this->authorize('access', 'settings.projects');
        	$form = [
        		'project_stage_on_create_id' => null,
        		'project_stage_on_create' => null,
        		'default_project_category_id' => null,
        		'default_project_category' => null
        	];

        	$id = settings()->get('project_stage_on_create_id');

        	if($id) {
        		$c = Stage::findOrFail($id);
        		$form['project_stage_on_create'] = [
        			'name' => $c->name,
        			'id' => $c->id
        		];

        		$form['project_stage_on_create_id'] = $id;
        	}

        	$id = settings()->get('default_project_category_id');

        	if($id) {
        		$c = Category::findOrFail($id);
        		$form['default_project_category'] = [
        			'name' => $c->name,
        			'id' => $c->id
        		];

        		$form['default_project_category_id'] = $id;
        	}

        	return to_json([
        		'form' => $form
        	]);
        }

        public function update(Request $request)
        {
            $this->authorize('access', 'settings.projects');
            $request->validate([
        		'project_stage_on_create_id' => 'required|integer',
    		    'default_project_category_id' => 'required|integer'
            ]);

            settings()->setMany(
            	$request->only([
            		'project_stage_on_create_id',
            		'default_project_category_id'
            	])
            );

            return to_json([
                'saved' => true
            ]);
        }
}
