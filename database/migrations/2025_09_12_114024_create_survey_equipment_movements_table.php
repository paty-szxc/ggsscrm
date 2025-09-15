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
        Schema::create('survey_equipment_movements', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->string('site')->nullable();
            $table->string('handled_by')->nullable();
            $table->date('incoming_date')->nullable();
            $table->date('outgoing_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_equipment_movements');
    }
};