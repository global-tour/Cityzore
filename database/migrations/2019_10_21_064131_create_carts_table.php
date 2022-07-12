<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('referenceCode')->nullable();
            $table->longText('bookingItems')->nullable();
            $table->decimal('totalPrice')->nullable();
            $table->integer('userID')->nullable();
            $table->integer('status')->default(0);
            $table->integer('isGYG')->default(0);
            $table->integer('optionID');
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
        Schema::dropIfExists('carts');
    }
}
