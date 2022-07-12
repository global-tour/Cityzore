<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePricingSupplierPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pricing_supplier', function (Blueprint $table) {
            $table->integer('pricing_id')->unsigned()->index();
            $table->foreign('pricing_id')->references('id')->on('pricing')->onDelete('cascade');
            $table->integer('supplier_id')->unsigned()->index();
            $table->foreign('supplier_id')->references('id')->on('supplier')->onDelete('cascade');
            $table->primary(['pricing_id', 'supplier_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pricing_supplier');
    }
}
