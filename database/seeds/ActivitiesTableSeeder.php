<?php

use Illuminate\Database\Seeder;
use App\Activity\Activity;
use App\Activity\Type;
use Faker\Factory;
use App\Contact\Contact;

class ActivitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	Activity::truncate();
    	Type::truncate();

        $faker = Factory::create();

        foreach([
        	['name' => 'Phone Call', 'color' => 'blue'],
            ['name' => 'Email', 'color' => 'green'],
            ['name' => 'Meeting', 'color' => 'pink']
        ] as $c) {
        	Type::create($c);
        }

        foreach(Contact::all() as $contact) {
        	foreach(range(1, mt_rand(6, 10)) as $i) {
        		$contact->activities()->create([
        			'date' => date('Y-m-d'),
        			'description' => $faker->text,
        			'type_id' => mt_rand(1, 3),
        			'user_id' => 1
        		]);
        	}
        }
    }
}
