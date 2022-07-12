<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBokunLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bokunlogs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longtext('request');
            $table->longtext('query');
            $table->longtext('server');
            $table->string('path');
            $table->string('fullPath');
            $table->string('fromDateTime');
            $table->string('toDateTime');
            $table->string('optionReferenceCode');
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
        Schema::dropIfExists('bokunlogs');
    }
}
