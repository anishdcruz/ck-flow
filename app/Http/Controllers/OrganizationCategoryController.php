<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Organization\Category;
use DB;

class OrganizationCategoryController extends Controller
{
    public function typeahead()
    {
        $results = Category::typeahead(['number', 'name']);

        return to_json([
            'results' => $results
        ]);
    }

    public function search()
    {
        return to_json([
            'collection' => Category::search()
        ]);
    }

    public function index()
    {
        $this->authorize('access', 'settings.organizations');
        return to_json([
            'collection' => Category::filter()
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('access', 'settings.organizations');
        $request->validate([
            'name' => 'required|unique:organization_categories,name',
        ]);

        $item = new Category;
        $item->name = $request->name;
        $item->save();

        return to_json([
            'saved' => true,
            'item' => $item
        ]);
    }

    public function update($id, Request $request)
    {
        $this->authorize('access', 'settings.organizations');
        $request->validate([
            'name' => 'required|unique:organization_categories,name,'.$id.',id',
        ]);

        $item = Category::findOrFail($id);
        $item->name = $request->name;
        $item->save();

        return to_json([
            'saved' => true,
            'item' => $item
        ]);
    }

    public function destroy($id)
    {
        $this->authorize('access', 'settings.organizations');
        $model = Category::findOrFail($id);

        if(DB::table('organizations')->where('organization_category_id', $model->id)->count()) {
            return delete_first(__('lang.cannot_delete'));
        }

        // check settings
        if(settings()->check('default_organization_category_id', $model->id)) {
            return delete_first(__('lang.cannot_delete'));
        }

        $model->delete();

        return to_json([
            'deleted' => true
        ]);
    }
}
