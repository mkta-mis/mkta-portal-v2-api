<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApiTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_tokens', function (Blueprint $table) {
            
            $table->id();

            $table->string('tokenString');
            $table->string('tokenType')->comment("See model for Token Types");
            $table->integer('doesExpire')->default(1)->comment(" 0 - False | 1 - True ");
            $table->integer('reference_ID')->default(0);
            $table->dateTime('tokenExpiration')->nullable();

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
        Schema::dropIfExists('api_tokens');
    }
}
