<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Tournament;
use App\Models\AuctionResult;
use App\Models\TeamPlayer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PlayerController extends Controller
{
    private function getTournament($tournamentId)
    {
        return Tournament::where('id', $tournamentId)
            ->where('user_id', Auth::id())
            ->with(['teams', 'players'])
            ->firstOrFail();
    }

    // ── PUBLIC: Registration Form ──
    public function publicRegister($code)
    {
        $tournament = Tournament::where('code', $code)->firstOrFail();
        if ($tournament->registration_status === 'closed') {
            return view('public.registration-closed', compact('tournament'));
        }
        return view('public.player-register', compact('tournament'));
    }

    // ── PUBLIC: Registration Submit ──
    public function publicStore(Request $request, $code)
    {
        $tournament = Tournament::where('code', $code)->firstOrFail();
        if ($tournament->registration_status === 'closed') {
            return back()->with('error', 'Registration is closed.');
        }

        $request->validate([
            'name'          => 'required|string|max:255',
            'role'          => 'required|in:Batsman,Bowler,All Rounder,Wicket Keeper',
            'mobile'        => 'required|string|max:15',
            'email'         => 'nullable|email',
            'photo'         => 'nullable|image|max:2048',
            'age'           => 'nullable|integer|min:10|max:60',
            'city'          => 'nullable|string|max:100',
            'batting_style' => 'nullable|string|max:100',
            'bowling_style' => 'nullable|string|max:100',
            'experience'    => 'nullable|string|max:100',
            'jersey_number' => 'nullable|string|max:10',
        ]);

        $photo = null;
        if ($request->hasFile('photo')) {
            $photo = \App\Services\CloudinaryService::upload(
                $request->file('photo'), 'auction-xi/players'
            );
        }

        $playerId = $this->generatePlayerId($tournament->id);

        Player::create([
            'tournament_id' => $tournament->id,
            'player_id'     => $playerId,
            'name'          => $request->name,
            'role'          => $request->role,
            'mobile'        => $request->mobile,
            'email'         => $request->email,
            'photo'         => $photo,
            'age'           => $request->age,
            'city'          => $request->city,
            'batting_style' => $request->batting_style,
            'bowling_style' => $request->bowling_style,
            'experience'    => $request->experience,
            'jersey_number' => $request->jersey_number,
            'base_price'    => $tournament->default_base_price ?? 0,
            'status'        => 'approved',
        ]);

        return redirect()->back()->with(
            'success',
            'Registration successful! Your Player ID is: ' . $playerId
        );
    }

    // ── ORGANIZER: Player List ──
    public function index($tournamentId)
    {
        $tournament = $this->getTournament($tournamentId);
        $players    = Player::where('tournament_id', $tournamentId)
                            ->orderByRaw("CAST(SUBSTRING(player_id, 3) AS UNSIGNED) ASC")
                            ->get();

        return view('player.index', compact('tournament', 'players'));
    }

    // ── ORGANIZER: Approve ──
    public function approve($tournamentId, $playerId)
    {
        $this->getTournament($tournamentId);
        $player = Player::where('id', $playerId)->where('tournament_id', $tournamentId)->firstOrFail();
        $player->update(['status' => 'approved']);
        return back()->with('success', 'Player approved!');
    }

    // ── ORGANIZER: Reject ──
    public function reject($tournamentId, $playerId)
    {
        $this->getTournament($tournamentId);
        $player = Player::where('id', $playerId)->where('tournament_id', $tournamentId)->firstOrFail();
        $player->update(['status' => 'pending']);
        return back()->with('success', 'Player set to pending!');
    }

    // ── ORGANIZER: Delete Single Player ──
    public function destroy($tournamentId, $playerId)
    {
        $this->getTournament($tournamentId);

        $player = Player::where('id', $playerId)
                        ->where('tournament_id', $tournamentId)
                        ->firstOrFail();

        // Delete photo from Cloudinary
        \App\Services\CloudinaryService::delete($player->photo);

        // Reverse budget if sold
        if ($player->status === 'sold') {
            $result = AuctionResult::where('player_id', $player->id)
                                   ->where('tournament_id', $tournamentId)
                                   ->first();
            if ($result && $result->team_id) {
                \App\Models\Team::where('id', $result->team_id)
                    ->decrement('spent', $result->sold_price ?? 0);
            }
        }

        AuctionResult::where('player_id', $player->id)->delete();
        TeamPlayer::where('player_id', $player->id)->delete();
        $player->delete();

        return back()->with('success', "Player '{$player->name}' deleted.");
    }

    // ── ORGANIZER: Delete ALL Players ──
    public function destroyAll($tournamentId)
    {
        $tournament = $this->getTournament($tournamentId);

        $players = Player::where('tournament_id', $tournamentId)->get();

        foreach ($players as $player) {
            \App\Services\CloudinaryService::delete($player->photo);
        }

        // Reset team spending
        $tournament->teams()->update(['spent' => 0]);

        AuctionResult::where('tournament_id', $tournamentId)->delete();
        TeamPlayer::whereIn('player_id', $players->pluck('id'))->delete();
        Player::where('tournament_id', $tournamentId)->delete();

        return back()->with('success', 'All players deleted and team budgets reset.');
    }

    // ── ORGANIZER: Reorder (move up/down, swaps player_id strings) ──
    public function reorder(Request $request, $tournamentId)
    {
        $this->getTournament($tournamentId);

        $request->validate([
            'player_db_id' => 'required|integer',
            'direction'    => 'required|in:up,down',
        ]);

        // Get all players ordered numerically by player_id
        $players = Player::where('tournament_id', $tournamentId)
                         ->orderByRaw("CAST(SUBSTRING(player_id, 3) AS UNSIGNED) ASC")
                         ->get();

        $index = $players->search(fn($p) => $p->id === (int) $request->player_db_id);

        if ($index === false) {
            return back()->with('error', 'Player not found.');
        }

        $swapIndex = $request->direction === 'up' ? $index - 1 : $index + 1;

        if ($swapIndex < 0 || $swapIndex >= $players->count()) {
            return back()->with('error', 'Cannot move player further.');
        }

        $playerA = $players[$index];
        $playerB = $players[$swapIndex];

        $pidA = $playerA->player_id;
        $pidB = $playerB->player_id;

        // Swap via temp to avoid unique constraint collision
        DB::transaction(function () use ($playerA, $playerB, $pidA, $pidB) {
            DB::table('players')->where('id', $playerA->id)->update(['player_id' => 'TEMP__' . $playerA->id]);
            DB::table('players')->where('id', $playerB->id)->update(['player_id' => $pidA]);
            DB::table('players')->where('id', $playerA->id)->update(['player_id' => $pidB]);
        });

        return back()->with('success', 'Player order updated.');
    }

    // ── ORGANIZER: Edit Form ──
    public function edit($tournamentId, $playerId)
    {
        $tournament = $this->getTournament($tournamentId);
        $player     = Player::where('id', $playerId)->where('tournament_id', $tournamentId)->firstOrFail();
        return view('player.edit', compact('tournament', 'player'));
    }

    // ── ORGANIZER: Update ──
    public function update(Request $request, $tournamentId, $playerId)
    {
        $this->getTournament($tournamentId);
        $player = Player::where('id', $playerId)->where('tournament_id', $tournamentId)->firstOrFail();

        $request->validate([
            'name'          => 'required|string|max:255',
            'role'          => 'required|in:Batsman,Bowler,All Rounder,Wicket Keeper',
            'mobile'        => 'required|string|max:15',
            'email'         => 'nullable|email',
            'photo'         => 'nullable|image|max:2048',
            'age'           => 'nullable|integer',
            'city'          => 'nullable|string|max:100',
            'batting_style' => 'nullable|string|max:100',
            'bowling_style' => 'nullable|string|max:100',
            'experience'    => 'nullable|string|max:100',
            'jersey_number' => 'nullable|string|max:10',
            'base_price'    => 'nullable|numeric|min:0',
        ]);

        if ($request->hasFile('photo')) {
            \App\Services\CloudinaryService::delete($player->photo);
            $player->photo = \App\Services\CloudinaryService::upload(
                $request->file('photo'), 'auction-xi/players'
            );
        }

        $player->update([
            'name'          => $request->name,
            'role'          => $request->role,
            'mobile'        => $request->mobile,
            'email'         => $request->email,
            'photo'         => $player->photo,
            'age'           => $request->age,
            'city'          => $request->city,
            'batting_style' => $request->batting_style,
            'bowling_style' => $request->bowling_style,
            'experience'    => $request->experience,
            'jersey_number' => $request->jersey_number,
            'base_price'    => $request->base_price ?? 0,
        ]);

        return redirect()->route('player.index', $tournamentId)->with('success', 'Player updated!');
    }

    // ── IMPORT: Form ──
    public function importForm($tournamentId)
    {
        $tournament = $this->getTournament($tournamentId);
        return view('player.import', compact('tournament'));
    }

    // ── IMPORT: CSV ──
    public function importCsv(Request $request, $tournamentId)
    {
        $tournament = $this->getTournament($tournamentId);
        $request->validate(['csv_file' => 'required|file|mimes:csv,txt|max:5120']);

        $file   = $request->file('csv_file');
        $handle = fopen($file->getPathname(), 'r');
        $rawHeader = fgetcsv($handle);

        if (!$rawHeader) {
            fclose($handle);
            return back()->withErrors(['csv_file' => 'CSV file is empty or unreadable.']);
        }

        $header   = array_map(fn($h) => strtolower(trim($h)), $rawHeader);
        $imported = 0;
        $skipped  = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (empty(array_filter($row, fn($v) => trim($v) !== ''))) continue;
            if (count($row) < count($header)) $row = array_pad($row, count($header), '');

            $data = array_combine($header, $row);
            $name = trim($data['player name'] ?? $data['playername'] ?? $data['name'] ?? '');

            if ($name === '') { $skipped++; continue; }

            $role = trim($data['role'] ?? 'Batsman');
            if (!in_array($role, ['Batsman','Bowler','All Rounder','Wicket Keeper'], true)) $role = 'Batsman';

            $mobile       = trim($data['mobile'] ?? $data['phone'] ?? '0000000000');
            $rawPhoto     = trim($data['photo'] ?? $data['image url'] ?? $data['image_url'] ?? $data['image'] ?? $data['drive link'] ?? $data['drive_link'] ?? $data['photo url'] ?? $data['photo_url'] ?? '');
            $imageUrl     = $rawPhoto !== '' ? $this->convertGoogleDriveLink($rawPhoto) : null;
            $email        = trim($data['email'] ?? '') ?: null;
            $ageRaw       = trim($data['age'] ?? '');
            $age          = (is_numeric($ageRaw) && $ageRaw > 0) ? (int)$ageRaw : null;
            $city         = trim($data['city'] ?? '') ?: null;
            $battingStyle = trim($data['batting style'] ?? $data['batting_style'] ?? $data['batting'] ?? '') ?: null;
            $bowlingStyle = trim($data['bowling style'] ?? $data['bowling_style'] ?? $data['bowling'] ?? '') ?: null;
            $experience   = trim($data['experience'] ?? '') ?: null;
            $jerseyNumber = trim($data['jersey number'] ?? $data['jersey_number'] ?? $data['jersey'] ?? '') ?: null;
            $basePriceRaw = trim($data['base price'] ?? $data['base_price'] ?? $data['baseprice'] ?? $data['price'] ?? '');
            $basePrice    = is_numeric($basePriceRaw) ? (float)$basePriceRaw : (float)($tournament->default_base_price ?? 0);

            Player::create([
                'tournament_id' => $tournament->id,
                'player_id'     => $this->generatePlayerId($tournament->id),
                'name'          => $name,
                'role'          => $role,
                'mobile'        => $mobile,
                'email'         => $email,
                'photo'         => null,
                'image_url'     => $imageUrl,
                'age'           => $age,
                'city'          => $city,
                'batting_style' => $battingStyle,
                'bowling_style' => $bowlingStyle,
                'experience'    => $experience,
                'jersey_number' => $jerseyNumber,
                'base_price'    => $basePrice,
                'status'        => 'approved',
            ]);

            $imported++;
        }

        fclose($handle);

        $message = "{$imported} player(s) imported successfully!";
        if ($skipped > 0) $message .= " ({$skipped} row(s) skipped — missing name.)";

        return redirect()->route('player.index', $tournamentId)->with('success', $message);
    }

    // ── HELPER: Generate Unique Player ID per Tournament ──
    private function generatePlayerId(int $tournamentId): string
    {
        $lastPlayer = Player::where('tournament_id', $tournamentId)
                            ->orderByRaw("CAST(SUBSTRING(player_id, 3) AS UNSIGNED) DESC")
                            ->first();

        $newNumber = $lastPlayer ? ((int) substr($lastPlayer->player_id, 2)) + 1 : 1001;
        $playerId  = 'PX' . $newNumber;

        while (Player::where('tournament_id', $tournamentId)->where('player_id', $playerId)->exists()) {
            $newNumber++;
            $playerId = 'PX' . $newNumber;
        }

        return $playerId;
    }

    // ── HELPER: Convert any Google Drive link to direct-embed URL ──
    private function convertGoogleDriveLink(string $url): string
    {
        $url = trim($url);
        if (!str_contains($url, 'drive.google.com')) return $url;

        $fileId = null;
        if (preg_match('#/file/d/([a-zA-Z0-9_-]+)#', $url, $m)) {
            $fileId = $m[1];
        } elseif (preg_match('#[?&]id=([a-zA-Z0-9_-]+)#', $url, $m)) {
            $fileId = $m[1];
        }

        return $fileId ? 'https://lh3.googleusercontent.com/d/' . $fileId . '=w200' : $url;
    }
}