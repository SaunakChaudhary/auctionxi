<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Tournament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
        $tournament = Tournament::where('code', $code)
            ->firstOrFail();
        if ($tournament->registration_status === 'closed') {
            return view(
                'public.registration-closed',
                compact('tournament')
            );
        }
        return view(
            'public.player-register',
            compact('tournament')
        );
    }

    // ── PUBLIC: Registration Submit ──
    public function publicStore(Request $request, $code)
    {
        $tournament = Tournament::where('code', $code)
            ->firstOrFail();

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
            $photo = $request->file('photo')
                ->store('player_photos', 'public');
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
            'status'        => 'approved', // AUTO APPROVE
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

        // Only show players for THIS tournament
        $players = Player::where('tournament_id', $tournamentId)
            ->latest()
            ->get();

        return view('player.index', compact('tournament', 'players'));
    }

    // ── ORGANIZER: Approve ──
    public function approve($tournamentId, $playerId)
    {
        $tournament = $this->getTournament($tournamentId);
        $player = Player::where('id', $playerId)
            ->where('tournament_id', $tournamentId)
            ->firstOrFail();
        $player->update(['status' => 'approved']);
        return back()->with('success', 'Player approved!');
    }

    // ── ORGANIZER: Reject ──
    public function reject($tournamentId, $playerId)
    {
        $tournament = $this->getTournament($tournamentId);
        $player = Player::where('id', $playerId)
            ->where('tournament_id', $tournamentId)
            ->firstOrFail();
        $player->update(['status' => 'pending']);
        return back()->with('success', 'Player set to pending!');
    }

    // ── ORGANIZER: Edit Form ──
    public function edit($tournamentId, $playerId)
    {
        $tournament = $this->getTournament($tournamentId);
        $player = Player::where('id', $playerId)
            ->where('tournament_id', $tournamentId)
            ->firstOrFail();
        return view('player.edit', compact('tournament', 'player'));
    }

    // ── ORGANIZER: Update ──
    public function update(Request $request, $tournamentId, $playerId)
    {
        $tournament = $this->getTournament($tournamentId);
        $player = Player::where('id', $playerId)
            ->where('tournament_id', $tournamentId)
            ->firstOrFail();

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
            if ($player->photo) {
                Storage::disk('public')->delete($player->photo);
            }
            $player->photo = $request->file('photo')
                ->store('player_photos', 'public');
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

        return redirect()->route('player.index', $tournamentId)
            ->with('success', 'Player updated!');
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

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $file   = $request->file('csv_file');
        $handle = fopen($file->getPathname(), 'r');
        $header = fgetcsv($handle);
        $header = array_map('strtolower', array_map('trim', $header));

        $imported = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < count($header)) {
                $row = array_pad($row, count($header), '');
            }

            $data = array_combine($header, $row);
            $name = trim($data['player name'] ?? $data['name'] ?? '');
            if (empty($name)) continue;

            $role   = trim($data['role'] ?? 'Batsman');
            $mobile = trim($data['mobile'] ?? '0000000000');
            $email  = trim($data['email'] ?? '') ?: null;

            $imageUrl = trim(
                $data['image url'] ?? $data['image_url'] ??
                    $data['photo']     ?? $data['image'] ?? ''
            ) ?: null;

            if ($imageUrl && str_contains($imageUrl, 'drive.google.com')) {
                $imageUrl = $this->convertGoogleDriveLink($imageUrl);
            }

            $validRoles = [
                'Batsman',
                'Bowler',
                'All Rounder',
                'Wicket Keeper'
            ];
            if (!in_array($role, $validRoles)) $role = 'Batsman';

            $playerId = $this->generatePlayerId($tournament->id);

            Player::create([
                'tournament_id' => $tournament->id,
                'player_id'     => $playerId,
                'name'          => $name,
                'role'          => $role,
                'mobile'        => $mobile,
                'email'         => $email,
                'photo'         => null,
                'image_url'     => $imageUrl,
                'age'           => is_numeric($data['age'] ?? '')
                    ? (int)$data['age'] : null,
                'city'          => trim($data['city'] ?? '') ?: null,
                'batting_style' => trim($data['batting style']
                    ?? $data['batting_style'] ?? '') ?: null,
                'bowling_style' => trim($data['bowling style']
                    ?? $data['bowling_style'] ?? '') ?: null,
                'experience'    => trim($data['experience'] ?? '') ?: null,
                'jersey_number' => trim($data['jersey number']
                    ?? $data['jersey_number'] ?? '') ?: null,
                'base_price'    => is_numeric($data['base price']
                    ?? $data['base_price'] ?? '')
                    ? (float)($data['base price']
                        ?? $data['base_price'])
                    : ($tournament->default_base_price ?? 0),
                'status'        => 'approved',
            ]);

            $imported++;
        }

        fclose($handle);

        return redirect()->route('player.index', $tournamentId)
            ->with(
                'success',
                "{$imported} players imported!"
            );
    }

    // ── HELPER: Generate Unique Player ID per Tournament ──
    private function generatePlayerId($tournamentId): string
    {
        // Lock to prevent race conditions
        $lastPlayer = Player::where('tournament_id', $tournamentId)
            ->orderBy('id', 'desc')
            ->lockForUpdate()
            ->first();

        $newNumber = $lastPlayer
            ? ((int) substr($lastPlayer->player_id, 2)) + 1
            : 1001;

        $playerId = 'PX' . $newNumber;

        // Ensure uniqueness WITHIN this tournament only
        while (
            Player::where('tournament_id', $tournamentId)
            ->where('player_id', $playerId)
            ->exists()
        ) {
            $newNumber++;
            $playerId = 'PX' . $newNumber;
        }

        return $playerId;
    }

    // ── HELPER: Convert Google Drive Link ──
    private function convertGoogleDriveLink(string $url): string
    {
        if (preg_match(
            '/\/file\/d\/([a-zA-Z0-9_-]+)/',
            $url,
            $matches
        )) {
            return 'https://drive.google.com/uc?export=view&id='
                . $matches[1];
        }
        if (preg_match(
            '/[?&]id=([a-zA-Z0-9_-]+)/',
            $url,
            $matches
        )) {
            return 'https://drive.google.com/uc?export=view&id='
                . $matches[1];
        }
        return $url;
    }
}
