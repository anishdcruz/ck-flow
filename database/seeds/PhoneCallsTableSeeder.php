<?php

use Illuminate\Database\Seeder;
use App\PhoneCall;
use App\Contact\Contact;
use Faker\Factory;

class PhoneCallsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PhoneCall::truncate();

        $faker = Factory::create();

        foreach(Contact::all() as $contact) {
        	foreach(range(1, mt_rand(3, 5)) as $i) {
        		$contact->phoneCalls()->create([
        			'date' => date('Y-m-d'),
        			'description' => $faker->text,
        			'user_id' => 1
        		]);
        	}
        }
    }
}
