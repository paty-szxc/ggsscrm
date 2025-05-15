<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{

    public function up(){
        Schema::create('survey_projects', function (Blueprint $table) {
            $table->id();
            $table->date('date_started')->nullable();
            $table->date('date_completed')->nullable();
            $table->string('location')->nullable();
            $table->text('survey_details')->nullable();
            $table->string('area')->nullable()->nullable();
            $table->string('processed_by')->nullable();
            $table->integer('survey')->default(0)->nullable();
            $table->integer('data_process')->default(0)->nullable();
            $table->integer('plans')->default(0)->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_no')->nullable();
            $table->date('date_approved')->nullable();
            $table->date('date_delivered')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(){
        Schema::dropIfExists('survey_projects');
    }
};