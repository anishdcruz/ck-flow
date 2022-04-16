<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProposalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proposals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contact_id')->unsigned();
            $table->integer('opportunity_id')->nullable();
            $table->integer('template_id')->unsigned();
            $table->string('number')->unique();
            $table->date('issue_date');
            $table->date('expiry_date');
            $table->text('custom_values');
            $table->text('custom_values_2');
            $table->integer('status_id')->unsigned();
            $table->timestamps();
        });

        Schema::create('proposal_statuses', function (Blueprint $table) {
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
        Schema::dropIfExists('proposals');
        Schema::dropIfExists('proposal_statuses');
    }
}
