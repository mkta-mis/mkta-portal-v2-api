<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsInRestrictionProductComponents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('restriction_product_components', function (Blueprint $table) {
            $table->integer('customer_id')->default('0');
            $table->longText('component_key')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('restriction_product_components', function (Blueprint $table) {
            //
        });
    }
}
