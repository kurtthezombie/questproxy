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
        //still not migrated, wip
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('f_name');
            $table->string('l_name');
            $table->string('contact_number',15);
            $table->char('status')->default('x');
            $table->char('role');
            $table->rememberToken();    
            $table->timestamps();
        });

        Schema::create('gamers', function (Blueprint $table) {
            $table->id();
            $table->string('gamer_preference')->nullable();
            //fk user_id
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
        });


        Schema::create('ranking', function (Blueprint $table) {
            $table->id();
            $table->string('pilot_rank')->nullable();
            $table->float('points')->default(0); 
        });

        Schema::create('pilots', function (Blueprint $table) {
            $table->id();
            $table->string('skills')->nullable()->default("N/A");
            $table->string('bio')->nullable()->default("N/A");
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('rank_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('rank_id')->references('id')->on('ranking');
        });


        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gamers', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::table('pilots', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['rank_id']);
        });
        
        Schema::dropIfExists('gamers');
        Schema::dropIfExists('pilots');
        Schema::dropIfExists('ranking');
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
