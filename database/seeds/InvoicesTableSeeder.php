<?php

use Illuminate\Database\Seeder;
use App\Invoice\Invoice;
use App\Invoice\Status;
use App\Invoice\Line;
use Faker\Factory;

class InvoicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        Invoice::truncate();
        Status::truncate();

        // foreach([
        // 	['name' => 'Draft', 'color' => 'grey', 'locked' => 1],
        // 	['name' => 'Sent', 'color' => 'light_green'],
        //     ['name' => 'Payment Requested', 'color' => 'pink', 'locked' => 1],
        // 	['name' => 'Paid', 'color' => 'blue', 'locked' => 1],
        // 	['name' => 'Partially Paid', 'color' => 'light_blue', 'locked' => 1],
        // 	['name' => 'Void', 'color' => 'red']
        // ] as $c) {
        // 	Status::create($c);
        // }

        foreach(range(1, 100) as $i) {
            $total = mt_rand(100, 990);
        	$inv = Invoice::create([
                'number' => 'INV'.$i,
        		'contact_id' => $i,
        		'proposal_id' => $i,
        		'contract_id' => null,
        		'template_id' => 3,
        		'issue_date' => '2018-07-'.mt_rand(1, 28),
        		'due_date' => '2018-08-'.mt_rand(1, 28),
        		'status_id' => mt_rand(1, 4),
        		'reference' => null,
        		'sub_total' => $total,
        		'grand_total' => $total,
        		'amount_paid' => 0,
        		'custom_values' => '{"cover.uf.default.item_table":{"thead":[{"title":"Item Code","name":"item_code","width":4,"align":"left","type":"item_lookup","default":null,"options":[null],"format":"Y-m-d","formula":"sum","columns":[],"val":"code","field_map":[{"item":"description","col":"description"},{"item":"unit_price","col":"unit_price"},{"item":"uom.name","col":"u_o_m"}]},{"title":"Description","name":"description","width":8,"align":"left","type":"textarea","default":null,"options":[null],"format":"Y-m-d","formula":"sum","columns":[],"val":"code","field_map":[]},{"title":"U.O.M","name":"u_o_m","width":2,"align":"left","type":"text","default":null,"options":[null],"format":"Y-m-d","formula":"sum","columns":[],"val":"code","field_map":[]},{"title":"Unit Price","name":"unit_price","width":4,"align":"left","type":"currency","default":"0","options":[null],"format":"Y-m-d","formula":"sum","columns":[],"val":"code","field_map":[]},{"title":"Qty","name":"qty","width":2,"align":"left","type":"number","default":"1","options":[null],"format":"Y-m-d","formula":"sum","columns":[],"val":"code","field_map":[]},{"title":"Total","name":"total","width":4,"align":"left","type":"computed_currency","default":"0","options":[null],"format":"Y-m-d","formula":"product","columns":["unit_price","qty"],"val":"code","field_map":[]}],"tbody":[{"item_code":"IP-6092364","description":"Item Harum nihil velit dolor repellendus architecto aut. Maiores maxime voluptatem nesciunt et.","u_o_m":"ea","unit_price":2998,"qty":"1","total":2998}],"tfoot":[{"title":"Sub Total","name":"sub_total","align":"right","type":"aggregate_currency","model":2998,"percent_model":0,"sub_total":null,"tbody_column":"total","formula":"sum","columns":[]},{"title":"Discount","name":"discount","align":"right","type":"input_currency","model":"0","percent_model":0,"sub_total":null,"tbody_column":null,"formula":"sum","columns":[]},{"title":"Grand Total","name":"grand_total","align":"right","type":"computed_currency","model":2998,"percent_model":0,"sub_total":null,"tbody_column":null,"formula":"difference","columns":["discount","sub_total"]}],"currency":{"code":"USD","precision":"2","separator":".","placement":"before"},"colspan":{"empty":"3","title":"2","value":1},"class_name":"items"},"cover.uf.terms_and_conditions.due_days":"30"}',
                'custom_values_2' => '[]'
        	]);
        }
    }
}
