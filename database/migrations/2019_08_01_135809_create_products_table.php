<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            $table->string('title');
            $table->string('shortDesc');
            $table->string('fullDesc');
            $table->string('location');
            $table->string('countryCode');
            $table->string('phoneNumber');
            $table->string('highlights');
            $table->string('included');
            $table->string('notIncluded');
            $table->string('knowBeforeYouGo');
            $table->string('foodAndDrink');
            $table->string('tags');
            $table->timestamps();
        });

        Schema::create('options', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
        });

        Schema::create('product_has_options', function (Blueprint $table) {
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('option_id');

            $table->foreign('option_id')
                ->references('id')
                ->on('options')
                ->onDelete('cascade');

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');

            $table->primary(['option_id', 'product_id']);
        });
        app('cache')
            ->store(config('product.cache.store') != 'default' ? config('product.cache.store') : null)
            ->forget(config('product.cache.key'));

    }


    public function down()
    {

        Schema::drop('product_has_options');
        Schema::drop('options');
        Schema::drop('products');

    }
}
