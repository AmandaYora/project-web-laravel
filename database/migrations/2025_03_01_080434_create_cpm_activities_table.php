<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('cpm_activities', function (Blueprint $table) {
            $table->id('activity_id');
            $table->foreignId('project_id')->references('project_id')->on('projects')->onDelete('cascade');
            $table->string('activity_name');
            $table->integer('duration');
            $table->string('predecessors')->nullable();
            $table->integer('early_start')->default(0);
            $table->integer('early_finish')->default(0);
            $table->integer('late_start')->default(0);
            $table->integer('late_finish')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('cpm_activities');
    }
};
