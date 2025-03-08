<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mapel', function (Blueprint $table) {
            $table->id('mapel_id');
            $table->string('day');
            $table->foreignId('subject_id')->constrained('subjects', 'subject_id');
            $table->foreignId('jurusan_id')->constrained('jurusan', 'jurusan_id');
            $table->foreignId('class_id')->constrained('classes', 'class_id');
            $table->time('start_time');
            $table->time('end_time');
            $table->date('date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mapel');
    }
};
