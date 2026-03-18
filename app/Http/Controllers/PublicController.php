<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\Player;
use App\Models\Team;
use App\Models\AuctionResult;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    // ── Live Auction Page — shows teams & budgets only ──
    public function live($code)
    {
        $tournament = Tournament::where('code', $code)
                                ->with(['teams.players'])
                                ->firstOrFail();

        $teams = Team::where('tournament_id', $tournament->id)
                     ->with('players')
                     ->orderBy('name')
                     ->get();

        // All approved players ordered for "first player" display
        $players = Player::where('tournament_id', $tournament->id)
                         ->whereIn('status', ['approved', 'sold', 'unsold'])
                         ->orderByRaw("CAST(SUBSTRING(player_id, 3) AS UNSIGNED) ASC")
                         ->get();

        $firstApproved = $players->firstWhere('status', 'approved');
        $allPlayerIds  = $players->pluck('player_id')->toArray();

        // Stats
        $soldCount   = $players->where('status', 'sold')->count();
        $totalPlayers = $players->count();

        return view('public.live', compact(
            'tournament', 'teams', 'players',
            'firstApproved', 'allPlayerIds',
            'soldCount', 'totalPlayers'
        ));
    }

    // ── Player Search Page — separate route ──
    public function searchPage($code)
    {
        $tournament = Tournament::where('code', $code)->firstOrFail();

        $players = Player::where('tournament_id', $tournament->id)
                         ->whereIn('status', ['approved', 'sold', 'unsold'])
                         ->orderByRaw("CAST(SUBSTRING(player_id, 3) AS UNSIGNED) ASC")
                         ->get();

        $firstApproved = $players->firstWhere('status', 'approved') ?? $players->first();
        $allPlayerIds  = $players->pluck('player_id')->toArray();

        return view('public.search', compact(
            'tournament', 'players', 'firstApproved', 'allPlayerIds'
        ));
    }

    // ── AJAX: Search Player (used by both live and search pages) ──
    // Supports search by player_id or by name
    public function searchPlayer(Request $request, $code)
    {
        $tournament = Tournament::where('code', $code)->firstOrFail();

        $query = trim($request->query('q', $request->query('player_id', '')));

        if ($query === '') {
            return response()->json(null);
        }

        // Try exact player_id
        $player = Player::where('tournament_id', $tournament->id)
                        ->where('player_id', strtoupper($query))
                        ->first();

        // Fallback: partial name search
        if (!$player) {
            $player = Player::where('tournament_id', $tournament->id)
                            ->where('name', 'LIKE', '%' . $query . '%')
                            ->orderByRaw("CAST(SUBSTRING(player_id, 3) AS UNSIGNED) ASC")
                            ->first();
        }

        if (!$player) {
            return response()->json(null);
        }

        // Return player with index info for prev/next
        $allPlayers   = Player::where('tournament_id', $tournament->id)
                               ->orderByRaw("CAST(SUBSTRING(player_id, 3) AS UNSIGNED) ASC")
                               ->get();

        $idx          = $allPlayers->search(fn($p) => $p->id === $player->id);
        $currentIndex = $idx !== false ? $idx : 0;

        return response()->json([
            ...$player->toArray(),
            '_index'       => $currentIndex,
            '_total'       => $allPlayers->count(),
            '_all_ids'     => $allPlayers->pluck('player_id')->toArray(),
        ]);
    }

    // ── Team Squad (public) ──
    public function teamSquad($code, $teamId)
    {
        $tournament = Tournament::where('code', $code)->firstOrFail();
        $team       = Team::where('id', $teamId)
                          ->where('tournament_id', $tournament->id)
                          ->with('players')
                          ->firstOrFail();

        return view('public.team-squad', compact('tournament', 'team'));
    }
}