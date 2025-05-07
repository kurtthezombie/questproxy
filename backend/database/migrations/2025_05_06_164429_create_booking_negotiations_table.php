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
        Schema::create('booking_negotiations', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key

            // Foreign key to link to the bookings table
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');

            // Store the proposed negotiable price
            $table->decimal('negotiable_price', 8, 2); // Not nullable, must have a price if a negotiation record exists

            // Track the pilot's decision status for this negotiation
            // 'pending': Negotiation proposed, waiting for pilot decision
            // 'approved': Pilot approved the negotiable price
            // 'declined': Pilot declined the negotiable price
            $table->string('pilot_status')->default('pending');

            // Store the final agreed price (will be the negotiable_price if approved)
            $table->decimal('final_price', 8, 2)->nullable(); // Nullable initially, set upon approval

            $table->timestamps(); // created_at and updated_at

            // Ensure only one negotiation record exists per booking
            $table->unique('booking_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_negotiations');
    }
};
