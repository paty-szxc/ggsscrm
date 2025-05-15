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
        Schema::table('survey_projects', function (Blueprint $table) {
            $table->date('date_completed')->nullable()->default(null)->change();
            $table->date('date_approved')->nullable()->default(null)->change();
            $table->date('date_delivered')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('survey_projects', function (Blueprint $table) {
            // Optionally, you can revert the changes if needed
            $table->date('date_completed')->nullable()->default('1970-01-01')->change();
            $table->date('date_approved')->nullable()->default('1970-01-01')->change();
            $table->date('date_delivered')->nullable()->default('1970-01-01')->change();
        });
    }
};
