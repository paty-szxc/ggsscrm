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
            $table->date('data_process')->nullable()->change();
            $table->date('plans')->nullable()->change();

            $table->string('surveyed_by')->nullable()->after('processed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('survey_projects', function (Blueprint $table) {
            $table->dropColumn('surveyed_by');
        });
    }
};
