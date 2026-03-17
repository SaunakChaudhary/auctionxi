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
    private function getTournament($tournamentId)
    {
        return Tournament::where('id', $tournamentId)
            ->where('user_id', Auth::id())
            ->with(['teams', 'players'])
            ->firstOrFail();
    }

    public function panel($tournamentId)
    {
        $tournament = $this->getTournament($tournamentId);

        // Only approved/sold/unsold players for THIS tournament
        $players = Player::where('tournament_id', $tournamentId)
            ->whereIn('status', [
                'approved',
                'sold',
                'unsold'
            ])
            ->orderBy('id')
            ->get();

        // Auto-load first approved player
        $firstPlayer = Player::where('tournament_id', $tournamentId)
            ->whereIn('status', [
                'approved',
                'sold',
                'unsold'
            ])
            ->orderBy('id')
            ->first();

        $allPlayerIds = $players->pluck('player_id')->toArray();
        $nextId       = isset($allPlayerIds[1])
            ? $allPlayerIds[1] : null;

        return view('auction.panel', compact(
            'tournament',
            'players',
            'firstPlayer',
            'allPlayerIds',
            'nextId'
        ));
    }

    public function searchPlayer(Request $request, $tournamentId)
    {
        // Verify tournament belongs to organizer
        $tournament = $this->getTournament($tournamentId);

        // Search ONLY within this tournament
        $player = Player::where('tournament_id', $tournamentId)
            ->where('player_id', $request->player_id)
            ->whereIn('status', [
                'approved',
                'sold',
                'unsold'
            ])
            ->first();

        if (!$player) {
            return response()->json([
                'player'        => null,
                'prevPlayerId'  => null,
                'nextPlayerId'  => null,
                'currentIndex'  => 0,
                'totalPlayers'  => 0,
            ]);
        }

        // Ordered list for THIS tournament only
        $allPlayers = Player::where('tournament_id', $tournamentId)
            ->whereIn('status', [
                'approved',
                'sold',
                'unsold'
            ])
            ->orderBy('id')
            ->pluck('player_id')
            ->toArray();

        $currentIndex = array_search(
            $player->player_id,
            $allPlayers
        );

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
        $tournament = $this->getTournament($tournamentId);

        $request->validate([
            'player_id'  => 'required|exists:players,id',
            'team_id'    => 'nullable|exists:teams,id',
            'sold_price' => 'required|numeric|min:0',
            'status'     => 'required|in:sold,unsold',
        ]);

        $player = Player::where('id', $request->player_id)
            ->where('tournament_id', $tournamentId)
            ->firstOrFail();

        // ── BUDGET VALIDATION ──
        if ($request->status === 'sold') {
            $team = Team::where('id', $request->team_id)
                ->where('tournament_id', $tournamentId)
                ->firstOrFail();

            // If same team re-assigning, add back old price
            $existingTP = TeamPlayer::where('player_id', $player->id)
                ->where('team_id', $team->id)
                ->first();

            $oldPrice        = $existingTP
                ? $existingTP->sold_price : 0;
            $availableBudget = ($team->budget - $team->spent)
                + $oldPrice;

            if ($request->sold_price > $availableBudget) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient budget! '
                        . $team->name
                        . ' has ₹'
                        . number_format($availableBudget)
                        . ' available.',
                ], 422);
            }
        }

        // ── REVERSE previous assignment ──
        $existingTeamPlayer = TeamPlayer::where('player_id', $player->id)
            ->first();
        if ($existingTeamPlayer) {
            $oldTeam = Team::find($existingTeamPlayer->team_id);
            if ($oldTeam) {
                $oldTeam->spent = max(
                    0,
                    $oldTeam->spent - $existingTeamPlayer->sold_price
                );
                $oldTeam->save();
            }
            $existingTeamPlayer->delete();
        }

        // ── ASSIGN ──
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
                ? 'Player assigned to '
                . Team::find($request->team_id)->name . '!'
                : 'Player marked as unsold.',
        ]);
    }

    public function updateStatus(Request $request, $tournamentId)
    {
        $tournament = $this->getTournament($tournamentId);
        $tournament->update([
            'auction_status' => $request->status
        ]);
        return back()->with('success', 'Auction status updated!');
    }

    public function results($tournamentId)
    {
        $tournament = $this->getTournament($tournamentId);

        // Results for THIS tournament only
        $results = AuctionResult::where('tournament_id', $tournamentId)
            ->with(['player', 'team'])
            ->latest()
            ->get();

        return view(
            'auction.results',
            compact('tournament', 'results')
        );
    }
}
