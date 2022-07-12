<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAvailabilityTickettypePivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('availability_tickettype', function (Blueprint $table) {
            $table->integer('availability_id')->unsigned()->index();
            $table->foreign('availability_id')->references('id')->on('availability')->onDelete('cascade');
            $table->integer('tickettype_id')->unsigned()->index();
            $table->foreign('tickettype_id')->references('id')->on('tickettype')->onDelete('cascade');
            $table->primary(['availability_id', 'tickettype_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('availability_tickettype');
    }
}
