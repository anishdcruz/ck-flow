<?php

use Illuminate\Database\Seeder;
use App\Contract\Contract;
use App\Contract\Type;
use App\Contract\Status;
use Faker\Factory;

class ContractsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        Contract::truncate();
        Status::truncate();
        Type::truncate();

        // foreach([
        // 	['name' => 'Draft', 'color' => 'grey', 'locked' => true],
        // 	['name' => 'Sent', 'color' => 'light_green'],
        // 	['name' => 'Accepted', 'color' => 'blue'],
        // 	['name' => 'Declined', 'color' => 'red'],
        // 	['name' => 'Terminated', 'color' => 'orange']
        // ] as $c) {
        // 	Status::create($c);
        // }

        // foreach([
        // 	['name' => 'Project'],
        // 	['name' => 'Annual Maintainance']
        // ] as $c) {
        // 	Type::create($c);
        // }

        foreach(range(1, 50) as $i) {
        	Contract::create([
                'title' => 'Contract Title 1'.$i,
                'number' => 'PR1'.$i,
        		'contact_id' => $i,
        		'proposal_id' => $i,
        		'template_id' => 2,
                'title' => $faker->text,
        		'start_date' => '2018-07-'.mt_rand(1, 28),
        		'expiry_date' => '2018-08-'.mt_rand(1, 28),
        		'status_id' => mt_rand(1, 5),
        		'type_id' => mt_rand(1, 2),
        		'value' => $faker->numberBetween(1000, 90000),
        		'custom_values' => '{}',
                'custom_values_2' => '[]'
        	]);
        }

        foreach(range(1, 50) as $i) {
        	Contract::create([
                'title' => 'Contract Title'.$i,
                'number' => 'PR2'.$i,
        		'contact_id' => $i,
        		'proposal_id' => $i,
        		'template_id' => 2,
        		'start_date' => '2018-07-'.mt_rand(1, 28),
        		'expiry_date' => null,
        		'auto_renewal' => 1,
        		'no_of_months' => $faker->randomElement([6, 12]),
        		'status_id' => mt_rand(1, 5),
        		'type_id' => mt_rand(1, 2),
        		'value' => $faker->numberBetween(1000, 90000),
        		'custom_values' => '{"new_page1.uf.default.second_party_office_hours":null,"new_page1.uf.default.support_contact_person":"John Doe, Mob: +1234567899","new_page1.uf.default.support_supervisor":"James Doe, Mob: +1234567890","final.uf.default.first_party_authorized_name":null,"final.uf.default.first_party_authorized_position":null,"final.uf.default.second_party_authorized_name":null,"final.uf.default.second_party_authorized_position":null}',
                'custom_values_2' => '[]'
        	]);
        }
    }
}
