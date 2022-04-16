<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contract\Type;
use DB;

class ContractTypeController extends Controller
{
    public function search()
	{
	    return to_json([
	        'collection' => Type::search()
	    ]);
	}

	public function index()
	{
		$this->authorize('access', 'settings.contracts');
	    return to_json([
	        'collection' => Type::filter()
	    ]);
	}

	public function store(Request $request)
	{
		$this->authorize('access', 'settings.contracts');
	    $request->validate([
	        'name' => 'required|unique:contract_types,name',
	    ]);

	    $item = new Type;
	    $item->name = $request->name;
	    $item->save();

	    return to_json([
	        'saved' => true,
	        'item' => $item
	    ]);
	}

	public function update($id, Request $request)
	{
		$this->authorize('access', 'settings.contracts');
	    $request->validate([
	        'name' => 'required|unique:contract_types,name,'.$id.',id',
	    ]);

	    $item = Type::findOrFail($id);
	    $item->name = $request->name;
	    $item->save();

	    return to_json([
	        'saved' => true,
	        'item' => $item
	    ]);
	}

	public function destroy($id)
    {
        $this->authorize('access', 'settings.contracts');
        $model = Type::findOrFail($id);

        if(DB::table('contracts')->where('type_id', $model->id)->count()) {
            return delete_first(__('lang.cannot_delete'));
        }

        if(settings()->check('default_contract_type_id', $model->id)) {
            return delete_first(__('lang.cannot_delete'));
        }

        $model->delete();

        return to_json([
            'deleted' => true
        ]);
    }
}
