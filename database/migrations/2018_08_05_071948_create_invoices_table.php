<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contact_id')->unsigned();
            $table->integer('proposal_id')->nullable();
            $table->integer('status_id')->unsigned();
            $table->integer('contract_id')->nullable();
            $table->integer('template_id')->unsigned();
            $table->string('number')->unique();
            $table->date('issue_date');
            $table->date('due_date');
            $table->string('reference')->nullable();
            $table->double('sub_total');
            $table->double('grand_total');
            $table->double('amount_paid')->default(0);
            $table->text('custom_values');
            $table->text('custom_values_2');
            $table->timestamps();
        });

         Schema::create('invoice_lines', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('invoice_id')->unsigned();
            $table->integer('product_id')->nullable();
            $table->float('qty');
            $table->double('unit_price');
            $table->timestamps();
        });

        Schema::create('invoice_statuses', function (Blueprint $table) {
             $table->increments('id');
             $table->string('name')->unique();
             $table->string('color')->unique();
             $table->boolean('locked')->default(0);
             $table->timestamps();
         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('invoice_lines');
        Schema::dropIfExists('invoice_statuses');
    }
}
