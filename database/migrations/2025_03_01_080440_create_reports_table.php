<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('reports', function (Blueprint $table) {
            $table->id('report_id');
            $table->foreignId('project_id')->references('project_id')->on('projects')->onDelete('cascade');
            $table->string('report_name')->nullable();
            $table->string('report_file_path');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('reports');
    }
};
