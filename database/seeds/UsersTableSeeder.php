<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Role;
use Faker\Factory;
use App\Services\Permission;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        $roles = [
        	[
        		'name' => 'admin',
        		'description' => 'admin',
        		'permissions' => json_encode(Permission::schema())
        	],
        	[
        		'name' => 'basic',
        		'description' => 'basic',
        		'permissions' => json_encode(Permission::schema())
        	]
        ];

        foreach($roles as $role) {
        	$r = Role::create($role);

        	foreach(range(1, 3) as $i) {
        		User::create([
        			'role_id' => $r->id,
        			'name' => $faker->name,
        			'email' => $faker->safeEmail,
        			'password' => bcrypt('password')
        		]);
        	}
        }

        User::create([
            'role_id' => Role::first()->id,
            'name' => 'John Doe',
            'email' => 'admin@flow.test',
            'password' => bcrypt('password')
        ]);

        User::create([
            'role_id' => Role::first()->id,
            'name' => 'Jane Doe',
            'email' => 'admin2@flow.test',
            'password' => bcrypt('password')
        ]);
    }
}
