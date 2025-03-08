<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id('task_id');
            $table->foreignId('project_id')->references('project_id')->on('projects')->onDelete('cascade');
            $table->string('task_name');
            $table->text('description')->nullable();
            $table->foreignId('assigned_to')->nullable()->references('user_id')->on('users')->onDelete('set null');
            $table->string('status')->default('Pending');
            $table->decimal('progress', 5, 2)->default(0);
            $table->integer('priority')->default(1); // 1: Low, 2: Medium, 3: High
            $table->integer('weight')->default(1); // Task weight for project progress calculation
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('estimated_hours', 8, 2)->nullable();
            $table->decimal('actual_hours', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('tasks');
    }
};
