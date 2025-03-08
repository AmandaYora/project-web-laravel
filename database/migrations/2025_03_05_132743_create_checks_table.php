<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChecksTable extends Migration
{
    public function up()
    {
        Schema::create('checks', function (Blueprint $table) {
            $table->id('check_id');
            
            $table->string('name');
            $table->string('description')->nullable();
            $table->json('extra')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('checks');
    }
}
