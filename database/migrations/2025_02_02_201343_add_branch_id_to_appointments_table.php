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
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('branch_id')
                  ->nullable()
                  ->constrained()
                  ->onDelete('set null'); // This handles the case when a branch is deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['branch_id']); // Drop the foreign key constraint
            $table->dropColumn('branch_id');   // Drop the column
        });
    }
};
