<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Payment\Request as PaymentRequest;
use App\Services\ExportCSV;
use DB;

class PaymentRequestCrudController extends Controller
{
	public function search()
	{
	    return to_json([
	        'collection' => PaymentRequest::search()
	    ]);
	}

	public function export()
    {
        $this->authorize('access', 'payment_requests.export');
        return (new ExportCSV(PaymentRequest::with('invoice', 'contact'), 'payment_requests'))
            ->download();
    }

    public function index()
    {
        $this->authorize('access', 'payment_requests.index');

        return to_json([
            'collection' => PaymentRequest::with('invoice:id,number')->filter()
        ]);
    }

    public function show($id)
    {
        $this->authorize('access', 'payment_requests.show');
        $item = PaymentRequest::with([
        	'invoice:id,number', 'contact:id,name'
        	])
        	->findOrFail($id);

        return to_json([
            'model' => $item
        ]);
    }

    public function destroy($id)
    {
        $this->authorize('access', 'payment_requests.delete');
        $model = PaymentRequest::findOrFail($id);

        $model->delete();

        return to_json([
            'deleted' => true
        ]);
    }
}
