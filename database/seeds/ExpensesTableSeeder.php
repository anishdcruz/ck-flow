<?php

use Illuminate\Database\Seeder;
use App\Expense\Expense;
use App\Expense\Category;
// use App\Vendor;
use Faker\Factory;

class ExpensesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        // Vendor::truncate();
        Category::truncate();
        Expense::truncate();

        // foreach(range(1, 10) as $i) {
        // 	Vendor::create(['name' => $faker->company]);
        // }

            foreach(range(1, 50) as $i) {
                Expense::create([
                    'number' => 'EXP-'.mt_rand(100, 3000).$i,
                    'vendor_id' => mt_rand(1, 10),
                    'category_id' => mt_rand(1, 3),
                    'date' => '2018-08-'.mt_rand(1, 28),
                    'description' => $faker->text,
                    'amount' => $faker->numberBetween(100, 20000),
                    'project_id' => mt_rand(1, 10),
                    'opportunity_id' => mt_rand(1, 10),
                    'custom_values' => '[]'
                ]);
            }
    }
}
