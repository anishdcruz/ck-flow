<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Item\Uom;
use DB;

class UomController extends Controller
{
	public function typeahead()
	{
	    $results = Uom::typeahead(['name']);

	    return to_json([
	        'results' => $results
	    ]);
	}

	public function search()
	{
	    return to_json([
	        'collection' => Uom::search()
	    ]);
	}

	public function index()
	{
		$this->authorize('access', 'settings.items');
	    return to_json([
	        'collection' => Uom::filter()
	    ]);
	}

	public function store(Request $request)
	{
		$this->authorize('access', 'settings.items');
	    $request->validate([
	        'name' => 'required|unique:item_uoms,name',
	    ]);

	    $item = new Uom;
	    $item->name = $request->name;
	    $item->save();

	    return to_json([
	        'saved' => true,
	        'item' => $item
	    ]);
	}

	public function update($id, Request $request)
	{
		$this->authorize('access', 'settings.items');
	    $request->validate([
	        'name' => 'required|unique:item_uoms,name,'.$id.',id',
	    ]);

	    $item = Uom::findOrFail($id);
	    $item->name = $request->name;
	    $item->save();

	    return to_json([
	        'saved' => true,
	        'item' => $item
	    ]);
	}

	public function destroy($id)
    {
    	$this->authorize('access', 'settings.items');
        $model = Uom::findOrFail($id);

        if(DB::table('items')->where('uom_id', $model->id)->count()) {
            return delete_first(__('lang.cannot_delete'));
        }

        // check settings
        if(settings()->check('default_item_uom_id', $model->id)) {
            return delete_first(__('lang.cannot_delete'));
        }

        $model->delete();

        return to_json([
            'deleted' => true
        ]);
    }
}
