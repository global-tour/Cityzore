<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTable extends Migration
{

    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('paymentMethod');
            $table->integer('bookingID');
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
