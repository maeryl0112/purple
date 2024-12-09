<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveColumnsFromServicesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['notes', 'cautions', 'benefits', 'aftercare_tips']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('notes')->nullable();
            $table->string('cautions')->nullable();
            $table->string('benefits')->nullable();
            $table->string('aftercare_tips')->nullable();
        });
    }
}
