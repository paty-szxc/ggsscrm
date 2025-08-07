<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_registers', function (Blueprint $table) {
            $table->string('emp_code')->after('id');
            $table->string('role')->after('password');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_registers', function (Blueprint $table) {
            $table->dropColumn('emp_code');
            $table->dropColumn('role');
        });
    }
}; 