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
        Schema::table('company_vehicle', function (Blueprint $table) {
            $table->dropColumn('property_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_vehicle', function (Blueprint $table) {
            $table->enum('property_type', ['company', 'personal'])->default('company')->after('id');
        });
    }
};
