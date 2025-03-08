<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('documents', function (Blueprint $table) {
            $table->id('document_id');
            $table->foreignId('project_id')->references('project_id')->on('projects')->onDelete('cascade');
            $table->string('document_name')->nullable();
            $table->string('file_path');
            $table->foreignId('uploaded_by')->nullable()->references('user_id')->on('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('documents');
    }
};
