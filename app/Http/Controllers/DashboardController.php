<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $tournaments = $user->tournaments()->withCount(['teams', 'players'])->get();

        $totalTournaments = $tournaments->count();
        $totalTeams       = $tournaments->sum('teams_count');
        $totalPlayers     = $tournaments->sum('players_count');

        return view('dashboard.index', compact(
            'tournaments', 'totalTournaments', 'totalTeams', 'totalPlayers'
        ));
    }
}