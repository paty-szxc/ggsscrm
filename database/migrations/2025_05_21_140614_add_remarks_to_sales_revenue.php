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
        Schema::table('sales_revenue', function (Blueprint $table) {
            $table->string('remarks')->nullable()->after('withholding_tax');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_revenue', function (Blueprint $table) {
            $table->dropColumn('remarks');
        });
    }
};
