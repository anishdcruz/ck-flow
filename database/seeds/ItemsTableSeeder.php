<?php

use Illuminate\Database\Seeder;
use App\Item\Item;
use App\Item\Category;
use Faker\Factory;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        Category::truncate();
        Item::truncate();

        foreach(range(5, 10) as $j) {
            Item::create([
                'code' => 'IP-'.mt_rand(100, 3000).$faker->numberBetween(1, 10000),
                'description' => 'Item '.$faker->text,
                'unit_price' => $faker->numberBetween(10, 8000),
                'category_id' => mt_rand(1, 2),
                'uom_id' => $faker->numberBetween(1, 5),
                'custom_values' => '{}'
            ]);
        }
    }
}
