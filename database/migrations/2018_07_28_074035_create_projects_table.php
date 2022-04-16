<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contact_id')->unsigned();
            $table->integer('proposal_id')->nullable();
            $table->integer('stage_id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->string('number')->unique();
            $table->string('title');
            $table->date('start_date');
            $table->date('estimated_finish_date');
            $table->date('deadline_date')->nullable();
            $table->date('actual_start_date')->nullable();
            $table->date('actual_end_date')->nullable();
            $table->integer('progress')->default(0); // percent
            $table->double('estimated_cost');
            $table->double('actual_cost')->default(0);
            $table->double('cost_consumption')->default(0);
            $table->text('description');
            $table->text('custom_values');
            $table->timestamps();
        });

        Schema::create('project_stages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('color')->unique();
            $table->boolean('locked')->default(0);
            $table->timestamps();
        });

        Schema::create('project_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('project_tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('project_id')->unsigned();
            $table->string('title');
            $table->date('start_date');
            $table->date('due_date')->nullable();
            $table->text('description');
            $table->integer('stage_id');
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
        Schema::dropIfExists('projects');
        Schema::dropIfExists('project_categories');
        Schema::dropIfExists('project_stages');
        Schema::dropIfExists('project_tasks');
    }
}
