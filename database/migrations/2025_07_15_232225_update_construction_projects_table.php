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
        Schema::table('construction_projects', function (Blueprint $table) {
            //remove columns
            $table->dropColumn(['client', 'type_of_plan_survey', 'duration']);

            //add new columns
            $table->text('particulars')->nullable()->after('location');
            $table->string('area')->nullable()->after('particulars');
            $table->string('processed_by')->nullable()->after('area');
            $table->integer('start_process')->default(0)->nullable()->after('processed_by');
            $table->integer('end_process')->default(0)->nullable()->after('start_process');
            $table->integer('start_actual')->default(0)->nullable()->after('end_process');
            $table->integer('end_actual')->default(0)->nullable()->after('start_actual');
            $table->string('contact_person')->nullable()->after('end_actual');
            $table->string('contact_no')->nullable()->after('contact_person');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('construction_projects', function (Blueprint $table) {
            $table->string('client')->nullable();
            $table->string('type_of_plan_survey')->nullable();
            $table->string('duration')->nullable();
            
            // Remove new columns
            $table->dropColumn([
                'particulars',
                'area',
                'processed_by',
                'start_process',
                'start_actual',
                'end_process',
                'end_actual',
                'contact_person',
                'contact_no'
            ]);
        });
    }
};
