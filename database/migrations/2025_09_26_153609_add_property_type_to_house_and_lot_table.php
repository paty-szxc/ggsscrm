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
        Schema::table('house_and_lot', function (Blueprint $table) {
            // $table->enum('property_type', ['company', 'personal'])->default('company')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('house_and_lot', function (Blueprint $table) {
            //
        });
    }
};
