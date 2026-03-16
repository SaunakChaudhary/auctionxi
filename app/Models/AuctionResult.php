<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuctionResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'tournament_id', 'player_id', 'team_id',
        'sold_price', 'status'
    ];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}