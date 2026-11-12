<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_homepage_redirects_to_the_watchlist(): void
    {
        $this->get('/')->assertRedirect('/watchlist');
    }
}