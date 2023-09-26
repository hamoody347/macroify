<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('job_function_s_o_p', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_function_id');
            $table->unsignedBigInteger('s_o_p_id');
            $table->timestamps();

            // Define foreign keys
            $table->foreign('job_function_id')->references('id')->on('job_functions');
            $table->foreign('s_o_p_id')->references('id')->on('s_o_p_s');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_function_s_o_p');
    }
};
