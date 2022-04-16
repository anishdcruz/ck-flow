<?php

use Illuminate\Database\Seeder;
use App\Payment\Payment;
use App\Payment\Deposit;
use App\Payment\Method;
use App\Payment\Line;
use Faker\Factory;

class PaymentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$faker = Factory::create();

    	Payment::truncate();
    	Method::truncate();
    	Deposit::truncate();
        Line::truncate();

        // foreach([
        // 	['name' => 'Cash'],
        // 	['name' => 'Cheque'],
        // 	['name' => 'Credit Card'],
        // 	['name' => 'Bank Transfer'],
        // ] as $c) {
        // 	Method::create($c);
        // }

        // foreach([
        // 	['name' => 'Undeposited Funds'],
        // 	['name' => 'Bank A'],
        // 	['name' => 'Bank B'],
        // 	['name' => 'Bank C'],
        // ] as $c) {
        // 	Deposit::create($c);
        // }

        foreach(range(1, 100) as $i) {
            $a = $faker->numberBetween(1000, 40000);
        	$p = Payment::create([
                'number' => 'PAY-'.$i,
        		'contact_id' => $i,
        		'payment_date' => '2018-07-'.mt_rand(1, 28),
        		'method_id' => mt_rand(1, 4),
        		'reference' => str_random(5, 10),
        		'deposit_id' => mt_rand(1, 4),
        		'amount_received' => $a,
                'amount_applied' => $a,
                'bank_fees' => 0,
                'net_amount' => $a,
        		'note' => null,
        		'custom_values' => '{}'
        	]);

            Line::create([
                'payment_id' => $p->id,
                'invoice_id' => $i,
                'amount_applied' => $a,
            ]);
        }
    }
}
