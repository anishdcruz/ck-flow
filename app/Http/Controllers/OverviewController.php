<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contact\Contact;
use App\Item\Item;
use App\Organization\Organization;
use App\Vendor\Vendor;
use App\Project\Project;
use App\Payment\Request as PaymentRequest;
use App\Invoice\Invoice;
use App\UserMetric;

class OverviewController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $p = UserMetric::where('user_id', auth()->id())->get();
        $metrics = $p->map(function($item) use ($user) {
            $resource = $item->resource;
            if(in_array("{$resource}.index", $user->permissions())) {
                $model = $item->model::getModel();
                $values = $model::metrics(
                    json_decode($item->params, true),
                    $item->metric_type,
                    $item->group_by,
                    $item->time_peroid
                );
                return [
                    'card_label' => $item->card_label,
                    'values' => $values,
                    'id' => $item->id,
                    'type' => $item->metric_type,
                    'chart_type' => $item->chart_type,
                    'color' => $item->color
                ];
            }
            return [
                'card_label' => $item->card_label.' - '.__('lang.un_auth'),
                'value' => 0,
                'id' => $item->id,
                'type' => $item->metric_type
            ];
        });

    	return to_json([
    		'metrics' => $metrics
    	]);
    }
}
