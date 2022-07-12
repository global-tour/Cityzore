<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOptionSupplierPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('option_supplier', function (Blueprint $table) {
            $table->integer('option_id')->unsigned()->index();
            $table->foreign('option_id')->references('id')->on('option')->onDelete('cascade');
            $table->integer('supplier_id')->unsigned()->index();
            $table->foreign('supplier_id')->references('id')->on('supplier')->onDelete('cascade');
            $table->primary(['option_id', 'supplier_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('option_supplier');
    }
}
