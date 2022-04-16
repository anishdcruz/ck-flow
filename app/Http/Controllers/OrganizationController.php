<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Organization\Organization;
use App\Organization\Category;
use App\Services\ExportCSV;
use DB;

class OrganizationController extends Controller
{
	public function typeahead()
	{
	    $results = Organization::typeahead([
            'number', 'name'
        ], [
            'id', 'number', 'name'
        ]);

	    return to_json([
	        'results' => $results
	    ]);
	}

	public function search()
	{
	    return to_json([
	        'collection' => Organization::search()
	    ]);
	}

    public function export()
    {
        $this->authorize('access', 'organizations.export');
        return (new ExportCSV(Organization::with('category'), 'organizations'))
            ->download();
    }

    public function index()
    {
        $this->authorize('access', 'organizations.index');
        return to_json([
            'collection' => Organization::with(['category:id,name'])->filter()
        ]);
    }

    public function create()
    {
        $this->authorize('access', 'organizations.create');
        $form = [
            'name' => '',
            'organization_category_id' => '',
            'category' => null,
            'email' => '',
            'mobile' => '',
            'work' => '',
            'fax' => '',
            'website' => '',
            'primary_address' => '',
            'other_address' => '',
            'number' => __('lang.auto_generated'),
            'custom_fields' => custom_fields('organizations', "[]")
        ];

        $id = settings()->get('default_organization_category_id');

        if($id) {
            $c = Category::findOrFail($id);

            $form['category'] = [
                'name' => $c->name,
                'id' => $c->id
            ];

            $form['organization_category_id'] = $id;
        }

        return to_json([
            'form' => $form
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('access', 'organizations.create');
        $request->validate([
            'name' => 'required|string',
            'organization_category_id' => 'required|integer|exists:organization_categories,id',
            'email' => 'nullable|email',
            'mobile' => 'nullable|string',
            'work' => 'nullable|string',
            'fax' => 'nullable|string',
            'website' => 'nullable|string',
            'primary_address' => 'required|string',
            'other_address' => 'nullable|string',
            'custom_fields' => 'array'
        ]);

        $org = new Organization;
        $org->fill($request->all());
        $org->custom_values = json_encode(custom_values($request->custom_fields));

        $org = DB::transaction(function() use ($org) {
            $c = counter('organization');
            $org->number = $c->number;
            $org->save();
            $c->increment('value');

            return $org;
        });
        $org->save();

        return to_json([
            'saved' => true,
            'id' => $org->id
        ]);
    }

    public function show($id)
    {
        $this->authorize('access', 'organizations.show');
        $org = Organization::with([
        	'category:id,name'
        	])
        	->findOrFail($id);

        $org->custom_fields = custom_fields_preview('organizations', $org->custom_values);

        return to_json([
            'model' => $org
        ]);
    }

    public function edit($id)
    {
        $this->authorize('access', 'organizations.update');
        $org = Organization::with([
            'category:id,name'
            ])
            ->findOrFail($id);

        $org->custom_fields = custom_fields('organizations', $org->custom_values);

        return to_json([
            'form' => $org
        ]);
    }

    public function update($id, Request $request)
    {
        $this->authorize('access', 'organizations.update');
        $request->validate([
            'name' => 'required|string',
            'organization_category_id' => 'required|integer|exists:organization_categories,id',
            'email' => 'nullable|email',
            'mobile' => 'nullable|string',
            'work' => 'nullable|string',
            'fax' => 'nullable|string',
            'website' => 'nullable|string',
            'primary_address' => 'required|string',
            'other_address' => 'nullable|string',
            'custom_fields' => 'array'
        ]);

        $org = Organization::findOrFail($id);
        $org->fill($request->all());
        $org->custom_values = json_encode(custom_values($request->custom_fields));
        $org->save();

        return to_json([
            'saved' => true,
            'id' => $org->id
        ]);
    }

    public function destroy($id)
    {
        $this->authorize('access', 'organizations.delete');
        $model = Organization::findOrFail($id);

        if(DB::table('contacts')->where('organization_id', $model->id)->count()) {
            return delete_first(__('lang.cannot_delete'));
        }

        $model->delete();

        return to_json([
            'deleted' => true
        ]);
    }
}
