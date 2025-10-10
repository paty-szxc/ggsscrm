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
        Schema::table('construction_projects', function (Blueprint $table) {
            $table->string('permit_duration')->nullable()->after('end_process');
            $table->string('construction_duration')->nullable()->after('end_actual');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('construction_projects', function (Blueprint $table) {
        });
    }
};
