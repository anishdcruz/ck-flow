<?php

use Illuminate\Database\Seeder;
use App\Opportunity\Opportunity;
use App\Opportunity\Category;
use App\Opportunity\Source;
use App\Opportunity\Stage;
use Faker\Factory;

class OpportunitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        Opportunity::truncate();
        Category::truncate();
        Source::truncate();
        Stage::truncate();

        // foreach([
        // 	'Category A', 'Category B', 'Category C'
        // ] as $c) {
        // 	Category::create(['name' => $c]);
        // }

        // foreach([
        // 	'Source A', 'Source B', 'Source C'
        // ] as $c) {
        // 	Source::create(['name' => $c]);
        // }

        // foreach([
        // 	['name' => 'New', 'color' => 'light_blue', 'locked' => 1],
        // 	['name' => 'Information Gathering', 'color' => 'light_green'],
        // 	['name' => 'Meeting', 'color' => 'pink'],
        // 	['name' => 'Proposal', 'color' => 'yellow'],
        // 	['name' => 'Follow up', 'color' => 'cyan'],
        // 	['name' => 'Closed', 'color' => 'red', 'locked' => 1]
        // ] as $c) {
        // 	Stage::create($c);
        // }

        foreach(range(1, 100) as $i) {
        	Opportunity::create([
                'number' => 'OT'.$i,
        		'contact_id' => $i,
        		'title' => 'Opportunity '.substr($faker->text, 0, 45),
        		'description' => $faker->paragraph,
        		'source_id' => mt_rand(1, 3),
        		'category_id' => mt_rand(1, 3),
        		'stage_id' => mt_rand(1, 6),
        		'probability' => $faker->randomElement([25, 33, 50, 66, 75, 100]),
        		'start_date' => '2018-04-'.mt_rand(1, 28),
        		'close_date' => '2018-'.mt_rand(5, 12).'-'.mt_rand(1, 28),
        		'status_id' => $faker->randomElement(['open', 'won', 'lost']),
                'value' => $faker->numberBetween(100, 10000),
                'custom_values' => '{}'
        	]);
        }
    }
}
