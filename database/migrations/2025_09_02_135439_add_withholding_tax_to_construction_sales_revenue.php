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
        Schema::table('construction_sales_revenue', function (Blueprint $table) {
            $table->string('withholding_tax')->nullable()->after('fully_paid_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('construction_sales_revenue', function (Blueprint $table) {
            $table->dropColumn('withholding_tax');
        });
    }
};
