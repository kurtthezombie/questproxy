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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id'); //user who booked
            $table->unsignedBigInteger('service_id'); //service booked
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('users');
            $table->foreign('service_id')->references('id')->on('services');
        });

        Schema::create('booking_instructions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id'); //where this instruction belongs to
            $table->text('additional_notes')->nullable();
            $table->string('credentials_username');
            $table->string('credentials_password');
            $table->timestamps();

            $table->foreign('booking_id')->references('id')
            ->on('bookings')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_instructions');
        Schema::dropIfExists('bookings');
    }
};
