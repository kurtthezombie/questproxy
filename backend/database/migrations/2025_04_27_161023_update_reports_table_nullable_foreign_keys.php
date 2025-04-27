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
        Schema::table('reports', function (Blueprint $table) {
            // Make foreign key columns nullable
            $table->unsignedBigInteger('reporting_user_id')->nullable()->change();
            $table->unsignedBigInteger('reported_user_id')->nullable()->change();
    
            // Add onDelete constraints
            $table->dropForeign(['reporting_user_id']);
            $table->dropForeign(['reported_user_id']);
    
            $table->foreign('reporting_user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('reported_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // Revert the changes: make the columns non-nullable and remove onDelete
            $table->unsignedBigInteger('reporting_user_id')->nullable(false)->change();
            $table->unsignedBigInteger('reported_user_id')->nullable(false)->change();
    
            $table->dropForeign(['reporting_user_id']);
            $table->dropForeign(['reported_user_id']);
    
            $table->foreign('reporting_user_id')->references('id')->on('users');
            $table->foreign('reported_user_id')->references('id')->on('users');
        });
    }
};
