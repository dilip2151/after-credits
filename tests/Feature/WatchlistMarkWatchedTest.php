<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class WatchlistMarkWatchedTest extends TestCase
{
    use RefreshDatabase;

    protected function beforeRefreshingDatabase(): void
    {
        $connection = DB::connection();

        $this->assertSame('sqlite', $connection->getDriverName());
        $this->assertSame(':memory:', $connection->getDatabaseName());
    }

    public function test_marking_a_film_as_watched_saves_its_status(): void
    {
        $filmId = DB::table('watchlist_items')->insertGetId([
            'title' => 'Interstellar',
            'watched' => false,
        ]);

        $this->patchJson(route('watchlist.mark-watched', ['item' => $filmId]))
            ->assertOk()
            ->assertJson(['watched' => true]);

        $this->assertDatabaseHas('watchlist_items', [
            'id' => $filmId,
            'watched' => true,
        ]);
    }
}