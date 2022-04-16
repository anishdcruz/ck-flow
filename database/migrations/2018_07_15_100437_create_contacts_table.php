<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('organization_id')->nullable();
            $table->string('number')->unique();
            $table->string('name');
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
            $table->double('total_revenue')->default(0);
            $table->double('amount_receivable')->default(0);
            $table->timestamp('last_contact')->nullable();
            $table->timestamp('last_project')->nullable();
            $table->timestamp('last_invoice')->nullable();
            $table->timestamp('last_payment')->nullable();
            $table->timestamp('last_proposal')->nullable();
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
        Schema::dropIfExists('contacts');
    }
}
