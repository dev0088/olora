<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImageToLinksTable extends Migration
{
    public function up()
    {
        Schema::table('links', function (Blueprint $table) {
            $table->string('image');
        });
    }

    public function down()
    {
        Schema::table('links', function (Blueprint $table) {

        });
    }
}
