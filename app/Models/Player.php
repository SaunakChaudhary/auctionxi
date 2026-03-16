<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $fillable = [
        'tournament_id',
        'player_id',
        'name',
        'role',
        'mobile',
        'email',
        'photo',
        'image_url',
        'age',
        'city',
        'batting_style',
        'bowling_style',
        'experience',
        'jersey_number',
        'base_price',
        'status'
    ];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function teamPlayer()
    {
        return $this->hasOne(TeamPlayer::class);
    }

    public function auctionResult()
    {
        return $this->hasOne(AuctionResult::class);
    }

    public function team()
    {
        return $this->belongsToMany(Team::class, 'team_players')
            ->withPivot('sold_price')
            ->withTimestamps();
    }
}
