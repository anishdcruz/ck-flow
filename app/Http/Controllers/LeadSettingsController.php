<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lead\Status;

class LeadSettingsController extends Controller
{
    public function show()
    {
        $this->authorize('access', 'settings.leads');
    	$form = [
    		'lead_status_on_create_id' => null,
    		'lead_status_on_create' => null,
    	];

    	$id = settings()->get('lead_status_on_create_id');

    	if($id) {
    		$c = Status::findOrFail($id);
    		$form['lead_status_on_create'] = [
    			'name' => $c->name,
    			'id' => $c->id
    		];

    		$form['default_item_category_id'] = $id;
    	}

    	return to_json([
    		'form' => $form
    	]);
    }

    public function update(Request $request)
    {
        $this->authorize('access', 'settings.leads');
        $request->validate([
        	'lead_status_on_create_id' => 'required|integer',
        ]);

        settings()->setMany(
        	$request->only([
        		'lead_status_on_create_id'
        	])
        );

        return to_json([
            'saved' => true
        ]);
    }
}
