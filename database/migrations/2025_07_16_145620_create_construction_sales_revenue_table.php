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
        Schema::create('construction_sales_revenue', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->string('client_name_address')->nullable();
            $table->string('particulars')->nullable();
            $table->string('status_of_vat')->nullable();
            $table->string('receipt_no')->nullable();
            $table->decimal('project_cost', 10, 2)->nullable();
            $table->decimal('amount_gross_of_vat', 12, 2)->default(0)->nullable();
            $table->decimal('net_of_vat', 12, 2)->default(0)->nullable();
            $table->decimal('vat', 12, 2)->default(0)->nullable();
            $table->date('first_date_of_collection')->nullable();
            $table->decimal('first_collection', 10, 2)->nullable();
            $table->date('second_date_of_collection')->nullable();
            $table->decimal('second_collection', 10, 2)->nullable();
            $table->date('third_date_of_collection')->nullable();
            $table->decimal('third_collection', 10, 2)->nullable();
            $table->date('fourth_date_of_collection')->nullable();
            $table->decimal('fourth_collection', 10, 2)->nullable();
            $table->decimal('total', 10, 2)->nullable();
            $table->decimal('receivable_bal', 10, 2)->nullable();
            $table->string('others')->nullable();
            $table->string('remarks')->nullable();
            $table->date('fully_paid_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('construction_sales_revenue');
    }
};
