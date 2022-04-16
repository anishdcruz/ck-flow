<?php

use Illuminate\Database\Seeder;
use App\UserMetric;

class UserMetricsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $metrics = [
        	[
        		'user_id' => 1,
        		'filter_match' => 'and',
        		'resource' => 'contacts',
        		'model' => 'App\Contact\Contact',
        		'card_label' => 'All Contacts',
        		'metric_type' => 'value',
        		'params' => '{"filter_match":"and"}',
        		'time_peroid' => null,
        		'chart_type' => null,
        		'group_by' => null
        	],
        	[
        		'user_id' => 1,
        		'filter_match' => 'and',
        		'resource' => 'items',
        		'model' => 'App\Item\Item',
        		'card_label' => 'All Items',
        		'metric_type' => 'value',
        		'params' => '{"filter_match":"and"}',
        		'time_peroid' => null,
        		'chart_type' => null,
        		'group_by' => null
        	],
        	[
        		'user_id' => 1,
        		'filter_match' => 'and',
        		'resource' => 'leads',
        		'model' => 'App\Lead\Lead',
        		'card_label' => 'New Leads',
        		'metric_type' => 'value',
        		'params' => '{"filter_match":"and","f":[{"column":"status.name","operator":"includes","query_2":null,"query_1":"New"}]}',
        		'time_peroid' => null,
        		'chart_type' => null,
        		'group_by' => null
        	],
        	[
        		'user_id' => 1,
        		'filter_match' => 'and',
        		'resource' => 'opportunities',
        		'model' => 'App\Opportunity\Opportunity',
        		'card_label' => 'Opportunities This Month',
        		'metric_type' => 'chart',
        		'params' => '{"filter_match":"and"}',
        		'time_peroid' => 'this_month',
        		'chart_type' => 'area',
        		'group_by' => 'start_date',
                'color' => '#6be6c1'
        	],
        	[
        		'user_id' => 1,
        		'filter_match' => 'and',
        		'resource' => 'proposals',
        		'model' => 'App\Proposal\Proposal',
        		'card_label' => 'Proposals This Month',
        		'metric_type' => 'chart',
        		'params' => '{"filter_match":"and"}',
        		'time_peroid' => 'this_month',
        		'chart_type' => 'area',
        		'group_by' => 'issue_date',
                'color' => '#626c91'
        	],
        	// [
        	// 	'user_id' => 1,
        	// 	'filter_match' => 'and',
        	// 	'resource' => 'proposals',
        	// 	'model' => 'App\Proposal\Proposal',
        	// 	'card_label' => 'Proposals This Month',
        	// 	'metric_type' => 'chart',
        	// 	'params' => '{"filter_match":"and"}',
        	// 	'time_peroid' => 'this_month',
        	// 	'chart_type' => 'area',
        	// 	'group_by' => 'issue_date'
        	// ],
        	[
        		'user_id' => 1,
        		'filter_match' => 'and',
        		'resource' => 'projects',
        		'model' => 'App\Project\Project',
        		'card_label' => 'Projects This Month',
        		'metric_type' => 'chart',
        		'params' => '{"filter_match":"and"}',
        		'time_peroid' => 'this_month',
        		'chart_type' => 'area',
        		'group_by' => 'start_date',
                'color' => '#a0a7e6'
        	],
        	[
        		'user_id' => 1,
        		'filter_match' => 'and',
        		'resource' => 'invoices',
        		'model' => 'App\Invoice\Invoice',
        		'card_label' => 'Payment Requested Invoices',
        		'metric_type' => 'value',
        		'params' => '{"filter_match":"and","f":[{"column":"status.name","operator":"includes","query_2":null,"query_1":"Payment Requested"}]}',
        		'time_peroid' => null,
        		'chart_type' => null,
        		'group_by' => null
        	],
        	[
        		'user_id' => 1,
        		'filter_match' => 'and',
        		'resource' => 'invoices',
        		'model' => 'App\Invoice\Invoice',
        		'card_label' => 'Paid Invoices This Month',
        		'metric_type' => 'chart',
        		'params' => '{"filter_match":"and","f":[{"column":"status.name","operator":"includes","query_2":null,"query_1":"Paid"}]}',
        		'time_peroid' => 'this_month',
        		'chart_type' => 'area',
        		'group_by' => 'created_at',
                'color' => '#c4ebad'
        	],
        	[
        		'user_id' => 1,
        		'filter_match' => 'and',
        		'resource' => 'payments',
        		'model' => 'App\Payment\Payment',
        		'card_label' => 'Payments This Month',
        		'metric_type' => 'chart',
        		'params' => '{"filter_match":"and"}',
        		'time_peroid' => 'this_month',
        		'chart_type' => 'area',
        		'group_by' => 'payment_date',
                'color' => '#96dee8'
        	],
        	[
        		'user_id' => 1,
        		'filter_match' => 'and',
        		'resource' => 'expenses',
        		'model' => 'App\Expense\Expense',
        		'card_label' => 'Expenses This Month',
        		'metric_type' => 'chart',
        		'params' => '{"filter_match":"and"}',
        		'time_peroid' => 'this_month',
        		'chart_type' => 'area',
        		'group_by' => 'created_at'
        	],
        	[
        		'user_id' => 1,
        		'filter_match' => 'and',
        		'resource' => 'projects',
        		'model' => 'App\Project\Project',
        		'card_label' => 'Projects on Hold',
        		'metric_type' => 'value',
        		'params' => '{"filter_match":"and","f":[{"column":"stage.name","operator":"includes","query_2":null,"query_1":"On Hold"}]}',
        		'time_peroid' => null,
        		'chart_type' => null,
        		'group_by' => null
        	],
        	[
        		'user_id' => 1,
        		'filter_match' => 'and',
        		'resource' => 'contracts',
        		'model' => 'App\Contract\Contract',
        		'card_label' => 'Contracts Expiring Next Month',
        		'metric_type' => 'value',
        		'params' => '{"filter_match":"and","f":[{"column":"expiry_date","operator":"in_the_peroid","query_2":null,"query_1":"next_month"}]}',
        		'time_peroid' => null,
        		'chart_type' => null,
        		'group_by' => null
        	],
        ];

        foreach($metrics as $metric) {
        	UserMetric::create($metric);
        }
    }
}
