<?php

use Illuminate\Database\Seeder;
use App\Template\Template;
use App\Template\Page;
use Faker\Factory;

class TemplatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        Template::truncate();
        Page::truncate();

        $this->createProposals($faker);
    }

    protected function createProposals($faker)
    {
    	$names = [
        	'Web Design Only',
        	'Web Development Only',
        	'Web Design and Development',
        	'CRM Solution',
        	'Mobile Application Development',
        	'Mobile Game Development',
        	'Wordpress Development',
        	'Drupal Development',
        	'Laravel Development',
            'Vue.js Development',
        ];

        $pages = [
            'Cover Page', 'Table of Content', 'About Us',
            'What we do', 'Our Portfolio', 'Case Study',
            'Our Services', 'Solution Outline', 'Project Timeline', 'Project Costs', 'Terms and Conditions',
            'Acceptance of Quote'
        ];

        foreach($names as $name) {
        	$t = Template::create([
        		'name' => $name. ' Contract',
        		'type_id' => 2,
        		'page_size' => $faker->randomElement(['A4', 'legal']),
        		'orientation' => $faker->randomElement(['L', 'P']),
        		'header_height' => 25,
        		'footer_height' => 25,
                'stylesheet' => '.header {background: blue;}.footer {background: red;}.page {background: pink;}',

        		'header_html' => '<div class="header">Header</div>',
        		'header_content_fields' => json_encode([
                    ['title' => __('lang.default'), 'name' => snake_case(__('lang.default')), 'fields' => $this->fields()]
                ]),

        		'footer_html' => '<div class="footer">Footer</div>',
        		'footer_content_fields' => json_encode([
                    ['title' => __('lang.default'), 'name' => snake_case(__('lang.default')), 'fields' => $this->fields()]
                ]),
        	]);

            $tp = [];
            $i = 0;
            foreach(array_random($pages, mt_rand(3, count($pages))) as $page) {
                $tp[] = new Page([
                    'title' => $page,
                    'name' => snake_case($page),
                    'orientation' => $t->orientation,
                    'instruction' => $faker->text,
                    'index' => $i,
                    'page_html' => '<div class="page">'.$page.'</div>',
                    'content_fields' => json_encode([
                        ['title' => __('lang.default'), 'name' => snake_case(__('lang.default')), 'fields' => $this->fields()]
                    ]),
                    'user_fields' => json_encode([
                        ['title' => __('lang.default'), 'name' => snake_case(__('lang.default')), 'fields' => $this->fields()]
                    ])
                ]);
                $i++;
            }
            $t->pages()->saveMany($tp);
        }

        foreach($names as $name) {
            $t = Template::create([
                'name' => $name,
                'type_id' => 1,
                'page_size' => $faker->randomElement(['A4', 'legal']),
                'orientation' => $faker->randomElement(['L', 'P']),
                'header_height' => 25,
                'footer_height' => 25,
                'stylesheet' => '.header {background: blue;}.footer {background: red;}.page {background: pink;}',

                'header_html' => '<div class="header">Header</div>',
                'header_content_fields' => json_encode([
                    ['title' => __('lang.default'), 'name' => snake_case(__('lang.default')), 'fields' => $this->fields()]
                ]),

                'footer_html' => '<div class="footer">Footer</div>',
                'footer_content_fields' => json_encode([
                    ['title' => __('lang.default'), 'name' => snake_case(__('lang.default')), 'fields' => $this->fields()]
                ]),
            ]);

            $tp = [];
            $i = 0;
            foreach(array_random($pages, mt_rand(3, count($pages))) as $page) {
                $tp[] = new Page([
                    'title' => $page,
                    'name' => snake_case($page),
                    'orientation' => $t->orientation,
                    'instruction' => $faker->text,
                    'index' => $i,
                    'page_html' => '<div class="page">'.$page.'</div>',
                    'content_fields' => json_encode([
                        ['title' => __('lang.default'), 'name' => snake_case(__('lang.default')), 'fields' => $this->fields()]
                    ]),
                    'user_fields' => json_encode([
                        ['title' => __('lang.default'), 'name' => snake_case(__('lang.default')), 'fields' => $this->fields()]
                    ])
                ]);
                $i++;
            }
            $t->pages()->saveMany($tp);
        }

        $invoices = ['Invoice 1', 'Invoice 2'];
        foreach($invoices as $name) {
            $t = Template::create([
                'name' => $name,
                'type_id' => 3,
                'page_size' => $faker->randomElement(['A4', 'legal']),
                'orientation' => $faker->randomElement(['L', 'P']),
                'header_height' => 25,
                'footer_height' => 25,
                'stylesheet' => '.header {background: blue;}.footer {background: red;}.page {background: pink;}',

                'header_html' => '<div class="header">Header</div>',
                'header_content_fields' => json_encode([
                    ['title' => __('lang.default'), 'name' => snake_case(__('lang.default')), 'fields' => []]
                ]),

                'footer_html' => '<div class="footer">Footer</div>',
                'footer_content_fields' => json_encode([
                    ['title' => __('lang.default'), 'name' => snake_case(__('lang.default')), 'fields' => []]
                ]),
            ]);

            $tp = [];
            $i = 0;
            foreach(['Main'] as $page) {
                $tp[] = new Page([
                    'title' => $page,
                    'name' => snake_case($page),
                    'orientation' => $t->orientation,
                    'instruction' => $faker->text,
                    'index' => $i,
                    'page_html' => '<div class="page">'.$page.'</div>',
                    'content_fields' => json_encode([
                        ['title' => __('lang.default'), 'name' => snake_case(__('lang.default')), 'fields' => []]
                    ]),
                    'user_fields' => json_encode([
                        ['title' => __('lang.default'), 'name' => snake_case(__('lang.default')), 'fields' => []]
                    ])
                ]);
                $i++;
            }
            $t->pages()->saveMany($tp);
        }

        $invoices = ['Payment 1', 'Payment 2'];
        foreach($invoices as $name) {
            $t = Template::create([
                'name' => $name,
                'type_id' => 4,
                'page_size' => $faker->randomElement(['A4', 'legal']),
                'orientation' => $faker->randomElement(['L', 'P']),
                'header_height' => 25,
                'footer_height' => 25,
                'stylesheet' => '.header {background: blue;}.footer {background: red;}.page {background: pink;}',

                'header_html' => '<div class="header">Header</div>',
                'header_content_fields' => json_encode([
                    ['title' => __('lang.default'), 'name' => snake_case(__('lang.default')), 'fields' => []]
                ]),

                'footer_html' => '<div class="footer">Footer</div>',
                'footer_content_fields' => json_encode([
                    ['title' => __('lang.default'), 'name' => snake_case(__('lang.default')), 'fields' => []]
                ]),
            ]);

            $tp = [];
            $i = 0;
            foreach(['Main'] as $page) {
                $tp[] = new Page([
                    'title' => $page,
                    'name' => snake_case($page),
                    'orientation' => $t->orientation,
                    'instruction' => $faker->text,
                    'index' => $i,
                    'page_html' => '<div class="page">'.$page.'</div>',
                    'content_fields' => json_encode([
                        ['title' => __('lang.default'), 'name' => snake_case(__('lang.default')), 'fields' => []]
                    ]),
                    'user_fields' => json_encode([
                        ['title' => __('lang.default'), 'name' => snake_case(__('lang.default')), 'fields' => []]
                    ])
                ]);
                $i++;
            }
            $t->pages()->saveMany($tp);
        }

        $invoices = ['Expense 1', 'Expense 2'];
        foreach($invoices as $name) {
            $t = Template::create([
                'name' => $name,
                'type_id' => 5,
                'page_size' => $faker->randomElement(['A4', 'legal']),
                'orientation' => $faker->randomElement(['L', 'P']),
                'header_height' => 25,
                'footer_height' => 25,
                'stylesheet' => '.header {background: blue;}.footer {background: red;}.page {background: pink;}',

                'header_html' => '<div class="header">Header</div>',
                'header_content_fields' => json_encode([
                    ['title' => __('lang.default'), 'name' => snake_case(__('lang.default')), 'fields' => []]
                ]),

                'footer_html' => '<div class="footer">Footer</div>',
                'footer_content_fields' => json_encode([
                    ['title' => __('lang.default'), 'name' => snake_case(__('lang.default')), 'fields' => []]
                ]),
            ]);

            $tp = [];
            $i = 0;
            foreach(['Main'] as $page) {
                $tp[] = new Page([
                    'title' => $page,
                    'name' => snake_case($page),
                    'orientation' => $t->orientation,
                    'instruction' => $faker->text,
                    'index' => $i,
                    'page_html' => '<div class="page">'.$page.'</div>',
                    'content_fields' => json_encode([
                        ['title' => __('lang.default'), 'name' => snake_case(__('lang.default')), 'fields' => []]
                    ]),
                    'user_fields' => json_encode([
                        ['title' => __('lang.default'), 'name' => snake_case(__('lang.default')), 'fields' => []]
                    ])
                ]);
                $i++;
            }
            $t->pages()->saveMany($tp);
        }
    }

    protected function fields()
    {
        $fields = [
            [
                'type' => 'table', 'label' => 'Price List', 'name' => 'price_list', 'width' => 100,
                'currency' => ['code' => '$', 'placement' => 'before', 'precision' => 2, 'separator' => '.'],
                'thead' => [
                    ['align' => 'right', 'title' => 'Description', 'name' => 'description', 'width' => '9', 'type' => 'textarea', 'default' => null],
                    [
                        'align' => 'right', 'title' => 'OS', 'name' => 'os', 'width' => '2', 'type' => 'select',
                        'default' => 'MacOS', 'options' => ['MacOS', 'Win10', 'Linux']
                    ],
                    ['align' => 'right', 'title' => 'Delivery Date', 'name' => 'delivery_date', 'width' => '3', 'type' => 'date',
                        'format' => 'Y-m-d', 'default' => null],
                    ['align' => 'right', 'title' => 'Price', 'name' => 'price', 'width' => '3', 'type' => 'currency', 'default' => 0],
                    ['align' => 'right', 'title' => 'Qty', 'name' => 'qty', 'width' => '2', 'type' => 'number', 'default' => 0],
                    ['align' => 'right', 'title' => 'Before Discount', 'name' => 'before_discount', 'type' => 'hidden', 'default' => 0, 'formula' => 'product', 'columns' => ['price', 'qty']],
                    ['align' => 'right', 'title' => 'Discount', 'name' => 'discount', 'width' => '2', 'type' => 'currency', 'default' => 0],
                    ['align' => 'right', 'title' => 'Total', 'name' => 'total', 'width' => '3', 'type' => 'computed_currency', 'formula' => 'difference', 'columns' => ['discount', 'before_discount']]
                ],
                'tbody' => [
                    ['description' => 'lorem 1', 'os' => 'MacOS', 'delivery_date' => null, 'price' => 10, 'qty' => 2, 'before_discount' => 20, 'discount' => 4, 'total' => 16],
                    ['description' => 'lorem 2', 'os' => 'MacOS', 'delivery_date' => null, 'price' => 10, 'qty' => 3, 'before_discount' => 30, 'discount' => 4, 'total' => 26]
                ],
                'tfoot' => [
                    ['align' => 'right', 'title' => 'Sub Total', 'name' => 'sub_total', 'type' => 'aggregate_currency', 'formula' => 'sum', 'tbody_column' => 'total', 'model' => 0],
                    ['align' => 'right', 'title' => 'Tax 1', 'name' => 'tax_1', 'type' => 'tax', 'percent_model' => 0, 'model' => 0, 'sub_total' => 'sub_total'],
                    ['align' => 'right', 'title' => 'Tax 2', 'name' => 'tax_2', 'type' => 'tax', 'percent_model' => 0, 'model' => 0, 'sub_total' => 'sub_total'],
                    ['align' => 'right', 'title' => 'Total Tax', 'name' => 'total_tax', 'type' => 'computed_currency', 'formula' => 'sum', 'columns' => ['tax_1', 'tax_2'], 'model' => 0],
                    ['align' => 'right', 'title' => 'Shipping Fees', 'name' => 'shipping_fees', 'type' => 'input_currency', 'model' => 0],
                    ['align' => 'right', 'title' => 'Grand Total', 'name' => 'grand_total', 'type' => 'computed_currency', 'formula' => 'sum', 'columns' => ['shipping_fees', 'total_tax', 'sub_total'], 'model' => 0]
                ],
                'colspan' => [
                    'empty' => 2,
                    'title' => 3,
                    'value' => 2
                ],
                'className' => ''
            ],
            ['type' => 'text', 'label' => 'Name', 'name' => 'name', 'model' => 'Apple Man', 'width' => 33],
            ['type' => 'number', 'label' => 'Count', 'name' => 'count', 'model' => 10, 'width' => 33],
            ['type' => 'date', 'label' => 'Issue Date', 'name' => 'issue_date', 'model' => null, 'width' => 33, 'format' => 'Y-m-d'],
            [
                'type' => 'currency', 'label' => 'Total', 'name' => 'total', 'model' => 420, 'width' => 33,
                'currency' => ['code' => '$', 'placement' => 'before', 'precision' => 2, 'separator' => '.']
            ],
            ['type' => 'image', 'label' => 'Logo', 'name' => 'logo', 'model' => null, 'width' => 33],
            ['type' => 'textarea', 'label' => 'Description', 'name' => 'description', 'model' => 'lorem ipsum', 'width' => 66],
            [
                'type' => 'select', 'label' => 'Placement', 'name' => 'placement', 'model' => 'Before', 'width' => 33,
                'options' => ['Before', 'After']
            ],
            [
                'type' => 'list', 'label' => 'Todo List', 'name' => 'todo_list', 'width' => 33,
                'list_model' => ['Todo 1', 'Todo 2', 'Todo 3']
            ]
        ];

        return $fields;
    }
}
