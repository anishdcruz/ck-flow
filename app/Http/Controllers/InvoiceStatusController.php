<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Invoice\Status;
use DB;

class InvoiceStatusController extends Controller
{
    public function index()
    {
        $this->authorize('access', 'settings.invoices');
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
        $this->authorize('access', 'settings.invoices');
        $request->validate([
            'name' => 'required|unique:invoice_statuses,name',
            'color' => 'required|alpha_dash|unique:invoice_statuses,color',
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
        $this->authorize('access', 'settings.invoices');
        $request->validate([
            'name' => 'required|unique:invoice_statuses,name,'.$id.',id',
            'color' => 'required|alpha_dash|unique:invoice_statuses,color,'.$id.',id',
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
        $this->authorize('access', 'settings.invoices');
        $model = Status::findOrFail($id);

        if(DB::table('invoices')->where('status_id', $model->id)->count()) {
            return delete_first(__('lang.cannot_delete'));
        }

        // check settings
        if(settings()->check('invoice_status_on_create_id', $model->id)) {
            return delete_first(__('lang.cannot_delete'));
        }

        if(settings()->check('invoice_status_on_email_sent_id', $model->id)) {
            return delete_first(__('lang.cannot_delete'));
        }

        if(settings()->check('invoice_status_on_payment_request_id', $model->id)) {
            return delete_first(__('lang.cannot_delete'));
        }

        if(settings()->check('invoice_status_on_partial_payment_id', $model->id)) {
            return delete_first(__('lang.cannot_delete'));
        }

        if(settings()->check('invoice_status_on_partial_payment_id', $model->id)) {
            return delete_first(__('lang.cannot_delete'));
        }

        if(settings()->check('invoice_status_on_complete_payment_id', $model->id)) {
            return delete_first(__('lang.cannot_delete'));
        }

        // receive status
        $ids = json_decode(settings()->get('receive_payment_on_status_ids'), true);
        if(in_array($model->id, $ids)) {
            return delete_first(__('lang.cannot_delete'));
        }
        $model->delete();

        return to_json([
            'deleted' => true
        ]);
    }
}
