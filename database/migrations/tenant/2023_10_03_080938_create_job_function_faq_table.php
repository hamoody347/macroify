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
        Schema::create('f_a_q_job_function', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_function_id');
            $table->unsignedBigInteger('f_a_q_id');
            $table->timestamps();

            // Define foreign keys with ON DELETE CASCADE
            $table->foreign('job_function_id')
                ->references('id')
                ->on('job_functions')
                ->onDelete('cascade');

            $table->foreign('f_a_q_id')
                ->references('id')
                ->on('f_a_q_s')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('f_a_q_job_function');
    }
};
