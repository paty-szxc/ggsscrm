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
        Schema::table('quotations', function (Blueprint $table) {
            $table->string('client')->after('id')->nullable();
            $table->string('location')->after('client')->nullable();
            $table->string('attachment')->after('location')->nullable();
            $table->date('date')->after('attachment')->nullable();
            $table->string('revised_attachment')->after('date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn([
                'client',
                'location',
                'attachment',
                'date',
                'revised_attachment'
            ]);
        });
    }
};
