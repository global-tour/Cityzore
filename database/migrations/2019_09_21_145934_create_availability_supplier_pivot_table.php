<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAvailabilitySupplierPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('availability_supplier', function (Blueprint $table) {
            $table->integer('availability_id')->unsigned()->index();
            $table->foreign('availability_id')->references('id')->on('availability')->onDelete('cascade');
            $table->integer('supplier_id')->unsigned()->index();
            $table->foreign('supplier_id')->references('id')->on('supplier')->onDelete('cascade');
            $table->primary(['availability_id', 'supplier_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('availability_supplier');
    }
}
