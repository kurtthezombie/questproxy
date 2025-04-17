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
        Schema::table('reviews', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->unsignedBigInteger('service_id')->nullable()->change();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Drop foreign and column for rollback
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');

            // Revert service_id to not nullable
            $table->unsignedBigInteger('service_id')->nullable(false)->change();
        });
    }
};
