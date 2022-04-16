<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Item\Item;
use App\Item\Category;
use App\Item\Uom;
use App\Services\ExportCSV;
use DB;

class ItemController extends Controller
{
	public function typeahead()
	{
        $columns = ['code', 'description'];
	    $results = Item::with(['uom:id,name'])
            ->when(request('query'), function($query) use ($columns) {
                foreach($columns as $column) {
                    $query->orWhere($column, 'like', '%'.request('query').'%');
                }
            })
            ->limit(10)
            ->get()
            ->transform(function($item) {
                $item->custom_fields = custom_fields_value('items', $item->custom_values);
                return $item;
            });

	    return to_json([
	        'results' => $results
	    ]);
	}

	public function search()
	{
	    return to_json([
	        'collection' => Item::search()
	    ]);
	}

    public function export()
    {
        $this->authorize('access', 'items.export');
        return (new ExportCSV(Item::with('category', 'uom'), 'items'))
            ->download();
    }

    public function index()
    {
        $this->authorize('access', 'items.index');
        return to_json([
            'collection' => Item::with(['category:id,name', 'uom:id,name'])->filter()
        ]);
    }

    public function create()
    {
        $this->authorize('access', 'items.create');
        $form = [
            'category_id' => '',
            'category' => null,
            'uom_id' => '',
            'uom' => null,
            'unit_price' => 0,
            'description' => '',
            'code' => __('lang.auto_generated'),
            'custom_fields' => custom_fields('items', "[]")
        ];

        $id = settings()->get('default_item_category_id');

        if($id) {
            $c = Category::findOrFail($id);

            $form['category'] = [
                'name' => $c->name,
                'id' => $c->id
            ];

            $form['category_id'] = $id;
        }

        $id = settings()->get('default_item_uom_id');

        if($id) {
            $c = Uom::findOrFail($id);

            $form['uom'] = [
                'name' => $c->name,
                'id' => $c->id
            ];

            $form['uom_id'] = $id;
        }

        return to_json([
            'form' => $form
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('access', 'items.create');
        $request->validate([
            'category_id' => 'required|integer|exists:item_categories,id',
            'uom_id' => 'required|integer|exists:item_uoms,id',
            'unit_price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'custom_fields' => 'array'
        ]);

        $item = new Item;
        $item->fill($request->all());
        $item->custom_values = json_encode(custom_values($request->custom_fields));

        $item = DB::transaction(function() use ($item) {
            $c = counter('item');
            $item->code = $c->number;
            $item->save();
            $c->increment('value');

            return $item;
        });
        $item->save();

        return to_json([
            'saved' => true,
            'id' => $item->id
        ]);
    }

    public function show($id)
    {
        $this->authorize('access', 'items.show');
        $item = Item::with([
        	'category:id,name', 'uom:id,name'
        	])
        	->findOrFail($id);

        $item->custom_fields = custom_fields_preview('items', $item->custom_values);

        return to_json([
            'model' => $item
        ]);
    }

    public function edit($id)
    {
        $this->authorize('access', 'items.update');
        $item = Item::with([
            'category:id,name', 'uom:id,name'
            ])
            ->findOrFail($id);

        $item->custom_fields = custom_fields('items', $item->custom_values);

        return to_json([
            'form' => $item
        ]);
    }

    public function update($id, Request $request)
    {
        $this->authorize('access', 'items.update');
        $request->validate([
            'category_id' => 'required|integer|exists:item_categories,id',
            'uom_id' => 'required|integer|exists:item_uoms,id',
            'unit_price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'custom_fields' => 'array'
        ]);

        $item = Item::findOrFail($id);
        $item->fill($request->all());
        $item->custom_values = json_encode(custom_values($request->custom_fields));
        $item->save();

        return to_json([
            'saved' => true,
            'id' => $item->id
        ]);
    }

    public function destroy($id)
    {
        $this->authorize('access', 'items.delete');
        $model = Item::findOrFail($id);

        $model->delete();

        return to_json([
            'deleted' => true
        ]);
    }
}
