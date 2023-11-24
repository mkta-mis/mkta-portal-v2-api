<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RelatedProductsColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
          Schema::table('products_related_products', function (Blueprint $table) {
                    $table->integer('product_id')->nullable();
                    $table->integer('relation_type')->default(0)->comment('0 - Group Product, 1 - Recommended Products')->nullable();
                    $table->integer('target_id')->nullable();

          });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
