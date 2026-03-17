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
            $photo = $request->file('photo')->store('player_photos', 'public');
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
            ->latest()
            ->get();

        return view('player.index', compact('tournament', 'players'));
    }

    // ── ORGANIZER: Approve ──
    public function approve($tournamentId, $playerId)
    {
        $this->getTournament($tournamentId);

        $player = Player::where('id', $playerId)
            ->where('tournament_id', $tournamentId)
            ->firstOrFail();

        $player->update(['status' => 'approved']);

        return back()->with('success', 'Player approved!');
    }

    // ── ORGANIZER: Reject ──
    public function reject($tournamentId, $playerId)
    {
        $this->getTournament($tournamentId);

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
        $player     = Player::where('id', $playerId)
            ->where('tournament_id', $tournamentId)
            ->firstOrFail();

        return view('player.edit', compact('tournament', 'player'));
    }

    // ── ORGANIZER: Update ──
    public function update(Request $request, $tournamentId, $playerId)
    {
        $tournament = $this->getTournament($tournamentId);
        $player     = Player::where('id', $playerId)
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
            $player->photo = $request->file('photo')->store('player_photos', 'public');
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
    // Expected CSV columns (case-insensitive, trimmed):
    //   REQUIRED : Player Name | Role | Mobile
    //   OPTIONAL : Photo (Google Drive link) | Email | Age | City |
    //              Batting Style | Bowling Style | Experience |
    //              Jersey Number | Base Price
    public function importCsv(Request $request, $tournamentId)
    {
        $tournament = $this->getTournament($tournamentId);

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $file   = $request->file('csv_file');
        $handle = fopen($file->getPathname(), 'r');

        // Read and normalise header row
        $rawHeader = fgetcsv($handle);

        if (!$rawHeader) {
            fclose($handle);
            return back()->withErrors(['csv_file' => 'CSV file is empty or unreadable.']);
        }

        // Normalise: lowercase + trim each header cell
        $header = array_map(fn($h) => strtolower(trim($h)), $rawHeader);

        $imported = 0;
        $skipped  = 0;
        $errors   = [];

        while (($row = fgetcsv($handle)) !== false) {

            // Skip completely empty rows
            if (empty(array_filter($row, fn($v) => trim($v) !== ''))) {
                continue;
            }

            // Pad row to match header length
            if (count($row) < count($header)) {
                $row = array_pad($row, count($header), '');
            }

            // Combine header => values
            $data = array_combine($header, $row);

            // ── REQUIRED: Player Name ──
            // Accept: "player name" or "name"
            $name = trim(
                $data['player name'] ??
                    $data['playername']  ??
                    $data['name']        ?? ''
            );

            if ($name === '') {
                $skipped++;
                continue;
            }

            // ── REQUIRED: Role ──
            // Accept: "role"
            $role = trim($data['role'] ?? 'Batsman');
            $validRoles = ['Batsman', 'Bowler', 'All Rounder', 'Wicket Keeper'];
            if (!in_array($role, $validRoles, true)) {
                $role = 'Batsman';
            }

            // ── REQUIRED: Mobile ──
            // Accept: "mobile" or "phone"
            $mobile = trim(
                $data['mobile'] ??
                    $data['phone']  ?? '0000000000'
            );

            // ── OPTIONAL: Photo / Drive Link ──
            // Accept: "photo" or "image url" or "image" or "drive link"
            // ── OPTIONAL: Photo / Drive Link ──
            $rawPhoto = trim(
                $data['photo']      ??
                    $data['image url']  ??
                    $data['image_url']  ??
                    $data['image']      ??
                    $data['drive link'] ??
                    $data['drive_link'] ??
                    $data['photo url']  ??
                    $data['photo_url']  ?? ''
            );

            $imageUrl = null;
            if ($rawPhoto !== '') {
                if (str_contains($rawPhoto, 'drive.google.com')) {
                    $imageUrl = $this->convertGoogleDriveLink($rawPhoto);
                } else {
                    $imageUrl = $rawPhoto;
                }
            }
            // ── OPTIONAL: Email ──
            $email = trim($data['email'] ?? '') ?: null;

            // ── OPTIONAL: Age ──
            $ageRaw = trim($data['age'] ?? '');
            $age    = (is_numeric($ageRaw) && $ageRaw > 0) ? (int)$ageRaw : null;

            // ── OPTIONAL: City ──
            $city = trim($data['city'] ?? '') ?: null;

            // ── OPTIONAL: Batting Style ──
            $battingStyle = trim(
                $data['batting style'] ??
                    $data['batting_style'] ??
                    $data['batting']       ?? ''
            ) ?: null;

            // ── OPTIONAL: Bowling Style ──
            $bowlingStyle = trim(
                $data['bowling style'] ??
                    $data['bowling_style'] ??
                    $data['bowling']       ?? ''
            ) ?: null;

            // ── OPTIONAL: Experience ──
            $experience = trim($data['experience'] ?? '') ?: null;

            // ── OPTIONAL: Jersey Number ──
            $jerseyNumber = trim(
                $data['jersey number'] ??
                    $data['jersey_number'] ??
                    $data['jersey']        ?? ''
            ) ?: null;

            // ── OPTIONAL: Base Price ──
            $basePriceRaw = trim(
                $data['base price'] ??
                    $data['base_price'] ??
                    $data['baseprice']  ??
                    $data['price']      ?? ''
            );
            $basePrice = is_numeric($basePriceRaw)
                ? (float) $basePriceRaw
                : (float) ($tournament->default_base_price ?? 0);

            // Generate unique Player ID for this tournament
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
        if ($skipped > 0) {
            $message .= " ({$skipped} row(s) skipped — missing name.)";
        }

        return redirect()
            ->route('player.index', $tournamentId)
            ->with('success', $message);
    }

    // ── HELPER: Generate Unique Player ID per Tournament ──
    private function generatePlayerId(int $tournamentId): string
    {
        $lastPlayer = Player::where('tournament_id', $tournamentId)
            ->orderBy('id', 'desc')
            ->first();

        $newNumber = $lastPlayer
            ? ((int) substr($lastPlayer->player_id, 2)) + 1
            : 1001;

        $playerId = 'PX' . $newNumber;

        // Guarantee uniqueness within this tournament
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

    // ── HELPER: Convert any Google Drive link to direct-embed URL ──
    //
    // Handles all common Google Drive URL formats:
    //   1. https://drive.google.com/file/d/FILE_ID/view?usp=sharing
    //   2. https://drive.google.com/file/d/FILE_ID/view
    //   3. https://drive.google.com/open?id=FILE_ID
    //   4. https://drive.google.com/uc?id=FILE_ID
    //   5. https://drive.google.com/thumbnail?id=FILE_ID
    //
    // Output: https://drive.google.com/uc?export=view&id=FILE_ID
    //
    private function convertGoogleDriveLink(string $url): string
    {
        $url = trim($url);

        if (!str_contains($url, 'drive.google.com')) {
            return $url;
        }

        $fileId = null;

        // Pattern 1: /file/d/{ID}/
        if (preg_match('#/file/d/([a-zA-Z0-9_-]+)#', $url, $m)) {
            $fileId = $m[1];
        }
        // Pattern 2: ?id={ID} or &id={ID}
        elseif (preg_match('#[?&]id=([a-zA-Z0-9_-]+)#', $url, $m)) {
            $fileId = $m[1];
        }

        if ($fileId) {
            return 'https://lh3.googleusercontent.com/d/' . $fileId . '=w200';
        }

        return $url;
    }
}
