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
        Schema::create('survey_equipments', function (Blueprint $table) {
            $table->id();
            $table->string('description')->nullable();
            $table->decimal('price', 12, 2)->default(0)->nullable();
            $table->string('qty')->nullable();
            $table->string('serial_no')->nullable();
            $table->date('date_supplied')->nullable();
            $table->string('status')->nullable();
            $table->string('recos')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_equipments');
    }
};
