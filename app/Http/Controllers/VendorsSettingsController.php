<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Vendor\Category;

class VendorsSettingsController extends Controller
{
    public function show()
    {
        $this->authorize('access', 'settings.vendors');
    	$form = [
    		'default_vendor_category_id' => null,
    		'default_vendor_category' => null,
    	];

    	$id = settings()->get('default_vendor_category_id');

    	if($id) {
    		$c = Category::findOrFail($id);
    		$form['default_vendor_category'] = [
    			'name' => $c->name,
    			'id' => $c->id
    		];

    		$form['default_vendor_category_id'] = $id;
    	}

    	return to_json([
    		'form' => $form
    	]);
    }

    public function update(Request $request)
    {
        $this->authorize('access', 'settings.vendors');
        $request->validate([
    		'default_vendor_category_id' => 'required|integer'
        ]);

        settings()->setMany(
        	$request->only([
        		'default_vendor_category_id'
        	])
        );

        return to_json([
            'saved' => true
        ]);
    }
}
