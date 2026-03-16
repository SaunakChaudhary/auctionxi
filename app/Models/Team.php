<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'tournament_id', 'name', 'logo',
        'owner_name', 'owner_mobile', 'owner_email',
        'budget', 'spent', 'description'
    ];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function teamPlayers()
    {
        return $this->hasMany(TeamPlayer::class);
    }

    public function players()
    {
        return $this->belongsToMany(Player::class, 'team_players')
                    ->withPivot('sold_price')
                    ->withTimestamps();
    }

    public function auctionResults()
    {
        return $this->hasMany(AuctionResult::class);
    }

    public function getRemainingBudgetAttribute()
    {
        return $this->budget - $this->spent;
    }
}