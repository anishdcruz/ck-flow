<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Proposal\Status;
use DB;

class ProposalStatusController extends Controller
{
    public function index()
    {
        $this->authorize('access', 'settings.proposals');
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
        $this->authorize('access', 'settings.proposals');
        $request->validate([
            'name' => 'required|unique:proposal_statuses,name',
            'color' => 'required|alpha_dash|unique:opportunity_stages,color',
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
        $this->authorize('access', 'settings.proposals');
        $request->validate([
            'name' => 'required|unique:proposal_statuses,name,'.$id.',id',
            'color' => 'required|alpha_dash|unique:proposal_statuses,color,'.$id.',id',
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
        $this->authorize('access', 'settings.proposals');
        $model = Status::findOrFail($id);

        if(DB::table('proposals')->where('status_id', $model->id)->count()) {
            return delete_first(__('lang.cannot_delete'));
        }

        if(settings()->check('proposal_status_on_create_id', $model->id)) {
            return delete_first(__('lang.cannot_delete'));
        }

        if(settings()->check('proposal_status_on_email_sent_id', $model->id)) {
            return delete_first(__('lang.cannot_delete'));
        }

        $model->delete();

        return to_json([
            'deleted' => true
        ]);
    }
}
