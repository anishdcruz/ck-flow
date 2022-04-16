<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('callable_id')->unsigned();
            $table->string('callable_type');
            $table->integer('type_id')->integer();
            $table->text('description');
            $table->date('date');
            $table->timestamps();
        });

        Schema::create('activity_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('color');
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
        Schema::dropIfExists('activities');
        Schema::dropIfExists('activity_types');
    }
}
