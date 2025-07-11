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
        Schema::table('office_supplies', function (Blueprint $table) {
            $table->string('item_cost')->nullable()->change();
            $table->string('quantity')->nullable()->change();
            $table->string('unit')->nullable()->change();
            $table->string('care_of')->nullable()->change();
            $table->string('remarks')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('office_supplies', function (Blueprint $table) {
            $table->string('item_cost')->nullable(false)->change();
            $table->string('quantity')->nullable(false)->change();
            $table->string('unit')->nullable(false)->change();
            $table->string('care_of')->nullable(false)->change();
            $table->string('remarks')->nullable(false)->change();
        });
    }
};
