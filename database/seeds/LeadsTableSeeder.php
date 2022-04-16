<?php

use Illuminate\Database\Seeder;
use App\Lead\Lead;
use App\Lead\Status;
use Faker\Factory;

class LeadsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        Lead::truncate();
        Status::truncate();
        // foreach([
        //     ['name' => 'New', 'color' => 'grey', 'locked' => 1],
        //     ['name' => 'Contacted', 'color' => 'blue'],
        //     ['name' => 'Attempted to Contact', 'color' => 'orange'],
        //     ['name' => 'Qualified', 'color' => 'green'],
        //     ['name' => 'Disqualified', 'color' => 'red']
        // ] as $i) {
        //     Status::create($i);
        // }

        foreach(range(1, 100) as $i) {
        	Lead::create([
        		'organization' => $faker->company,
        		'person' => $faker->name,
        		'number' => 'LD-'.$i,
        		'phone' => $faker->phoneNumber,
        		'fax' => null,
        		'email' => $faker->unique()->safeEmail,
        		'website' => null,
        		'primary_address' => $faker->address,
        		'other_address' => null,
        		'custom_values' => '{}',
        		'status_id' => mt_rand(1, 5)
        	]);
        }
    }
}
