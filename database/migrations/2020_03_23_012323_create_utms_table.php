<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUtmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('utms', function (Blueprint $table) {
            $table->engine = 'InnoDB'; // use for foreign key support

            $table->increments('id');
            $table->string('utm_name');
            $table->string('utm_description');
            $table->integer('utm_type')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('url_counts')->unsigned()->default('0');;
            $table->integer('click_counts')->unsigned()->default('0');;
            $table->boolean('disabled')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('utms');
    }
}
