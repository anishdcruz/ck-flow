<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->increments('id');
            $table->string('number')->unique();
            $table->string('organization')->nullable();
            $table->string('person');
            $table->string('title')->nullable();
            $table->string('department')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('fax')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->text('primary_address')->nullable();
            $table->text('other_address')->nullable();
            $table->text('custom_values');
            $table->string('status_id');
            $table->timestamps();
        });

        Schema::create('lead_statuses', function (Blueprint $table) {
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
        Schema::dropIfExists('leads');
        Schema::dropIfExists('lead_statuses');
    }
}
