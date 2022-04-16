<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CustomField;

class CustomFieldController extends Controller
{
    protected $types = [
        'contacts', 'organizations', 'items', 'leads',
        'opportunities', 'proposals', 'contracts',
        'projects', 'invoices', 'payments',
        'expenses', 'vendors', 'globals'
    ];

    public function show(Request $request)
    {
    	$request->validate([
    		'type' => 'required|in:'.implode(',', $this->types)
    	]);

        $this->authorize('access', 'settings.'.$request->type);
    	$cf = CustomField::where('name', $request->type)
    		->firstOrFail();

    	return to_json([
    		'form' => $cf
    	]);
    }

    public function update(Request $request)
    {
        $request->validate([
        	'type' => 'required|in:'.implode(',', $this->types),
            'fields' => 'array'
        ]);


        $this->authorize('access', 'settings.'.$request->type);
        $item = CustomField::where('name', $request->type)
    		->firstOrFail();
        $item->fields = json_encode($request->fields);
        $item->save();

        return to_json([
            'saved' => true,
            'id' => $item->id
        ]);
    }
}
