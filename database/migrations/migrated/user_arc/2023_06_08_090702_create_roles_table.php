<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description');
            $table->timestamps();
        });
        
        $arr = array(
            array(
                'title' => 'Guest',
                'description' => 'This user can only view the Web Store ( with default product items ) and Account Information.'
            ),
            array(
                'title' => 'System Admin',
                'description' => 'This user can do anything in the system.'
            ),
            array(
                'title' => 'Data Admin',
                'description' => 'This user can create and update any data in the system ( permissions, users, products, categories and etc).'
            ),
            array(
                'title' => 'Product Admin',
                'description' => 'This user can create and update any data in the Products, Product Components, Categories, Tags, Images and etc.'
            ),
            array(
                'title' => 'Sales Officer',
                'description' => 'This user will handle all the sales related task.'
            ),
            array(
                'title' => 'Client',
                'description' => 'This user can use Carts and message a Sales Officer.'
            )
        );
        foreach ($arr as $key => $value) {
            DB::table('roles')->insert( $value );
        }
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
    }
}
