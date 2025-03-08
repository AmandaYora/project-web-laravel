<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitiesTable extends Migration
{
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id('activity_id');
            
            $table->foreignId('task_id')->constrained('tasks', 'task_id')->onDelete('cascade');
            $table->string('status');
            $table->date('date');
            $table->time('time');
            $table->string('evidence');
            $table->string('description')->nullable();
            $table->json('extra')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('activities');
    }
}
