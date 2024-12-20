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
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();    
            $table->timestamps();
        });

        Schema::create('gamers', function (Blueprint $table) {
            $table->id();
            $table->string('gamer_preference')->nullable();
            $table->timestamps();
            //fk user_id
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
            //$table->dropForeign('gamers_user_id_foreign');
            $table->dropForeign(['user_id']);
        });

        Schema::table('gamers', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });

        Schema::dropIfExists('gamers');
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
