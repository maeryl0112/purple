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
        // Add roles column to users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->constrained()->onDelete('set null');
        });

        // Add roles column to employees table
        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->constrained()->onDelete('set null');
        });

        Schema::table('equipment', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->constrained()->onDelete('set null');
        });

        Schema::table('supplies', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->constrained()->onDelete('set null');
        });


       
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['branch_id']); // Drop the foreign key constraint
            $table->dropColumn('branch_id');   // Drop the column
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['branch_id']); // Drop the foreign key constraint
            $table->dropColumn('branch_id');   // Drop the column
        });

        Schema::table('equipment', function (Blueprint $table) {
            $table->dropForeign(['branch_id']); // Drop the foreign key constraint
            $table->dropColumn('branch_id');   // Drop the column
        });

        Schema::table('supplies', function (Blueprint $table) {
            $table->dropForeign(['branch_id']); // Drop the foreign key constraint
            $table->dropColumn('branch_id');   // Drop the column
        });



    }
};
