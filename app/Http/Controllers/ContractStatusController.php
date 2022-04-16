<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contract\Status;
use DB;

class ContractStatusController extends Controller
{
    public function index()
    {
        $this->authorize('access', 'settings.contracts');
        return to_json([
            'collection' => Status::filter()
        ]);
    }

    public function search()
    {
        return to_json([
            'collection' => Status::search()
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('access', 'settings.contracts');
        $request->validate([
            'name' => 'required|unique:contract_statuses,name',
            'color' => 'required|alpha_dash|unique:contract_statuses,color',
            'locked' => 'required|boolean'
        ]);

        $item = new Status;
        $item->name = $request->name;
        $item->color = $request->color;
        $item->locked = $request->locked;
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
            'name' => 'required|unique:contract_statuses,name,'.$id.',id',
            'color' => 'required|alpha_dash|unique:contract_statuses,color,'.$id.',id',
            'locked' => 'required|boolean'
        ]);

        $item = Status::findOrFail($id);
        $item->name = $request->name;
        $item->color = $request->color;
        $item->locked = $request->locked;
        $item->save();

        return to_json([
            'saved' => true,
            'item' => $item
        ]);
    }

    public function destroy($id)
    {
        $this->authorize('access', 'settings.contracts');
        $model = Status::findOrFail($id);

        if(DB::table('contracts')->where('status_id', $model->id)->count()) {
            return delete_first(__('lang.cannot_delete'));
        }

        // check settings
        if(settings()->check('contract_status_on_email_sent_id', $model->id)) {
            return delete_first(__('lang.cannot_delete'));
        }

        if(settings()->check('contract_status_on_create_id', $model->id)) {
            return delete_first(__('lang.cannot_delete'));
        }

        $model->delete();

        return to_json([
            'deleted' => true
        ]);
    }
}
