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
            $table->string('duration')->nullable()->after('date_completed');

            $table->date('start_process')->nullable()->change();
            $table->date('end_process')->nullable()->change();
            $table->date('start_actual')->nullable()->change();
            $table->date('end_actual')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('construction_projects', function (Blueprint $table) {
            $table->dropColumn('duration');
        });
    }
};
