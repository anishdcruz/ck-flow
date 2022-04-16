<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Item\Category;
use App\Item\Uom;

class ItemSettingsController extends Controller
{
    public function show()
    {
        $this->authorize('access', 'settings.items');
    	$form = [
    		'default_item_category_id' => null,
    		'default_item_category' => null,
    		'default_item_uom_id' => null,
    		'default_item_uom' => null
    	];

    	$id = settings()->get('default_item_category_id');

    	if($id) {
    		$c = Category::findOrFail($id);
    		$form['default_item_category'] = [
    			'name' => $c->name,
    			'id' => $c->id
    		];

    		$form['default_item_category_id'] = $id;
    	}

    	$id = settings()->get('default_item_uom_id');

    	if($id) {
    		$c = Uom::findOrFail($id);
    		$form['default_item_uom'] = [
    			'name' => $c->name,
    			'id' => $c->id
    		];

    		$form['default_item_uom_id'] = $id;
    	}



    	return to_json([
    		'form' => $form
    	]);
    }

    public function update(Request $request)
    {
        $this->authorize('access', 'settings.items');
        $request->validate([
        	'default_item_category_id' => 'required|integer',
        	'default_item_uom_id' => 'required|integer'
        ]);

        settings()->setMany(
        	$request->only([
	    		'default_item_category_id',
	    		'default_item_uom_id'
        	])
        );

        return to_json([
            'saved' => true
        ]);
    }
}
