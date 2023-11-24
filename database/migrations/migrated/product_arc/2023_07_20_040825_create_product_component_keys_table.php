<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductComponentKeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_component_keys', function (Blueprint $table) {
            $table->id();

            $table->string('key');
            $table->string('title');

            $table->string('displayType')->comment('See model for Display Types');
            $table->longText('defaultContent');

            $table->string('contentType')->default("html")->comment("See model for Content Type List");
            $table->integer('isVisible')->default(1)->comment("0 - False | 1 - True");

            $table->integer('creator_id')->default(0);
            $table->timestamps();
        });

        $arr = array(
            array(
                'key' => 'product-dimension',
                'title' => 'Dimension',

                'displayType' => 'table',
                'defaultContent' => '[["", "Centimeter (cm)", "Inch (in)"],["Length", "0.00 cm", "0.00 in"],["Width", "0.00 cm", "0.00 in"],["Height", "0.00 cm", "0.00 in"]]',

                'contentType' => 'json',
                'isVisible' => 1
            ),
            array(
                'key' => 'product-weight-mass',
                'title' => 'Weight / Mass',

                'displayType' => 'table',
                'defaultContent' => '[["", "Kilogram (kg)", "Pound (lb)"],["Net / Raw", "0.00 kg", "0.00 lb"],["Gross / Packed", "0.00 kg", "0.00 lb"]]',

                'contentType' => 'json',
                'isVisible' => 1
            ),
            array(
                'key' => 'product-packaging',
                'title' => 'Packaging',

                'displayType' => 'table',
                'defaultContent' => '[["", "Cubic meter (m3)", "Cubic ft (ft3)"],["Packed Volume", "0.00 m3", "0.00 ft3"],["Knockdowns", "0", "0"],["No of Carton(s)", "0", "0"]]',

                'contentType' => 'json',
                'isVisible' => 1
            ),

        );

        foreach ($arr as $key => $value) { DB::table('product_component_keys')->insert($value); }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_component_keys');
    }
}
