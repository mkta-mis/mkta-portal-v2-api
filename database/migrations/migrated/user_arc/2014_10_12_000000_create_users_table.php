<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->integer('isClient')->default(0)->comment("0 - False | 1 - True");
            $table->integer('isActive')->default(0)->comment("0 - False | 1 - True");
            $table->timestamps();
        });
        DB::table('users')->insert(
            array(
                'name' => 'John Ricky Lapitan',
                'email' => 'johnrickyl@mkthemedattractions.com.ph',
                'password' => '$2y$10$5Y7H1Mnmxm5wECmvYW8.rucp7cwJA/0z1pTVvXROGslrrRN6/I/yi',
                'isClient' => 0,
                'isActive' => 1
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
