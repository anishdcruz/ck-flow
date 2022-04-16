<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vendor_id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->string('number')->unique();
            $table->date('date');
            $table->string('description');
            $table->double('amount');
            $table->integer('project_id')->nullable();
            $table->integer('opportunity_id')->nullable();
            $table->text('custom_values');
            $table->timestamps();
        });

        Schema::create('expense_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Schema::create('expense_vendors', function (Blueprint $table) {
        //     $table->increments('id');
        //     $table->string('name')->unique();
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('expense_categories');
        // Schema::dropIfExists('expense_vendors');
    }
}
