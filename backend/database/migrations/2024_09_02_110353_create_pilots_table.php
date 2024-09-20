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
        Schema::create('ranking', function (Blueprint $table) {
            $table->id();
            $table->string('pilot_rank')->nullable();
            $table->float('points')->default(0); 
            $table->timestamps();
        });

        Schema::create('pilots', function (Blueprint $table) {
            $table->id();
            $table->string('skills')->nullable()->default("N/A");
            $table->string('bio')->nullable()->default("N/A");
            $table->timestamps();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('rank_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('rank_id')->references('id')->on('ranking');

            $table->unique('rank_id');
        });

        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();
            $table->string('p_content');
            
            $table->unsignedBigInteger('pilot_id');
            $table->foreign('pilot_id')->references('id')->on('pilots')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //drop fk in portfolios:
        Schema::table('portfolios', function (Blueprint $table) {
            $table->dropForeign(['pilot_id']);
        });

        Schema::table('portfolios', function (Blueprint $table) {
            $table->dropColumn('pilot_id');
        });
        //drop fk in pilots:
        Schema::table('pilots', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['rank_id']);
        });
        //drop columns in pilots:
        Schema::table('pilots', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn('rank_id');
        });

        //drop tables:
        Schema::dropIfExists('portfolios');
        Schema::dropIfExists('ranking');
        Schema::dropIfExists('pilots');
    }
};
