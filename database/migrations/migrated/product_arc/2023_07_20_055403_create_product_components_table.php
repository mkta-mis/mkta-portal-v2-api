<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductComponentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_components', function (Blueprint $table) {
            $table->id();

            $table->string('product_id');
            
            $table->string('key');
            $table->string('title');

            $table->string('displayType')->comment('See product_component_keys model for Display Types');
            $table->longText('content')->nullable();

            $table->string('contentType')->default("html")->comment("See product_component_keys model for Content Type List");
            $table->integer('isVisible')->default(1)->comment("0 - False | 1 - True");
            
            $table->integer('creator_id')->default(0);
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
        Schema::dropIfExists('product_components');
    }
}
