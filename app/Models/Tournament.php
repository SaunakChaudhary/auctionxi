<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Tournament extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'code',
        'number_of_teams',
        'team_budget',
        'default_base_price',
        'location',
        'start_date',
        'auction_date',
        'description',
        'registration_status',
        'auction_status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    public function players()
    {
        return $this->hasMany(Player::class);
    }

    public function auctionResults()
    {
        return $this->hasMany(AuctionResult::class);
    }
}
