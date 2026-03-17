<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixPlayerIdUniqueConstraint extends Migration
{
    public function up(): void
    {
        Schema::table('players', function (Blueprint $table) {
            // Drop old global unique constraint
            $table->dropUnique('players_player_id_unique');

            // Add composite unique: player_id unique per tournament
            $table->unique(
                ['tournament_id', 'player_id'],
                'players_tournament_player_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropUnique('players_tournament_player_unique');
            $table->unique('player_id');
        });
    }
}