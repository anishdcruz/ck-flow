<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecurringExportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recurring_exports', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('frequency');
            $table->integer('send_on')->nullable();
            $table->string('send_at');
            $table->text('params');
            $table->string('model');
            $table->string('with')->nullable();
            $table->string('email_to');
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
        Schema::dropIfExists('recurring_exports');
    }
}
