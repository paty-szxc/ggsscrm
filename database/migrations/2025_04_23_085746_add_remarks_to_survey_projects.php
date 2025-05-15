<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(){
        Schema::table('survey_projects', function (Blueprint $table) {
            $table->text('remarks')->nullable()->after('plans'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('survey_projects', function (Blueprint $table) {
            $table->dropColumn('remarks');
        });
    }
};
