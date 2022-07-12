<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlogpostMetatagPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blogpost_metatag', function (Blueprint $table) {
            $table->integer('blogpost_id')->unsigned()->index();
            $table->foreign('blogpost_id')->references('id')->on('blogpost')->onDelete('cascade');
            $table->integer('metatag_id')->unsigned()->index();
            $table->foreign('metatag_id')->references('id')->on('metatag')->onDelete('cascade');
            $table->primary(['blogpost_id', 'metatag_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blogpost_metatag');
    }
}
