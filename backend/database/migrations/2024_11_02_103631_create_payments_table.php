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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount',10,2);
            $table->string('method');
            $table->string('details')->nullable();
            $table->string('transaction_id');
            $table->string('payment_link');
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('payer_id');
            $table->unsignedBigInteger('service_id');
            $table->timestamps();

            $table->foreign('payer_id')->references('id')->on('users');
            $table->foreign('service_id')->references('id')->on('services');
        });
        
        Schema::create('transaction_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payment_id');
            $table->string('status');
            $table->timestamps();

            $table->foreign('payment_id')->references('id')->on('payments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_history');
        Schema::dropIfExists('payments');
    }
};
