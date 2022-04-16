<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->unique();
            $table->text('description');
            $table->integer('category_id')->unsigned();
            $table->integer('uom_id')->unsigned();
            $table->text('custom_values');
            $table->double('unit_price')->default(0);
            $table->timestamp('last_invoice')->nullable();
            $table->timestamps();
        });

        Schema::create('item_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('item_uoms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
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
        Schema::dropIfExists('items');
        Schema::dropIfExists('item_categories');
        Schema::dropIfExists('item_uoms');
    }
}
