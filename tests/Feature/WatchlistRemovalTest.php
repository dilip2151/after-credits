<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class WatchlistRemovalTest extends TestCase
{
    use RefreshDatabase;

    protected function beforeRefreshingDatabase(): void
    {
        $connection = DB::connection();

        $this->assertSame('sqlite', $connection->getDriverName());
        $this->assertSame(':memory:', $connection->getDatabaseName());
    }

    public function test_removing_a_film_deletes_it_from_the_watchlist(): void
    {
        $filmId = DB::table('watchlist_items')->insertGetId([
            'title' => 'Dune',
            'watched' => false,
        ]);

        $this->delete("/watchlist/{$filmId}");

        $this->assertDatabaseMissing('watchlist_items', [
            'id' => $filmId,
        ]);
    }
}