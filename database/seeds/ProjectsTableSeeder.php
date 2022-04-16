<?php

use Illuminate\Database\Seeder;
use App\Project\Project;
use App\Project\Stage;
use App\Project\Category;
use Faker\Factory;

class ProjectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Project::truncate();
        Stage::truncate();
        Category::truncate();

        $faker = Factory::create();

        // foreach([
        // 	['name' => 'Not Started', 'color' => 'light_blue', 'locked' => true],
        // 	['name' => 'In Progress', 'color' => 'light_green'],
        // 	['name' => 'On Hold', 'color' => 'pink'],
        // 	['name' => 'Completed', 'color' => 'yellow']
        // ] as $c) {
        // 	Stage::create($c);
        // }

        // foreach([
        // 	'Category A', 'Category B', 'Category C'
        // ] as $c) {
        // 	Category::create(['name' => $c]);
        // }

        foreach(range(1, 100) as $i) {
        	Project::create([
                'number' => 'P'.$i,
        		'contact_id' => $i,
        		'proposal_id' => $i,
        		'stage_id' => mt_rand(1, 4),
        		'category_id' => mt_rand(1, 3),
                'title' => $faker->text,
        		'start_date' => '2018-04-'.mt_rand(1, 28),
        		'estimated_finish_date' => '2018-'.mt_rand(5, 12).'-'.mt_rand(1, 28),
        		'deadline_date' => '2018-'.mt_rand(5, 12).'-'.mt_rand(1, 28),
        		'actual_start_date' => null,
        		'actual_end_date' => null,
        		'progress' => 0,
        		'estimated_cost' => 10009,
        		'actual_cost' => 0,
        		'cost_consumption' => 0,
        		'custom_values' => '{}',
                'description' => $faker->text
        	]);
        }
    }
}
