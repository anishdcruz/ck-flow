<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserMetric;
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

class UserMetricController extends Controller
{
	protected $types = [
        'contacts', 'organizations', 'items', 'leads',
        'opportunities', 'proposals', 'contracts',
        'projects', 'invoices', 'payments',
        'expenses', 'vendors', 'payment_requests'
    ];

    public function store(Request $request)
    {

    	$data = $request->validate([
    	    'card_label' => 'required|string',
    	    // 'sort_column' => 'sometimes|required',
    	    // 'sort_direction' => 'sometimes|required|in:asc,desc',
    	    'filter_match' => 'sometimes|required|in:and,or',
    	    'f' => 'sometimes|array',
    	    'resource' => 'required|in:'.implode(',', $this->types),
    	    'metric_type' => 'required|in:value,chart', // metric_type,
            'time_peroid' => 'required',
            'chart_type' => 'required',
            'group_by' => 'required',
            'color' => 'sometimes'
    	]);

        // $this->authorize('access', $request->resource.'.export');

        if($request->has('f')) {
            $params = [
                // 'sort_column' => $request->sort_column,
                // 'sort_direction' => $request->sort_direction,
                'filter_match' => $request->filter_match,
                'f' => $request->f
            ];
        } else {
            $params = [
                // 'sort_column' => $request->sort_column,
                // 'sort_direction' => $request->sort_direction,
                'filter_match' => $request->filter_match,
            ];
        }


    	$re = new UserMetric;
    	$re->fill($data);
    	$re->filter_match = $request->filter_match;
    	$re->metric_type = $request->metric_type;
    	$re->params = json_encode($params);
    	$re->resource = $request->resource;
    	switch ($request->resource) {
    		case 'contacts':
    			$re->model = Contact::class;
    			break;
            case 'organizations':
                $re->model = Organization::class;
                break;
            case 'items':
                $re->model = Item::class;
                break;
            case 'leads':
                $re->model = Lead::class;
                break;
            case 'opportunities':
                $re->model = Opportunity::class;
                break;
            case 'proposals':
                $re->model = Proposal::class;
                break;
            case 'contracts':
                $re->model = Contract::class;
                break;
            case 'projects':
                $re->model = Project::class;
                break;
            case 'payments':
                $re->model = Payment::class;
                break;
            case 'payment_requests':
                $re->model = PaymentRequest::class;
                break;
            case 'expenses':
                $re->model = Expense::class;
                break;
            case 'expenses':
                $re->model = Expense::class;
                break;
            case 'invoices':
                $re->model = Invoice::class;
                break;
            // todo more
    		default:
    			abort(404, 'Not Found!');
    			break;
    	}

    	auth()
    		->user()
    		->metrics()
    		->save($re);

    	return to_json([
    	    'saved' => true
    	]);
    }

    public function destroy($id)
    {
        $found = UserMetric::where('user_id', auth()->id())
            ->where('id', $id)
            ->firstOrFail();


        $found->delete();

        return to_json([
            'deleted' => true
        ]);
    }
}
