<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionSetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permission_sets', function (Blueprint $table) {
            $table->id();
            $table->integer('type')->default(0);
            $table->integer('reference_ID')->default(0);
            $table->string('key')->default(0)->comment("Foreign Key for Permission Table");
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
        Schema::dropIfExists('permission_sets');
    }
}
