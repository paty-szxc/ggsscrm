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
        Schema::create('survey_titles', function (Blueprint $table) {
            $table->id();
            $table->date('date_of_survey')->nullable();
            $table->string('client')->nullable();
            $table->string('type_of_survey')->nullable();
            $table->json('lot_plan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_titles');
    }
};
