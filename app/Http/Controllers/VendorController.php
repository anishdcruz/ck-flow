<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Vendor\Vendor;
use App\Vendor\Category;
use App\Invoice\Invoice;
use DB;
use App\Services\ExportCSV;

class VendorController extends Controller
{
	public function typeahead()
	{
        $collection = Vendor::typeahead([
            'number', 'name'
        ], [
            'id', 'number', 'name'
        ]);

	    return to_json([
	        'results' => $collection
	    ]);
	}

	public function search()
	{
        $collection = Vendor::search();



	    return to_json([
	        'collection' => $collection
	    ]);
	}

    public function index()
    {
        $this->authorize('access', 'vendors.index');
        $collection = Vendor::with(['category:id,name'])->filter();
        return to_json([
            'collection' => $collection
        ]);
    }
    public function export()
    {
        $this->authorize('access', 'vendors.export');
        return (new ExportCSV(
            Vendor::with([
                'category'
            ]), 'vendors')
        )->download();
    }

    public function create()
    {
        $this->authorize('access', 'vendors.create');
        $form = [
            'name' => '',
            'email' => '',
            'mobile' => '',
            'category_id' => null,
            'category' => null,
            'work' => '',
            'fax' => '',
            'website' => '',
            'primary_address' => '',
            'other_address' => '',
            'number' => __('lang.auto_generated'),
            'custom_fields' => custom_fields('vendors', "[]")
        ];

        $id = settings()->get('default_vendor_category_id');

        if($id) {
            $c = Category::findOrFail($id);
            $form['category'] = [
                'name' => $c->name,
                'id' => $c->id
            ];

            $form['category_id'] = $id;
        }

        return to_json([
            'form' => $form
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('access', 'vendors.create');
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|string|unique:vendors,email,',
            'mobile' => 'nullable|string',
            'work' => 'nullable|string',
            'fax' => 'nullable|string',
            'website' => 'nullable|string',
            'primary_address' => 'required|string',
            'other_address' => 'nullable|string',
            'custom_fields' => 'array'
        ]);

        $vendor = new Vendor;
        $vendor->fill($request->all());
        $vendor->custom_values = json_encode(custom_values($request->custom_fields));


        $vendor = DB::transaction(function() use ($vendor) {
            $c = counter('vendor');
            $vendor->number = $c->number;
            $vendor->save();
            $c->increment('value');

            return $vendor;
        });

        return to_json([
            'saved' => true,
            'id' => $vendor->id
        ]);
    }

    public function show($id)
    {
        $this->authorize('access', 'vendors.show');
        $vendor = Vendor::with([
        	'category:id,name'
        	])
        	->findOrFail($id);

        $vendor->custom_fields = custom_fields_preview('vendors', $vendor->custom_values);

        return to_json([
            'model' => $vendor
        ]);
    }

    public function edit($id)
    {
        $this->authorize('access', 'vendors.update');
        $vendor = Vendor::with([
            'category:id,name'
            ])
            ->findOrFail($id);

        $vendor->custom_fields = custom_fields('vendors', $vendor->custom_values);

        return to_json([
            'form' => $vendor
        ]);
    }

    public function update($id, Request $request)
    {
        $this->authorize('access', 'vendors.update');
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|string|unique:vendors,email,'.$id.',id',
            'mobile' => 'nullable|string',
            'work' => 'nullable|string',
            'fax' => 'nullable|string',
            'website' => 'nullable|string',
            'primary_address' => 'required|string',
            'other_address' => 'nullable|string',
            'custom_fields' => 'array'
        ]);

        $vendor = Vendor::findOrFail($id);
        $vendor->fill($request->all());
        $vendor->custom_values = json_encode(custom_values($request->custom_fields));

        $vendor->save();

        return to_json([
            'saved' => true,
            'id' => $vendor->id
        ]);
    }

    public function destroy($id)
    {
        $this->authorize('access', 'vendors.delete');
        $model = Vendor::findOrFail($id);

        if(DB::table('expenses')->where('vendor_id', $model->id)->count()) {
            return delete_first(__('lang.cannot_delete'));
        }
        $model->delete();

        return to_json([
            'deleted' => true
        ]);
    }
}
