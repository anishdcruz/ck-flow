<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RecurringExport;
use App\Contact\Contact;
use App\Invoice\Invoice;
use App\Organization\Organization;
use App\Item\Item;
use App\Lead\Lead;
use App\Opportunity\Opportunity;
use App\Proposal\Proposal;
use App\Contract\Contract;
use App\Project\Project;
use App\Payment\Payment;
use App\Expense\Expense;
use App\Vendor\Vendor;
use App\Payment\Request as PaymentRequest;
use DB;

class RecurringExportController extends Controller
{
	protected $types = [
        'contacts', 'organizations', 'items', 'leads',
        'opportunities', 'proposals', 'contracts',
        'projects', 'invoices', 'payments',
        'expenses', 'vendors', 'payment_requests'
    ];

    public function index()
    {
        $this->authorize('access', 'recurring_exports.index');

        return to_json([
            'collection' => RecurringExport::filter()
        ]);
    }

    public function store(Request $request)
    {

    	$data = $request->validate([
    	    'email_to' => 'required|email',
    	    'frequency' => 'required|in:daily,weekly,monthly',
    	    'send_on' => 'required_unless:frequency,daily|integer|max:28',
    	    'send_at' => 'required|string',
    	    'name' => 'required|string',
    	    'sort_column' => 'sometimes|required',
    	    'sort_direction' => 'sometimes|required|in:asc,desc',
    	    'filter_match' => 'sometimes|required|in:and,or',
    	    'f' => 'sometimes|array',
    	    'resource' => 'required|in:'.implode(',', $this->types)
    	]);

        $this->authorize('access', $request->resource.'.export');

        if($request->has('f')) {
            $params = [
                'sort_column' => $request->sort_column,
                'sort_direction' => $request->sort_direction,
                'filter_match' => $request->filter_match,
                'f' => $request->f
            ];
        } else {
            $params = [
                'sort_column' => $request->sort_column,
                'sort_direction' => $request->sort_direction,
                'filter_match' => $request->filter_match,
            ];
        }

    	$re = new RecurringExport;
    	$re->fill($data);
    	$re->params = json_encode($params);
    	switch ($request->resource) {
    		case 'contacts':
    			$re->model = Contact::class;
    			$re->with = json_encode(['organization']);
    			break;
            case 'organizations':
                $re->model = Organization::class;
                $re->with = json_encode(['category']);
                break;
            case 'items':
                $re->model = Item::class;
                $re->with = json_encode(['category', 'uom']);
                break;
            case 'leads':
                $re->model = Lead::class;
                $re->with = json_encode(['status']);
                break;
            case 'opportunities':
                $re->model = Opportunity::class;
                $re->with = json_encode(['category', 'stage', 'contact', 'source']);
                break;
            case 'proposals':
                $re->model = Proposal::class;
                $re->with = json_encode(['contact', 'status', 'opportunity']);
                break;
            case 'contracts':
                $re->model = Contract::class;
                $re->with = json_encode(['contact', 'status', 'proposal', 'template', 'type']);
                break;
            case 'projects':
                $re->model = Project::class;
                $re->with = json_encode(['contact', 'stage', 'category', 'proposal']);
                break;
            case 'payments':
                $re->model = Payment::class;
                $re->with = json_encode(['contact', 'deposit', 'method']);
                break;
            case 'payment_requests':
                $re->model = PaymentRequest::class;
                $re->with = json_encode(['invoice', 'contact']);
                break;
            case 'expenses':
                $re->model = Expense::class;
                $re->with = json_encode(['vendor', 'category', 'project', 'opportunity']);
                break;
            case 'expenses':
                $re->model = Expense::class;
                $re->with = json_encode(['category']);
                break;
            case 'invoices':
                $re->model = Invoice::class;
                $re->with = json_encode(['contact', 'status', 'template', 'proposal', 'contract']);
                break;
            // todo more
    		default:
    			abort(404, 'Not Found!');
    			break;
    	}

    	$re->save();

    	return to_json([
    	    'saved' => true
    	]);
    }

    public function show($id)
    {
        $this->authorize('access', 'recurring_exports.show');
        $item = RecurringExport::findOrFail($id);

        return to_json([
            'model' => $item
        ]);
    }

    public function destroy($id)
    {
        $this->authorize('access', 'recurring_exports.delete');
        $model = RecurringExport::findOrFail($id);

        $model->delete();

        return to_json([
            'deleted' => true
        ]);
    }
}
