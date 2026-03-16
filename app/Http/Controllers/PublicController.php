<?php

namespace App\Http\Controllers;

use App\Models\AuctionResult;
use App\Models\Player;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function live($code)
    {
        $tournament = Tournament::where('code', $code)
                                ->with(['teams', 'players'])
                                ->firstOrFail();
        $teams   = $tournament->teams()->with('players')->get();
        $results = AuctionResult::where('tournament_id', $tournament->id)
                                ->with(['player', 'team'])
                                ->latest()
                                ->get();
        return view('public.live', compact('tournament', 'teams', 'results'));
    }

    public function searchPlayer(Request $request, $code)
    {
        $tournament = Tournament::where('code', $code)->firstOrFail();
        $player = Player::where('tournament_id', $tournament->id)
                        ->where('player_id', $request->player_id)
                        ->with('team')
                        ->first();
        return response()->json($player);
    }

    public function teamSquad($code, $teamId)
    {
        $tournament = Tournament::where('code', $code)->firstOrFail();
        $team = Team::where('id', $teamId)
                    ->where('tournament_id', $tournament->id)
                    ->with('players')
                    ->firstOrFail();
        return view('public.team-squad', compact('tournament', 'team'));
    }
}