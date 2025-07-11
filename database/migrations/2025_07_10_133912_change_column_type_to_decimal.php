<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnTypeToDecimal extends Migration
{
    public function up()
    {
        Schema::table('office_supplies', function (Blueprint $table) {
            $table->decimal('item_cost', 12, 2)->nullable()->change();
            $table->unsignedInteger('quantity')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('office_supplies', function (Blueprint $table) {
            $table->string('item_cost')->nullable()->change();
            $table->unsignedInteger('quantity')->nullable()->change();
        });
    }
}
