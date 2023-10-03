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
        Schema::table('policies', function (Blueprint $table) {
            // Remove the existing foreign key constraint
            $table->dropForeign(['policy_book_id']);

            // Add the foreign key with the cascade on delete constraint
            $table->foreign('policy_book_id')
                ->references('id')
                ->on('policy_books')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('policies', function (Blueprint $table) {
            //
        });
    }
};
