<?php

use Illuminate\Database\Seeder;
use App\Contact\Contact;
use App\Note;
use Faker\Factory;

class NotesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Note::truncate();

        $faker = Factory::create();

        foreach(Contact::get() as $contact) {
        	foreach(range(3, mt_rand(4, 6)) as $i ) {
        		Note::create([
        			'user_id' => 1,
        			'notable_id' => $contact->id,
        			'notable_type' => Contact::class,
        			'description' => $faker->text
        		]);
        	}
        }
    }
}
