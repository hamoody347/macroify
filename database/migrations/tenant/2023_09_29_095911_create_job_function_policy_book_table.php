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
        Schema::create('job_function_policy_book', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('policy_book_id');
            $table->unsignedBigInteger('job_function_id');
            $table->timestamps();

            $table->foreign('policy_book_id')->references('id')->on('policy_books')->onDelete('cascade');
            $table->foreign('job_function_id')->references('id')->on('job_functions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_function_policy_book');
    }
};
