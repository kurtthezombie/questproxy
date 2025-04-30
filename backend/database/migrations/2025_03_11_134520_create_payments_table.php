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
            $table->unsignedBigInteger('payer_id'); 
            $table->unsignedBigInteger('booking_id');
            $table->decimal('amount',10,2);
            $table->string('method')->nullable();
            $table->string('details')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('payment_link')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->foreign('payer_id')->references('id')->on('users');
            $table->foreign('booking_id')->references('id')->on('bookings');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
