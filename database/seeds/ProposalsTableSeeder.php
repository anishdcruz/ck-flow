<?php

use Illuminate\Database\Seeder;
use App\Proposal\Proposal;
use App\Proposal\Status;
use Faker\Factory;

class ProposalsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        Proposal::truncate();
        Status::truncate();

        // foreach([
        // 	['name' => 'Draft', 'color' => 'grey', 'locked' => 1],
        // 	['name' => 'Sent', 'color' => 'light_green'],
        // 	['name' => 'Accepted', 'color' => 'blue'],
        // 	['name' => 'Declined', 'color' => 'red']
        // ] as $c) {
        // 	Status::create($c);
        // }

        foreach(range(1, 100) as $i) {
        	if(mt_rand(0, 1)) {
                Proposal::create([
                'number' => 'PR'.$i,
                'contact_id' => $i,
                'opportunity_id' => $i,
                'template_id' => 6,
                'issue_date' => '2018-07-'.mt_rand(1, 28),
                'expiry_date' => '2018-08-'.mt_rand(1, 28),
                'status_id' => mt_rand(1, 4),
                'custom_values' => '{"main.uf.default.project_overview":"Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.\n\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\nquis nostrud exercitation ullamco.","main.uf.default.about_me":"Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.\n\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.\n\n\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\ntempor incididunt ut labore et dolore magna aliqua.","fees.uf.default.services":{"thead":[{"title":"Description","name":"description","width":14,"align":"left","type":"textarea","default":null,"options":[null],"format":"Y-m-d","formula":"sum","columns":[],"val":"code","field_map":[]},{"title":"U.O.M","name":"u_o_m","width":4,"align":"center","type":"text","default":"Hour","options":[null],"format":"Y-m-d","formula":"sum","columns":[],"val":"code","field_map":[]},{"title":"Rate","name":"rate","width":6,"align":"right","type":"currency","default":"0","options":[null],"format":"Y-m-d","formula":"sum","columns":[],"val":"code","field_map":[]}],"tbody":[{"description":"Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\ntempor incididunt ut labore et dolore magna aliqua.","u_o_m":"Hour","rate":"120"},{"description":"Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\ntempor incididunt ut labore et dolore magna aliqua.Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\ntempor incididunt ut labore et dolore magna aliqua.","u_o_m":"Hour","rate":"50"},{"description":"Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\ntempor incididunt ut labore et dolore magna aliqua.Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\ntempor incididunt ut labore et dolore magna aliqua.Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\ntempor incididunt ut labore et dolore magna aliqua.Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\ntempor incididunt ut labore et dolore magna aliqua.","u_o_m":"Hour","rate":"780"},{"description":"Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\ntempor incididunt ut labore et dolore magna aliqua.Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\ntempor incididunt ut labore et dolore magna aliqua.Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\ntempor incididunt ut labore et dolore magna aliqua.Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\ntempor incididunt ut labore et dolore magna aliqua.Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\ntempor incididunt ut labore et dolore magna aliqua.","u_o_m":"Hour","rate":"4658"},{"description":"Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\ntempor incididunt ut labore et dolore magna aliqua.Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\ntempor incididunt ut labore et dolore magna aliqua.Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\ntempor incididunt ut labore et dolore magna aliqua.","u_o_m":"Hour","rate":"876"}],"tfoot":[{"title":"Total","name":"total","align":"right","type":"aggregate_currency","model":6484,"percent_model":0,"sub_total":null,"tbody_column":"rate","formula":"sum","columns":[]}],"currency":{"code":"USD","precision":"2","separator":".","thousands":",","placement":"before"},"colspan":{"empty":1,"title":1,"value":1},"class_name":"services-table"},"terms.uf.default.next_step":"Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.\n\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.","terms.uf.default.terms_and_conditions":"Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.\n\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.\n\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.","terms.uf.default.signature_by":"John Doe"}',
                'custom_values_2' => '[]'
            ]);
            } else {
                Proposal::create([
                'number' => 'PR'.$i,
                'contact_id' => $i,
                'opportunity_id' => $i,
                'template_id' => 5,
                'issue_date' => '2018-07-'.mt_rand(1, 28),
                'expiry_date' => '2018-08-'.mt_rand(1, 28),
                'status_id' => mt_rand(1, 4),
                'custom_values' => '{"cover.uf.default.item_table":{"thead":[{"title":"Item Code","name":"item_code","width":4,"align":"left","type":"item_lookup","default":null,"options":[null],"format":"Y-m-d","formula":"sum","columns":[],"val":"code","field_map":[{"item":"description","col":"description"},{"item":"unit_price","col":"unit_price"},{"item":"custom_fields.photo","col":"photo"}]},{"title":"Description","name":"description","width":7,"align":"left","type":"textarea","default":null,"options":[null],"format":"Y-m-d","formula":"sum","columns":[],"val":"code","field_map":[]},{"title":"Photo","name":"photo","width":3,"align":"center","type":"image","default":null,"options":[null],"format":"Y-m-d","formula":"sum","columns":[],"val":"code","field_map":[]},{"title":"Unit Price","name":"unit_price","width":4,"align":"left","type":"currency","default":"0","options":[null],"format":"Y-m-d","formula":"sum","columns":[],"val":"code","field_map":[]},{"title":"Qty","name":"qty","width":2,"align":"left","type":"number","default":"1","options":[null],"format":"Y-m-d","formula":"sum","columns":[],"val":"code","field_map":[]},{"title":"Total","name":"total","width":4,"align":"left","type":"computed_currency","default":"0","options":[null],"format":"Y-m-d","formula":"product","columns":["unit_price","qty"],"val":"code","field_map":[]}],"tbody":[{"item_code":"IP-11885735","description":"Item Culpa quam quod ut maiores. Perferendis harum rerum rerum veritatis sit. Dolores iusto quos non reprehenderit ea ut et.","photo":"\/images\/tr6Mv9ohkvd9WnlSEkTqL79zHqhLuREH.png","unit_price":5953,"qty":"1","total":5953},{"item_code":"IP-1267195","description":"Item Recusandae ut fugit non veniam ut aperiam. Quia doloribus iure eum nulla quia et. Consequuntur voluptatum iure voluptatem est ipsam voluptatum dolores.","photo":"\/images\/tr6Mv9ohkvd9WnlSEkTqL79zHqhLuREH.png","unit_price":7549,"qty":"1","total":7549}],"tfoot":[{"title":"Sub Total","name":"sub_total","align":"right","type":"aggregate_currency","model":13502,"percent_model":0,"sub_total":null,"tbody_column":"total","formula":"sum","columns":[]},{"title":"Discount","name":"discount","align":"right","type":"input_currency","model":"0","percent_model":0,"sub_total":null,"tbody_column":null,"formula":"sum","columns":[]},{"title":"Grand Total","name":"grand_total","align":"right","type":"computed_currency","model":13502,"percent_model":0,"sub_total":null,"tbody_column":null,"formula":"difference","columns":["discount","sub_total"]}],"currency":{"code":"USD","precision":"2","separator":".","placement":"before"},"colspan":{"empty":"3","title":"2","value":1},"class_name":"items"},"cover.uf.terms_and_conditions.delivery_days":"10","cover.uf.terms_and_conditions.terms_url":"https:\/\/codekerala.com\/po-terms"}',
                'custom_values_2' => '[]'
            ]);
            }
        }
    }
}
