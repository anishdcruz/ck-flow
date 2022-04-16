<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Opportunity\Category;
use App\Opportunity\Source;
use App\Opportunity\Stage;
class OpportunitySettingsController extends Controller
{
    public function show()
    {
        $this->authorize('access', 'settings.opportunities');
    	$form = [
    		'default_opportunity_category_id' => null,
    		'default_opportunity_category' => null,
    		'default_opportunity_source_id' => null,
    		'default_opportunity_source' => null,
    		'opportunity_stage_on_create_id' => null,
    		'opportunity_stage_on_create' => null,
    		'opportunity_stage_on_win_id' => null,
    		'opportunity_stage_on_win' => null,
    		'opportunity_stage_on_lost_id' => null,
    		'opportunity_stage_on_lost' => null,
    		'close_after_x_days' => settings()->get('close_after_x_days'),
    		'default_probability' => settings()->get('default_probability')
    	];

    	$id = settings()->get('default_opportunity_category_id');

    	if($id) {
    		$c = Category::findOrFail($id);
    		$form['default_opportunity_category'] = [
    			'name' => $c->name,
    			'id' => $c->id
    		];

    		$form['default_opportunity_category_id'] = $id;
    	}

    	$id = settings()->get('opportunity_stage_on_create_id');

    	if($id) {
    		$c = Stage::findOrFail($id);
    		$form['opportunity_stage_on_create'] = [
    			'name' => $c->name,
    			'id' => $c->id
    		];

    		$form['opportunity_stage_on_create_id'] = $id;
    	}

    	$id = settings()->get('opportunity_stage_on_win_id');

    	if($id) {
    		$c = Stage::findOrFail($id);
    		$form['opportunity_stage_on_win'] = [
    			'name' => $c->name,
    			'id' => $c->id
    		];

    		$form['opportunity_stage_on_win_id'] = $id;
    	}

    	$id = settings()->get('opportunity_stage_on_lost_id');

    	if($id) {
    		$c = Stage::findOrFail($id);
    		$form['opportunity_stage_on_lost'] = [
    			'name' => $c->name,
    			'id' => $c->id
    		];

    		$form['opportunity_stage_on_lost_id'] = $id;
    	}

    	$id = settings()->get('default_opportunity_source_id');

    	if($id) {
    		$c = Source::findOrFail($id);
    		$form['default_opportunity_source'] = [
    			'name' => $c->name,
    			'id' => $c->id
    		];

    		$form['default_opportunity_source_id'] = $id;
    	}

    	return to_json([
    		'form' => $form
    	]);
    }

    public function update(Request $request)
    {
        $this->authorize('access', 'settings.opportunities');
        $request->validate([
        	'default_opportunity_category_id' => 'required|integer',
        	'default_opportunity_source_id' => 'required|integer',
        	'close_after_x_days' => 'nullable|integer',
        	'default_probability' => 'required|integer',
        	'opportunity_stage_on_create_id' => 'required|integer',
        	'opportunity_stage_on_win_id' => 'required|integer',
        	'opportunity_stage_on_lost_id' => 'required|integer'
        ]);

        settings()->setMany(
        	$request->only([
        		'default_opportunity_category_id',
        		'default_opportunity_source_id',
        		'close_after_x_days',
        		'default_probability',
        		'opportunity_stage_on_create_id',
        		'opportunity_stage_on_win_id',
        		'opportunity_stage_on_lost_id'
        	])
        );

        return to_json([
            'saved' => true
        ]);
    }
}
