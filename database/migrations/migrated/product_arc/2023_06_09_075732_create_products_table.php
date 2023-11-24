<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->longText('description')->comment("ChatGPT Generated")->nullable();
            $table->integer('parent_id')->default(0)->comment("if not 0 means its a variant");
            $table->integer('isVisible')->default(1)->comment("0 - False | 1 - True");

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
        Schema::dropIfExists('products');
    }
}
