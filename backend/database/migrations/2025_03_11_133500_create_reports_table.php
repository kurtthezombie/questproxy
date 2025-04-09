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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('reason');
            $table->string('status');
            $table->timestamps();
            $table->unsignedBigInteger('reporting_user_id');
            $table->unsignedBigInteger('reported_user_id');

            $table->foreign('reporting_user_id')->references('id')->on('users');
            $table->foreign('reported_user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
