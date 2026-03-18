<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\AuctionController;
use App\Http\Controllers\PublicController;

// ─────────────────────────────────────────
// HOMEPAGE
// ─────────────────────────────────────────
Route::get('/', function () {
    return view('home');
})->name('home');

// ─────────────────────────────────────────
// AUTH ROUTES
// ─────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('forgot.password');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ─────────────────────────────────────────
// ORGANIZER ROUTES (auth protected)
// ─────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Tournaments
    Route::prefix('tournaments')->name('tournament.')->group(function () {
        Route::get('/',                [TournamentController::class, 'index'])->name('index');
        Route::get('/create',          [TournamentController::class, 'create'])->name('create');
        Route::post('/',               [TournamentController::class, 'store'])->name('store');
        Route::get('/{id}',            [TournamentController::class, 'show'])->name('show');
        Route::get('/{id}/edit',       [TournamentController::class, 'edit'])->name('edit');
        Route::put('/{id}',            [TournamentController::class, 'update'])->name('update');
        Route::delete('/{id}',         [TournamentController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle-registration', [TournamentController::class, 'toggleRegistration'])->name('toggleRegistration');
    });

    // Teams
    Route::prefix('tournaments/{tournamentId}/teams')->name('team.')->group(function () {
        Route::get('/',                    [TeamController::class, 'index'])->name('index');
        Route::get('/create',              [TeamController::class, 'create'])->name('create');
        Route::post('/',                   [TeamController::class, 'store'])->name('store');
        Route::get('/{teamId}/edit',       [TeamController::class, 'edit'])->name('edit');
        Route::put('/{teamId}',            [TeamController::class, 'update'])->name('update');
        Route::delete('/{teamId}',         [TeamController::class, 'destroy'])->name('destroy');
        Route::get('/{teamId}/squad',      [TeamController::class, 'squad'])->name('squad');
    });

    // Players
    Route::prefix('tournaments/{tournamentId}/players')->name('player.')->group(function () {
        Route::get('/',                        [PlayerController::class, 'index'])->name('index');
        Route::get('/{playerId}/edit',         [PlayerController::class, 'edit'])->name('edit');
        Route::put('/{playerId}',              [PlayerController::class, 'update'])->name('update');
        Route::post('/{playerId}/approve',     [PlayerController::class, 'approve'])->name('approve');
        Route::post('/{playerId}/reject',      [PlayerController::class, 'reject'])->name('reject');
        Route::get('/import',                  [PlayerController::class, 'importForm'])->name('import');
        Route::post('/import',                 [PlayerController::class, 'importCsv'])->name('importCsv');
    });

    // Auction
    Route::prefix('tournaments/{tournamentId}/auction')->name('auction.')->group(function () {
        Route::get('/panel',                   [AuctionController::class, 'panel'])->name('panel');
        Route::get('/search-player',           [AuctionController::class, 'searchPlayer'])->name('searchPlayer');
        Route::post('/assign',                 [AuctionController::class, 'assign'])->name('assign');
        Route::post('/update-status',          [AuctionController::class, 'updateStatus'])->name('updateStatus');
        Route::get('/results',                 [AuctionController::class, 'results'])->name('results');
    });

    Route::delete(
        '/tournament/{tournamentId}/players/{playerId}',
        [App\Http\Controllers\PlayerController::class, 'destroy']
    )
        ->name('player.destroy');

    // Delete ALL players
    Route::delete(
        '/tournament/{tournamentId}/players',
        [App\Http\Controllers\PlayerController::class, 'destroyAll']
    )
        ->name('player.destroyAll');

    // Reorder player (move up/down)
    Route::post(
        '/tournament/{tournamentId}/players/reorder',
        [App\Http\Controllers\PlayerController::class, 'reorder']
    )
        ->name('player.reorder');
});

// ─────────────────────────────────────────
// PUBLIC ROUTES (no login required)
// ─────────────────────────────────────────

// Live auction page (teams + budgets — no results table)
Route::get(
    '/live/{code}',
    [App\Http\Controllers\PublicController::class, 'live']
)
    ->name('public.live');

// Player search page (separate route)
Route::get(
    '/live/{code}/search',
    [App\Http\Controllers\PublicController::class, 'searchPage']
)
    ->name('public.search.page');

// AJAX search player (by ID or name)
Route::get(
    '/live/{code}/search-player',
    [App\Http\Controllers\PublicController::class, 'searchPlayer']
)
    ->name('public.search');

// Team squad page
Route::get(
    '/live/{code}/team/{teamId}',
    [App\Http\Controllers\PublicController::class, 'teamSquad']
)
    ->name('public.team.squad');

// ── AUCTION ROUTES (add inside auth middleware group) ──

// Search player (by ID or name)
Route::get(
    '/tournament/{tournamentId}/auction/search',
    [App\Http\Controllers\AuctionController::class, 'searchPlayer']
)
    ->name('auction.searchPlayer');
// Player Registration (public)
Route::get('/auctionxi/player-register/{code}',  [PlayerController::class, 'publicRegister'])->name('public.player.register');
Route::post('/auctionxi/player-register/{code}', [PlayerController::class, 'publicStore'])->name('public.player.store');

// Live Auction Viewer (public)
Route::get('/auctionxi/live/{code}',             [PublicController::class, 'live'])->name('public.live');
Route::get('/auctionxi/live/{code}/search',      [PublicController::class, 'searchPlayer'])->name('public.search');
Route::get('/auctionxi/live/{code}/team/{teamId}', [PublicController::class, 'teamSquad'])->name('public.team.squad');
