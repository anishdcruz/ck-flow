<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Organization\Category;

class OrganizationSettingsController extends Controller
{
    public function show()
    {
        $this->authorize('access', 'settings.organizations');
    	$form = [
    		'default_organization_category_id' => null,
    		'default_organization_category' => null
    	];

    	$id = settings()->get('default_organization_category_id');

    	if($id) {
    		$c = Category::findOrFail($id);
    		$form['default_organization_category'] = [
    			'name' => $c->name,
    			'id' => $c->id
    		];

    		$form['default_organization_category_id'] = $id;
    	}

    	return to_json([
    		'form' => $form
    	]);
    }

    public function update(Request $request)
    {
        $this->authorize('access', 'settings.organizations');
        $request->validate([
        	'default_organization_category_id' => 'required|integer',
        ]);

        settings()->setMany(
        	$request->only([
	    		'default_organization_category_id'
        	])
        );


        return to_json([
            'saved' => true
        ]);
    }
}
