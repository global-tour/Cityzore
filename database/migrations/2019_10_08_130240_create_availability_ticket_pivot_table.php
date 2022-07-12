<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAvailabilityTicketPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('availability_ticket', function (Blueprint $table) {
            $table->integer('availability_id')->unsigned()->index();
            $table->foreign('availability_id')->references('id')->on('availability')->onDelete('cascade');
            $table->integer('ticket_id')->unsigned()->index();
            $table->foreign('ticket_id')->references('id')->on('ticket')->onDelete('cascade');
            $table->primary(['availability_id', 'ticket_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('availability_ticket');
    }
}
