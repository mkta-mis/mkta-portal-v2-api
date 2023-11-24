<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsInRestrictionCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('restriction_categories', function (Blueprint $table) {
                $table->integer('customer_id')->default('0')->nullable();
                $table->integer('category_id')->default('0')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('restriction_categories', function (Blueprint $table) {
            //
        });
    }
}
