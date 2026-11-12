<?php

use App\Http\Controllers\CinemaSearchController;
use App\Http\Controllers\MovieSearchController;
use App\Http\Controllers\WatchlistController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/watchlist');

Route::get('/watchlist', [WatchlistController::class, 'index']);

Route::post('/watchlist', [WatchlistController::class, 'store'])
    ->name('watchlist.store');

Route::patch('/watchlist/{item}/watched', [WatchlistController::class, 'markWatched'])
    ->name('watchlist.mark-watched');

Route::get('/movies/search', [MovieSearchController::class, 'search'])
    ->name('movies.search');

Route::view('/cinemas', 'cinemas')->name('cinemas.index');

Route::get('/cinemas/search', [CinemaSearchController::class, 'search'])
    ->name('cinemas.search');

Route::view('/credits', 'credits')->name('credits.index');