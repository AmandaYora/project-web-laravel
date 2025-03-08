<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAtmsTable extends Migration
{
    public function up()
    {
        Schema::create('atms', function (Blueprint $table) {
            $table->id('atm_id');
            
            $table->string('code');
            $table->string('name');
            $table->string('alamat');
            $table->string('description')->nullable();
            $table->json('extra')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('atms');
    }
}
