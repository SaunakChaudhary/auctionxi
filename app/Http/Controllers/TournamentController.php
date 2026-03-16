<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TournamentController extends Controller
{
    public function index()
    {
        $tournaments = Auth::user()->tournaments()->latest()->get();
        return view('tournament.index', compact('tournaments'));
    }

    public function create()
    {
        return view('tournament.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'number_of_teams' => 'required|integer|min:2',
            'team_budget'     => 'required|numeric|min:0',
            'location'        => 'nullable|string|max:255',
            'start_date'      => 'nullable|date',
            'auction_date'    => 'nullable|date',
            'description'     => 'nullable|string',
        ]);

        $code = strtoupper(Str::random(8));
        while (Tournament::where('code', $code)->exists()) {
            $code = strtoupper(Str::random(8));
        }

        $tournament = Tournament::create([
            'user_id'             => Auth::id(),
            'name'                => $request->name,
            'code'                => $code,
            'number_of_teams'     => $request->number_of_teams,
            'team_budget'         => $request->team_budget,
            'default_base_price'  => $request->default_base_price ?? 0,
            'location'            => $request->location,
            'start_date'          => $request->start_date,
            'auction_date'        => $request->auction_date,
            'description'         => $request->description,
        ]);

        return redirect()->route('tournament.show', $tournament->id)
            ->with('success', 'Tournament created successfully!');
    }

    public function show($id)
    {
        $tournament = Tournament::where('id', $id)
            ->where('user_id', Auth::id())
            ->with(['teams', 'players'])
            ->firstOrFail();
        return view('tournament.show', compact('tournament'));
    }

    public function edit($id)
    {
        $tournament = Tournament::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        return view('tournament.edit', compact('tournament'));
    }

    public function update(Request $request, $id)
    {
        $tournament = Tournament::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $request->validate([
            'name'            => 'required|string|max:255',
            'number_of_teams' => 'required|integer|min:2',
            'team_budget'     => 'required|numeric|min:0',
            'location'        => 'nullable|string|max:255',
            'start_date'      => 'nullable|date',
            'auction_date'    => 'nullable|date',
            'description'     => 'nullable|string',
        ]);

        $tournament->update([
            'name'                => $request->name,
            'number_of_teams'     => $request->number_of_teams,
            'team_budget'         => $request->team_budget,
            'default_base_price'  => $request->default_base_price ?? 0,
            'location'            => $request->location,
            'start_date'          => $request->start_date,
            'auction_date'        => $request->auction_date,
            'description'         => $request->description,
        ]);

        return redirect()->route('tournament.show', $tournament->id)
            ->with('success', 'Tournament updated successfully!');
    }

    public function destroy($id)
    {
        $tournament = Tournament::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        $tournament->delete();
        return redirect()->route('tournament.index')
            ->with('success', 'Tournament deleted successfully!');
    }

    public function toggleRegistration($id)
    {
        $tournament = Tournament::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        $tournament->registration_status = $tournament->registration_status === 'open' ? 'closed' : 'open';
        $tournament->save();
        return back()->with('success', 'Registration status updated!');
    }
}
