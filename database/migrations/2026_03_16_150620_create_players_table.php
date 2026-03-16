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
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->onDelete('cascade');
            $table->string('player_id')->unique();
            $table->string('name');
            $table->enum('role', ['Batsman', 'Bowler', 'All Rounder', 'Wicket Keeper']);
            $table->string('mobile');
            $table->string('email')->nullable();
            $table->string('photo')->nullable();
            $table->integer('age')->nullable();
            $table->string('city')->nullable();
            $table->string('batting_style')->nullable();
            $table->string('bowling_style')->nullable();
            $table->string('experience')->nullable();
            $table->string('jersey_number')->nullable();
            $table->decimal('base_price', 15, 2)->default(0);
            $table->enum('status', ['pending', 'approved', 'sold', 'unsold'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
