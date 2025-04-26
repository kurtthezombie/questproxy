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
        Schema::table('payments', function (Blueprint $table) {
            // Make the columns nullable
            $table->string('transaction_id')->nullable()->change();
            $table->string('payment_link')->nullable()->change();
            $table->string('method')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Reverse the nullable changes (make them non-nullable)
            $table->string('transaction_id')->nullable(false)->change();
            $table->string('payment_link')->nullable(false)->change();
            $table->string('method')->nullable(false)->change();
        });
    }
};
