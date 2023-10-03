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
        Schema::create('job_function_wiki', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_function_id');
            $table->unsignedBigInteger('wiki_id');
            $table->timestamps();

            // Define foreign keys with ON DELETE CASCADE
            $table->foreign('job_function_id')
                ->references('id')
                ->on('job_functions')
                ->onDelete('cascade');

            $table->foreign('wiki_id')
                ->references('id')
                ->on('wikis')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_function_wiki');
    }
};
