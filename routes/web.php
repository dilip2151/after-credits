<?php

use App\Http\Controllers\CinemaSearchController;
use App\Http\Controllers\CinemaShowtimesController;
use App\Http\Controllers\MovieSearchController;
use App\Http\Controllers\MovieWatchProvidersController;
use App\Http\Controllers\WatchlistController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/watchlist');

Route::get('/watchlist', [WatchlistController::class, 'index']);

Route::post('/watchlist', [WatchlistController::class, 'store'])
    ->name('watchlist.store');

Route::patch('/watchlist/{item}/watched', [WatchlistController::class, 'markWatched'])
    ->name('watchlist.mark-watched');

Route::patch('/watchlist/{item}/match', [WatchlistController::class, 'matchFilm'])
    ->whereNumber('item')
    ->name('watchlist.match');

Route::delete('/watchlist/{item}', [WatchlistController::class, 'destroy'])
    ->name('watchlist.destroy');

Route::get('/movies/search', [MovieSearchController::class, 'search'])
    ->name('movies.search');

Route::get('/movies/{movie}/watch-providers', [MovieWatchProvidersController::class, 'show'])
    ->whereNumber('movie')
    ->name('movies.watch-providers');

Route::view('/cinemas', 'cinemas')->name('cinemas.index');

Route::get('/cinemas/search', [CinemaSearchController::class, 'search'])
    ->name('cinemas.search');

Route::get('/cinemas/showtimes/{film}', [CinemaShowtimesController::class, 'show'])
    ->whereNumber('film')
    ->name('cinemas.showtimes');

Route::view('/credits', 'credits')->name('credits.index');