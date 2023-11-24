<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumns1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('Dimension_Raw_L', 65, 2)->default(0.00);
            $table->decimal('Dimension_Raw_W', 65, 2)->default(0.00);
            $table->decimal('Dimension_Raw_H', 65, 2)->default(0.00);

            $table->decimal('Dimension_Packed_L', 65, 2)->default(0.00);
            $table->decimal('Dimension_Packed_W', 65, 2)->default(0.00);
            $table->decimal('Dimension_Packed_H', 65, 2)->default(0.00);

            $table->decimal('Weight_Net', 65, 2)->default(0.00);
            $table->decimal('Weight_Gross', 65, 2)->default(0.00);

            $table->decimal('Volume_Raw', 65, 2)->default(0.00);
            $table->decimal('Volume_Packed', 65, 2)->default(0.00);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
}
