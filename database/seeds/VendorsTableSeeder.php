<?php

use Illuminate\Database\Seeder;
use App\Vendor\Vendor;
use App\Vendor\Category;
use Faker\Factory;

class VendorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        Vendor::truncate();
        Category::truncate();

        // foreach([
        // 	'Category A', 'Category B', 'Category C'
        // ] as $c) {
        // 	Category::create(['name' => $c]);
        // }

        foreach(range(1, 100) as $i) {
        	Vendor::create([
        		'category_id' => mt_rand(1, 3),
        		'name' => $faker->name,
        		'number' => 'VD-'.$i,
        		'phone' => $faker->phoneNumber,
        		'fax' => $faker->phoneNumber,
        		'email' => $faker->unique()->safeEmail,
        		'website' => $faker->domainName,
        		'primary_address' => $faker->address,
        		'other_address' => $faker->address,
        		'custom_values' => '[]'
        	]);
        }
    }
}
