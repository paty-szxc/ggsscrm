<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{
    public function up(){
        Schema::create('sales_revenue', function (Blueprint $table) {
            $table->id();
            $table->date('date_of_survey')->nullable();
            $table->string('location')->nullable();
            $table->string('type_of_survey')->nullable();
            $table->decimal('expenses', 10, 2)->nullable();
            $table->string('receipt_no')->nullable();
            $table->decimal('project_cost', 10, 2)->nullable();
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
            $table->string('withholding_tax')->nullable();
            $table->date('fully_paid_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(){
        Schema::dropIfExists('sales_revenue');
    }
};