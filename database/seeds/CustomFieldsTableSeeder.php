<?php

use Illuminate\Database\Seeder;
use App\CustomField;

class CustomFieldsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CustomField::truncate();

        $models = [
        	[
        		'name' => 'contacts',
        		'fields' => json_encode([
        			['type' => 'text', 'label' => 'Name', 'name' => 'name', 'model' => 'Apple Man', 'width' => 33],
        			['type' => 'number', 'label' => 'Count', 'name' => 'count', 'model' => 10, 'width' => 33],
        			['type' => 'date', 'label' => 'Issue Date', 'name' => 'issue_date_2', 'model' => null, 'width' => 33, 'format' => 'Y-m-d'],
        			[
        			    'type' => 'currency', 'label' => 'Total', 'name' => 'total', 'model' => 420, 'width' => 33,
        			    'currency' => ['code' => '$', 'placement' => 'before', 'precision' => 2, 'separator' => '.']
        			],
        			['type' => 'image', 'label' => 'Logo', 'name' => 'logo', 'model' => null, 'width' => 33],
        			['type' => 'textarea', 'label' => 'Desc', 'name' => 'desc', 'model' => 'lorem ipsum', 'width' => 66],
        			[
        			    'type' => 'select', 'label' => 'Placement', 'name' => 'placement', 'model' => 'Before', 'width' => 33,
        			    'options' => ['Before', 'After']
        			],
        		])
        	],
            [
                'name' => 'organizations',
                'fields' => json_encode([
                    ['type' => 'text', 'label' => 'Name', 'name' => 'name', 'model' => 'Apple Man', 'width' => 33],
                    ['type' => 'number', 'label' => 'Count', 'name' => 'count', 'model' => 10, 'width' => 33],
                    ['type' => 'date', 'label' => 'Issue Date', 'name' => 'issue_date_2', 'model' => null, 'width' => 33, 'format' => 'Y-m-d'],
                    [
                        'type' => 'currency', 'label' => 'Total', 'name' => 'total', 'model' => 420, 'width' => 33,
                        'currency' => ['code' => '$', 'placement' => 'before', 'precision' => 2, 'separator' => '.']
                    ],
                    ['type' => 'image', 'label' => 'Logo', 'name' => 'logo', 'model' => null, 'width' => 33],
                    ['type' => 'textarea', 'label' => 'Desc', 'name' => 'desc', 'model' => 'lorem ipsum', 'width' => 66],
                    [
                        'type' => 'select', 'label' => 'Placement', 'name' => 'placement', 'model' => 'Before', 'width' => 33,
                        'options' => ['Before', 'After']
                    ],
                ])
            ],
            [
                'name' => 'items',
                'fields' => json_encode([
                    ['type' => 'text', 'label' => 'Name', 'name' => 'name', 'model' => 'Apple Man', 'width' => 33],
                    ['type' => 'number', 'label' => 'Count', 'name' => 'count', 'model' => 10, 'width' => 33],
                    ['type' => 'date', 'label' => 'Issue Date', 'name' => 'issue_date_2', 'model' => null, 'width' => 33, 'format' => 'Y-m-d'],
                    [
                        'type' => 'currency', 'label' => 'Total', 'name' => 'total', 'model' => 420, 'width' => 33,
                        'currency' => ['code' => '$', 'placement' => 'before', 'precision' => 2, 'separator' => '.']
                    ],
                    ['type' => 'image', 'label' => 'Logo', 'name' => 'logo', 'model' => null, 'width' => 33],
                    ['type' => 'textarea', 'label' => 'Desc', 'name' => 'desc', 'model' => 'lorem ipsum', 'width' => 66],
                    [
                        'type' => 'select', 'label' => 'Placement', 'name' => 'placement', 'model' => 'Before', 'width' => 33,
                        'options' => ['Before', 'After']
                    ],
                ])
            ],
            [
                'name' => 'leads',
                'fields' => json_encode([
                    ['type' => 'text', 'label' => 'Name', 'name' => 'name', 'model' => 'Apple Man', 'width' => 33],
                    ['type' => 'number', 'label' => 'Count', 'name' => 'count', 'model' => 10, 'width' => 33],
                    ['type' => 'date', 'label' => 'Issue Date', 'name' => 'issue_date_2', 'model' => null, 'width' => 33, 'format' => 'Y-m-d'],
                    [
                        'type' => 'currency', 'label' => 'Total', 'name' => 'total', 'model' => 420, 'width' => 33,
                        'currency' => ['code' => '$', 'placement' => 'before', 'precision' => 2, 'separator' => '.']
                    ],
                    ['type' => 'image', 'label' => 'Logo', 'name' => 'logo', 'model' => null, 'width' => 33],
                    ['type' => 'textarea', 'label' => 'Desc', 'name' => 'desc', 'model' => 'lorem ipsum', 'width' => 66],
                    [
                        'type' => 'select', 'label' => 'Placement', 'name' => 'placement', 'model' => 'Before', 'width' => 33,
                        'options' => ['Before', 'After']
                    ],
                ])
            ],
            [
                'name' => 'opportunities',
                'fields' => json_encode([
                    ['type' => 'text', 'label' => 'Name', 'name' => 'name', 'model' => 'Apple Man', 'width' => 33],
                    ['type' => 'number', 'label' => 'Count', 'name' => 'count', 'model' => 10, 'width' => 33],
                    ['type' => 'date', 'label' => 'Issue Date', 'name' => 'issue_date_2', 'model' => null, 'width' => 33, 'format' => 'Y-m-d'],
                    [
                        'type' => 'currency', 'label' => 'Total', 'name' => 'total', 'model' => 420, 'width' => 33,
                        'currency' => ['code' => '$', 'placement' => 'before', 'precision' => 2, 'separator' => '.']
                    ],
                    ['type' => 'image', 'label' => 'Logo', 'name' => 'logo', 'model' => null, 'width' => 33],
                    ['type' => 'textarea', 'label' => 'Desc', 'name' => 'desc', 'model' => 'lorem ipsum', 'width' => 66],
                    [
                        'type' => 'select', 'label' => 'Placement', 'name' => 'placement', 'model' => 'Before', 'width' => 33,
                        'options' => ['Before', 'After']
                    ],
                ])
            ],
            [
                'name' => 'proposals',
                'fields' => json_encode([
                    ['type' => 'text', 'label' => 'Name', 'name' => 'name', 'model' => 'Apple Man', 'width' => 33],
                    ['type' => 'number', 'label' => 'Count', 'name' => 'count', 'model' => 10, 'width' => 33],
                    ['type' => 'date', 'label' => 'Issue Date', 'name' => 'issue_date_2', 'model' => null, 'width' => 33, 'format' => 'Y-m-d'],
                    [
                        'type' => 'currency', 'label' => 'Total', 'name' => 'total', 'model' => 420, 'width' => 33,
                        'currency' => ['code' => '$', 'placement' => 'before', 'precision' => 2, 'separator' => '.']
                    ],
                    ['type' => 'image', 'label' => 'Logo', 'name' => 'logo', 'model' => null, 'width' => 33],
                    ['type' => 'textarea', 'label' => 'Desc', 'name' => 'desc', 'model' => 'lorem ipsum', 'width' => 66],
                    [
                        'type' => 'select', 'label' => 'Placement', 'name' => 'placement', 'model' => 'Before', 'width' => 33,
                        'options' => ['Before', 'After']
                    ],
                ])
            ],
            [
                'name' => 'contracts',
                'fields' => json_encode([
                    ['type' => 'text', 'label' => 'Name', 'name' => 'name', 'model' => 'Apple Man', 'width' => 33],
                    ['type' => 'number', 'label' => 'Count', 'name' => 'count', 'model' => 10, 'width' => 33],
                    ['type' => 'date', 'label' => 'Issue Date', 'name' => 'issue_date_2', 'model' => null, 'width' => 33, 'format' => 'Y-m-d'],
                    [
                        'type' => 'currency', 'label' => 'Total', 'name' => 'total', 'model' => 420, 'width' => 33,
                        'currency' => ['code' => '$', 'placement' => 'before', 'precision' => 2, 'separator' => '.']
                    ],
                    ['type' => 'image', 'label' => 'Logo', 'name' => 'logo', 'model' => null, 'width' => 33],
                    ['type' => 'textarea', 'label' => 'Desc', 'name' => 'desc', 'model' => 'lorem ipsum', 'width' => 66],
                    [
                        'type' => 'select', 'label' => 'Placement', 'name' => 'placement', 'model' => 'Before', 'width' => 33,
                        'options' => ['Before', 'After']
                    ],
                ])
            ],
            [
                'name' => 'projects',
                'fields' => json_encode([
                    ['type' => 'text', 'label' => 'Name', 'name' => 'name', 'model' => 'Apple Man', 'width' => 33],
                    ['type' => 'number', 'label' => 'Count', 'name' => 'count', 'model' => 10, 'width' => 33],
                    ['type' => 'date', 'label' => 'Issue Date', 'name' => 'issue_date_2', 'model' => null, 'width' => 33, 'format' => 'Y-m-d'],
                    [
                        'type' => 'currency', 'label' => 'Total', 'name' => 'total', 'model' => 420, 'width' => 33,
                        'currency' => ['code' => '$', 'placement' => 'before', 'precision' => 2, 'separator' => '.']
                    ],
                    ['type' => 'image', 'label' => 'Logo', 'name' => 'logo', 'model' => null, 'width' => 33],
                    ['type' => 'textarea', 'label' => 'Desc', 'name' => 'desc', 'model' => 'lorem ipsum', 'width' => 66],
                    [
                        'type' => 'select', 'label' => 'Placement', 'name' => 'placement', 'model' => 'Before', 'width' => 33,
                        'options' => ['Before', 'After']
                    ],
                ])
            ],
            [
                'name' => 'invoices',
                'fields' => json_encode([
                    ['type' => 'text', 'label' => 'Name', 'name' => 'name', 'model' => 'Apple Man', 'width' => 33],
                    ['type' => 'number', 'label' => 'Count', 'name' => 'count', 'model' => 10, 'width' => 33],
                    ['type' => 'date', 'label' => 'Issue Date', 'name' => 'issue_date_2', 'model' => null, 'width' => 33, 'format' => 'Y-m-d'],
                    [
                        'type' => 'currency', 'label' => 'Total', 'name' => 'total', 'model' => 420, 'width' => 33,
                        'currency' => ['code' => '$', 'placement' => 'before', 'precision' => 2, 'separator' => '.']
                    ],
                    ['type' => 'image', 'label' => 'Logo', 'name' => 'logo', 'model' => null, 'width' => 33],
                    ['type' => 'textarea', 'label' => 'Desc', 'name' => 'desc', 'model' => 'lorem ipsum', 'width' => 66],
                    [
                        'type' => 'select', 'label' => 'Placement', 'name' => 'placement', 'model' => 'Before', 'width' => 33,
                        'options' => ['Before', 'After']
                    ],
                ])
            ],
            [
                'name' => 'payments',
                'fields' => json_encode([
                    ['type' => 'text', 'label' => 'Name', 'name' => 'name', 'model' => 'Apple Man', 'width' => 33],
                    ['type' => 'number', 'label' => 'Count', 'name' => 'count', 'model' => 10, 'width' => 33],
                    ['type' => 'date', 'label' => 'Issue Date', 'name' => 'issue_date_2', 'model' => null, 'width' => 33, 'format' => 'Y-m-d'],
                    [
                        'type' => 'currency', 'label' => 'Total', 'name' => 'total', 'model' => 420, 'width' => 33,
                        'currency' => ['code' => '$', 'placement' => 'before', 'precision' => 2, 'separator' => '.']
                    ],
                    ['type' => 'image', 'label' => 'Logo', 'name' => 'logo', 'model' => null, 'width' => 33],
                    ['type' => 'textarea', 'label' => 'Desc', 'name' => 'desc', 'model' => 'lorem ipsum', 'width' => 66],
                    [
                        'type' => 'select', 'label' => 'Placement', 'name' => 'placement', 'model' => 'Before', 'width' => 33,
                        'options' => ['Before', 'After']
                    ],
                ])
            ],
            [
                'name' => 'expenses',
                'fields' => json_encode([
                    ['type' => 'text', 'label' => 'Name', 'name' => 'name', 'model' => 'Apple Man', 'width' => 33],
                    ['type' => 'number', 'label' => 'Count', 'name' => 'count', 'model' => 10, 'width' => 33],
                    ['type' => 'date', 'label' => 'Issue Date', 'name' => 'issue_date_2', 'model' => null, 'width' => 33, 'format' => 'Y-m-d'],
                    [
                        'type' => 'currency', 'label' => 'Total', 'name' => 'total', 'model' => 420, 'width' => 33,
                        'currency' => ['code' => '$', 'placement' => 'before', 'precision' => 2, 'separator' => '.']
                    ],
                    ['type' => 'image', 'label' => 'Logo', 'name' => 'logo', 'model' => null, 'width' => 33],
                    ['type' => 'textarea', 'label' => 'Desc', 'name' => 'desc', 'model' => 'lorem ipsum', 'width' => 66],
                    [
                        'type' => 'select', 'label' => 'Placement', 'name' => 'placement', 'model' => 'Before', 'width' => 33,
                        'options' => ['Before', 'After']
                    ],
                ])
            ],
            [
                'name' => 'vendors',
                'fields' => json_encode([
                    ['type' => 'text', 'label' => 'Name', 'name' => 'name', 'model' => 'Apple Man', 'width' => 33],
                    ['type' => 'number', 'label' => 'Count', 'name' => 'count', 'model' => 10, 'width' => 33],
                    ['type' => 'date', 'label' => 'Issue Date', 'name' => 'issue_date_2', 'model' => null, 'width' => 33, 'format' => 'Y-m-d'],
                    [
                        'type' => 'currency', 'label' => 'Total', 'name' => 'total', 'model' => 420, 'width' => 33,
                        'currency' => ['code' => '$', 'placement' => 'before', 'precision' => 2, 'separator' => '.']
                    ],
                    ['type' => 'image', 'label' => 'Logo', 'name' => 'logo', 'model' => null, 'width' => 33],
                    ['type' => 'textarea', 'label' => 'Desc', 'name' => 'desc', 'model' => 'lorem ipsum', 'width' => 66],
                    [
                        'type' => 'select', 'label' => 'Placement', 'name' => 'placement', 'model' => 'Before', 'width' => 33,
                        'options' => ['Before', 'After']
                    ],
                ])
            ]
        ];

        foreach($models as $model) {
        	CustomField::create($model);
        }
    }
}
