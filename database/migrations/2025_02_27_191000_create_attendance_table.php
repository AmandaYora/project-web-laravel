<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->id('attendance_id');
            $table->foreignId('class_session_id')->constrained('class_sessions', 'class_session_id');
            $table->foreignId('user_id')->constrained('users', 'user_id');
            $table->time('clock_in');
            $table->date('date');
            $table->enum('status', ['present', 'late', 'absent'])->default('absent');
            $table->timestamps();

            // Prevent duplicate attendance
            $table->unique(['class_session_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
