<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOpportunitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opportunities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contact_id')->unsigned();
            $table->string('number')->unique();
            $table->string('title');
            $table->text('description');
            $table->integer('source_id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->integer('stage_id')->unsigned();
            $table->string('status_id'); // won or lost
            $table->tinyInteger('probability')->default(0);
            $table->date('start_date');
            $table->date('close_date');
            $table->double('value');
            $table->text('custom_values');
            $table->timestamps();
        });

        Schema::create('opportunity_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('opportunity_sources', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('opportunity_stages', function (Blueprint $table) {
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
        Schema::dropIfExists('opportunities');
        Schema::dropIfExists('opportunity_categories');
        Schema::dropIfExists('opportunity_sources');
        Schema::dropIfExists('opportunity_stages');
    }
}
