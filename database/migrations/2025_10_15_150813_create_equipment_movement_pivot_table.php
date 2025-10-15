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
        Schema::create('equipment_movement_pivot', function (Blueprint $table) {
           // 1. Foreign Key for the Equipment Movement
            $table->foreignId('equipment_movement_id')
                    ->constrained('survey_equipment_movements') // Assumes the main table is renamed
                        ->onDelete('cascade');

            // 2. Foreign Key for the Survey Equipment
            $table->foreignId('survey_equipment_id')
                    ->constrained('survey_equipments') // Links to your existing table
                        ->onDelete('cascade');

            // 3. Define the Composite Primary Key
            // This ensures a piece of equipment is only linked to a movement once.
            $table->primary(['equipment_movement_id', 'survey_equipment_id']);
            
            // 4. Timestamps (optional, but good practice for tracking when the link was made)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_movement_pivot');
    }
};
