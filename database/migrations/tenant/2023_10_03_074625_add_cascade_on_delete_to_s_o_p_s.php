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
        Schema::table('job_function_s_o_p', function (Blueprint $table) {
            // Remove the existing foreign key constraint
            $table->dropForeign(['job_function_id']);
            $table->dropForeign(['s_o_p_id']);

            $table->foreign('job_function_id')->references('id')->on('job_functions')->onDelete('cascade');
            $table->foreign('s_o_p_id')->references('id')->on('s_o_p_s')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_function_s_o_p', function (Blueprint $table) {
            //
        });
    }
};
