<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUtmCodesToLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('links', function (Blueprint $table) {
            $table->integer('utm_campaign_id')->unsigned()->nullable();
            $table->integer('utm_medium_id')->unsigned()->nullable();
            $table->integer('utm_source_id')->unsigned()->nullable();
            $table->integer('utm_content_id')->unsigned()->nullable();
            $table->integer('utm_term_id')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('links', function (Blueprint $table) {
            //
        });
    }
}
