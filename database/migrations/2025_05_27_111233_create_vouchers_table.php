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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_no')->nullable();
            $table->date('date')->nullable();
            $table->string('suppliers_name')->nullable();
            $table->string('address_brgy_city')->nullable();
            $table->string('status_of_vat')->nullable();
            $table->string('tin')->nullable();
            $table->text('particulars')->nullable();
            $table->decimal('employee_salary', 12, 2)->default(0)->nullable();
            $table->decimal('employee_benefits', 12, 2)->default(0)->nullable();
            $table->decimal('meals_office_survey', 12, 2)->default(0)->nullable();
            $table->decimal('dog_food', 12, 2)->default(0)->nullable();
            $table->decimal('construction_survey_supplies', 12, 2)->default(0)->nullable();
            $table->decimal('repairs_maintenance', 12, 2)->default(0)->nullable();
            $table->decimal('office_supplies', 12, 2)->default(0)->nullable();
            $table->decimal('gasoline_oil', 12, 2)->default(0)->nullable();
            $table->decimal('utilities', 12, 2)->default(0)->nullable();
            $table->decimal('parking_fee', 12, 2)->default(0)->nullable();
            $table->decimal('toll_fee', 12, 2)->default(0)->nullable();
            $table->decimal('permits_certification_tax', 12, 2)->default(0)->nullable();
            $table->decimal('transportation', 12, 2)->default(0)->nullable();
            $table->decimal('budget', 12, 2)->default(0)->nullable();
            $table->decimal('representation_expense_personal', 12, 2)->default(0)->nullable();
            $table->decimal('others_staff_personal', 12, 2)->default(0)->nullable();
            $table->decimal('amount_gross_of_vat', 12, 2)->default(0)->nullable();
            $table->decimal('net_of_vat', 12, 2)->default(0)->nullable();
            $table->decimal('vat', 12, 2)->default(0)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
