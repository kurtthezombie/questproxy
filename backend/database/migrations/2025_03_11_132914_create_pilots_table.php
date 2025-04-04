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
        Schema::create('pilots', function (Blueprint $table) {
            $table->id();
            $table->string('skills')->nullable()->default("N/A");
            $table->string('bio')->nullable()->default("N/A");
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('rank_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('rank_id')->references('id')->on('ranking');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pilots');
    }
};
