<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('survey_project_files', function (Blueprint $table) {
            $table->id();
            $table->integer('survey_project_id');
            $table->string('original_name'); // The name of the file as uploaded by the user
            $table->string('stored_name')->unique(); // The unique name given to the file on storage
            $table->string('path'); // The path where the file is stored (e.g., 'uploads/filename.ext')
            $table->string('mime_type')->nullable(); // The MIME type of the file
            $table->unsignedBigInteger('size'); // The size of the file in bytes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_project_files');
    }
};
