<?php

namespace App\Http\Controllers;

use App\Models\AuctionResult;
use App\Models\Player;
use App\Models\Team;
use App\Models\TeamPlayer;
use App\Models\Tournament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuctionController extends Controller
{
    public function panel($tournamentId)
    {
        $tournament = Tournament::where('id', $tournamentId)
            ->where('user_id', Auth::id())
            ->with('teams')
            ->firstOrFail();
        $players = $tournament->players()->where('status', 'approved')->get();
        return view('auction.panel', compact('tournament', 'players'));
    }

    public function searchPlayer(Request $request, $tournamentId)
    {
        $tournament = Tournament::where('id', $tournamentId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $player = Player::where('tournament_id', $tournamentId)
            ->where('player_id', $request->player_id)
            ->where('status', 'approved')
            ->first();

        if (!$player) {
            // Also search sold/unsold for edit purposes
            $player = Player::where('tournament_id', $tournamentId)
                ->where('player_id', $request->player_id)
                ->first();
        }

        // Get next and previous approved players
        $allPlayers = Player::where('tournament_id', $tournamentId)
            ->whereIn('status', ['approved', 'sold', 'unsold'])
            ->orderBy('id')
            ->pluck('player_id')
            ->toArray();

        $currentIndex = $player ? array_search($player->player_id, $allPlayers) : -1;

        $prevPlayerId = ($currentIndex > 0)
            ? $allPlayers[$currentIndex - 1] : null;
        $nextPlayerId = ($currentIndex !== false
            && $currentIndex < count($allPlayers) - 1)
            ? $allPlayers[$currentIndex + 1] : null;

        return response()->json([
            'player'        => $player,
            'prevPlayerId'  => $prevPlayerId,
            'nextPlayerId'  => $nextPlayerId,
            'currentIndex'  => $currentIndex + 1,
            'totalPlayers'  => count($allPlayers),
        ]);
    }
    public function assign(Request $request, $tournamentId)
    {
        $tournament = Tournament::where('id', $tournamentId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $request->validate([
            'player_id'  => 'required|exists:players,id',
            'team_id'    => 'nullable|exists:teams,id',
            'sold_price' => 'required|numeric|min:0',
            'status'     => 'required|in:sold,unsold',
        ]);

        $player = Player::findOrFail($request->player_id);

        // ── REVERSE previous assignment if player was already sold ──
        $existingTeamPlayer = TeamPlayer::where('player_id', $player->id)->first();
        if ($existingTeamPlayer) {
            // Subtract old price from old team's spent
            $oldTeam = Team::find($existingTeamPlayer->team_id);
            if ($oldTeam) {
                $oldTeam->spent = max(0, $oldTeam->spent - $existingTeamPlayer->sold_price);
                $oldTeam->save();
            }
            $existingTeamPlayer->delete();
        }

        if ($request->status === 'sold') {
            $team = Team::findOrFail($request->team_id);

            TeamPlayer::create([
                'team_id'       => $team->id,
                'player_id'     => $player->id,
                'tournament_id' => $tournament->id,
                'sold_price'    => $request->sold_price,
            ]);

            $team->increment('spent', $request->sold_price);
            $player->update(['status' => 'sold']);
        } else {
            $player->update(['status' => 'unsold']);
        }

        AuctionResult::updateOrCreate(
            [
                'player_id'     => $player->id,
                'tournament_id' => $tournament->id,
            ],
            [
                'team_id'    => $request->status === 'sold'
                    ? $request->team_id : null,
                'sold_price' => $request->sold_price,
                'status'     => $request->status,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => $request->status === 'sold'
                ? 'Player assigned successfully!'
                : 'Player marked as unsold.',
        ]);
    }
    public function updateStatus(Request $request, $tournamentId)
    {
        $tournament = Tournament::where('id', $tournamentId)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        $tournament->update(['auction_status' => $request->status]);
        return back()->with('success', 'Auction status updated!');
    }

    public function results($tournamentId)
    {
        $tournament = Tournament::where('id', $tournamentId)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        $results = AuctionResult::where('tournament_id', $tournamentId)
            ->with(['player', 'team'])
            ->latest()
            ->get();
        return view('auction.results', compact('tournament', 'results'));
    }
}
