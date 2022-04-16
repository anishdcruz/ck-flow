<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('type_id')->unsigned();

            $table->string('name')->unique();
            $table->text('stylesheet');

            $table->string('page_size');
            $table->string('orientation');

            $table->integer('header_height');
            $table->integer('footer_height');

            $table->text('header_html');
            $table->text('header_content_fields');

            $table->text('footer_html');
            $table->text('footer_content_fields');
            $table->timestamps();
        });

        Schema::create('template_pages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('template_id')->unsigned();

            $table->string('title');
            $table->string('name');
            $table->integer('index');

            $table->boolean('header_and_footer')->default(1);
            $table->string('orientation')->nullable();
            $table->text('instruction')->nullable();

            $table->text('page_html');
            $table->text('content_fields');
            $table->text('user_fields');
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
        Schema::dropIfExists('templates');
        Schema::dropIfExists('template_pages');
    }
}
