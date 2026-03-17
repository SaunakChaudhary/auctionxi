<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TeamController extends Controller
{
    private function getTournament($tournamentId)
    {
        return Tournament::where('id', $tournamentId)
                         ->where('user_id', Auth::id())
                         ->with(['teams', 'players'])
                         ->firstOrFail();
    }

    public function index($tournamentId)
    {
        $tournament = $this->getTournament($tournamentId);
        $teams = Team::where('tournament_id', $tournamentId)
                     ->withCount('players')
                     ->get();
        return view('team.index', compact('tournament', 'teams'));
    }

    public function create($tournamentId)
    {
        $tournament = $this->getTournament($tournamentId);
        return view('team.create', compact('tournament'));
    }

    public function store(Request $request, $tournamentId)
    {
        $tournament = $this->getTournament($tournamentId);

        $request->validate([
            'name'         => 'required|string|max:255',
            'logo'         => 'nullable|image|max:2048',
            'owner_name'   => 'required|string|max:255',
            'owner_mobile' => 'nullable|string|max:15',
            'owner_email'  => 'nullable|email',
            'description'  => 'nullable|string',
        ]);

        $logo = null;
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo')
                            ->store('team_logos', 'public');
        }

        Team::create([
            'tournament_id' => $tournament->id,
            'name'          => $request->name,
            'logo'          => $logo,
            'owner_name'    => $request->owner_name,
            'owner_mobile'  => $request->owner_mobile,
            'owner_email'   => $request->owner_email,
            'budget'        => $tournament->team_budget,
            'spent'         => 0,
            'description'   => $request->description,
        ]);

        return redirect()->route('team.index', $tournamentId)
                         ->with('success', 'Team created successfully!');
    }

    public function edit($tournamentId, $teamId)
    {
        $tournament = $this->getTournament($tournamentId);
        $team = Team::where('id', $teamId)
                    ->where('tournament_id', $tournamentId)
                    ->firstOrFail();
        return view('team.edit', compact('tournament', 'team'));
    }

    public function update(Request $request, $tournamentId, $teamId)
    {
        $tournament = $this->getTournament($tournamentId);
        $team = Team::where('id', $teamId)
                    ->where('tournament_id', $tournamentId)
                    ->firstOrFail();

        $request->validate([
            'name'         => 'required|string|max:255',
            'logo'         => 'nullable|image|max:2048',
            'owner_name'   => 'required|string|max:255',
            'owner_mobile' => 'nullable|string|max:15',
            'owner_email'  => 'nullable|email',
            'description'  => 'nullable|string',
        ]);

        if ($request->hasFile('logo')) {
            if ($team->logo) {
                Storage::disk('public')->delete($team->logo);
            }
            $team->logo = $request->file('logo')
                                  ->store('team_logos', 'public');
        }

        $team->update([
            'name'         => $request->name,
            'logo'         => $team->logo,
            'owner_name'   => $request->owner_name,
            'owner_mobile' => $request->owner_mobile,
            'owner_email'  => $request->owner_email,
            'description'  => $request->description,
        ]);

        return redirect()->route('team.index', $tournamentId)
                         ->with('success', 'Team updated successfully!');
    }

    public function destroy($tournamentId, $teamId)
    {
        $tournament = $this->getTournament($tournamentId);
        $team = Team::where('id', $teamId)
                    ->where('tournament_id', $tournamentId)
                    ->firstOrFail();

        if ($team->logo) {
            Storage::disk('public')->delete($team->logo);
        }
        $team->delete();

        return redirect()->route('team.index', $tournamentId)
                         ->with('success', 'Team deleted!');
    }

    public function squad($tournamentId, $teamId)
    {
        $tournament = $this->getTournament($tournamentId);
        $team = Team::where('id', $teamId)
                    ->where('tournament_id', $tournamentId)
                    ->with('players')
                    ->firstOrFail();
        return view('team.squad', compact('tournament', 'team'));
    }
}