<?php

use App\Http\Controllers\MovieSearchController;
use App\Http\Controllers\WatchlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/watchlist', [WatchlistController::class, 'index']);
Route::post('/watchlist', [WatchlistController::class, 'store'])->name('watchlist.store');
Route::patch('/watchlist/{item}/watched', [WatchlistController::class, 'markWatched'])
    ->name('watchlist.mark-watched');

Route::get('/movies/search', [MovieSearchController::class, 'search'])
    ->name('movies.search');