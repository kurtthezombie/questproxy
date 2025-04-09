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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('game')->nullable(false);
            $table->string('title');
            $table->string('category_type');
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('game');
            $table->text('description');
            $table->float('price');
            $table->dateTime('duration');
            $table->boolean('availability');
            $table->timestamps();

            $table->unsignedBigInteger('pilot_id');
            $table->unsignedBigInteger('category_id')->nullable();

            $table->foreign('pilot_id')->references('id')->on('pilots')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropForeign(['pilot_id']);
            $table->dropForeign(['category_id']);
        });

        Schema::dropIfExists('services');
        Schema::dropIfExists('categories');   
    }
};
