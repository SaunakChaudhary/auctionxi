<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Tournament;
use App\Models\Team;
use App\Models\AuctionResult;
use App\Models\TeamPlayer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuctionController extends Controller
{
    private function getTournament($tournamentId)
    {
        return Tournament::where('id', $tournamentId)
            ->where('user_id', Auth::id())
            ->with(['teams', 'players'])
            ->firstOrFail();
    }

    // ── Auction Panel ──
    public function panel($tournamentId)
    {
        $tournament = $this->getTournament($tournamentId);

        // Players ordered by player_id numerically
        $players = Player::where('tournament_id', $tournamentId)
            ->whereIn('status', ['approved', 'sold', 'unsold'])
            ->orderByRaw("CAST(SUBSTRING(player_id, 3) AS UNSIGNED) ASC")
            ->get();

        $firstPlayer   = $players->firstWhere('status', 'approved') ?? $players->first();
        $allPlayerIds  = $players->pluck('player_id')->toArray();

        return view('auction.panel', compact(
            'tournament',
            'players',
            'firstPlayer',
            'allPlayerIds'
        ));
    }

    // ── Search Player — by Player ID OR by Name ──
    public function searchPlayer(Request $request, $tournamentId)
    {
        $this->getTournament($tournamentId);

        $query    = trim($request->query('q', $request->query('player_id', '')));
        $isId     = strtoupper($query);

        // Try exact player_id first
        $player = Player::where('tournament_id', $tournamentId)
            ->where('player_id', $isId)
            ->first();

        // If not found by ID, try name search (partial, case-insensitive)
        if (!$player && $query !== '') {
            $player = Player::where('tournament_id', $tournamentId)
                ->where('name', 'LIKE', '%' . $query . '%')
                ->orderByRaw("CAST(SUBSTRING(player_id, 3) AS UNSIGNED) ASC")
                ->first();
        }

        if (!$player) {
            return response()->json(['player' => null]);
        }

        // Get all players ordered numerically for index
        $allPlayers = Player::where('tournament_id', $tournamentId)
            ->orderByRaw("CAST(SUBSTRING(player_id, 3) AS UNSIGNED) ASC")
            ->get();

        $idx          = $allPlayers->search(fn($p) => $p->id === $player->id);
        $currentIndex = $idx !== false ? $idx + 1 : 1;
        $totalPlayers = $allPlayers->count();

        return response()->json([
            'player'       => $player,
            'currentIndex' => $currentIndex,
            'totalPlayers' => $totalPlayers,
        ]);
    }

    // ── Assign Player ──
    public function assign(Request $request, $tournamentId)
    {
        $tournament = $this->getTournament($tournamentId);

        $request->validate([
            'player_id' => 'required|integer',
            'status'    => 'required|in:sold,unsold',
            'team_id'   => 'nullable|integer',
            'sold_price' => 'nullable|numeric|min:0',
        ]);

        $player = Player::where('id', $request->player_id)
            ->where('tournament_id', $tournamentId)
            ->firstOrFail();

        // Reverse previous assignment if sold
        if ($player->status === 'sold') {
            $prev = AuctionResult::where('player_id', $player->id)
                ->where('tournament_id', $tournamentId)
                ->first();
            if ($prev && $prev->team_id) {
                Team::where('id', $prev->team_id)
                    ->decrement('spent', $prev->sold_price ?? 0);
                TeamPlayer::where('player_id', $player->id)
                    ->where('team_id', $prev->team_id)
                    ->delete();
            }
            AuctionResult::where('player_id', $player->id)
                ->where('tournament_id', $tournamentId)
                ->delete();
        }

        if ($request->status === 'sold') {
            $team = Team::where('id', $request->team_id)
                ->where('tournament_id', $tournamentId)
                ->firstOrFail();

            $remaining = $team->budget - $team->spent;

            if ($request->sold_price > $remaining) {
                return response()->json([
                    'success' => false,
                    'message' => "Budget exceeded! {$team->name} only has ₹" . number_format($remaining) . " remaining.",
                ]);
            }

            // Deduct from team budget
            $team->increment('spent', $request->sold_price);

            // Create team_player record
            TeamPlayer::updateOrCreate(
                ['player_id' => $player->id, 'team_id' => $team->id],
                ['tournament_id' => $tournamentId, 'sold_price' => $request->sold_price]
            );

            // Create auction result
            AuctionResult::create([
                'tournament_id' => $tournamentId,
                'player_id'     => $player->id,
                'team_id'       => $team->id,
                'sold_price'    => $request->sold_price,
                'status'        => 'sold',
            ]);

            $player->update(['status' => 'sold']);

            return response()->json([
                'success' => true,
                'message' => "{$player->name} sold to {$team->name} for ₹" . number_format($request->sold_price),
            ]);
        } else {
            // Unsold
            AuctionResult::create([
                'tournament_id' => $tournamentId,
                'player_id'     => $player->id,
                'team_id'       => null,
                'sold_price'    => 0,
                'status'        => 'unsold',
            ]);

            $player->update(['status' => 'unsold']);

            return response()->json([
                'success' => true,
                'message' => "{$player->name} marked as unsold.",
            ]);
        }
    }

    // ── Update Auction Status ──
    public function updateStatus(Request $request, $tournamentId)
    {
        $tournament = $this->getTournament($tournamentId);
        $request->validate(['status' => 'required|in:pending,live,completed']);
        $tournament->update(['auction_status' => $request->status]);
        return back()->with('success', 'Auction status updated.');
    }

    // ── Auction Results ──
    public function results($tournamentId)
    {
        $tournament = $this->getTournament($tournamentId);
        $results    = AuctionResult::where('tournament_id', $tournamentId)
            ->with(['player', 'team'])
            ->latest()
            ->get();

        return view('auction.results', compact('tournament', 'results'));
    }
}
