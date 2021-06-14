<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VersionTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testVersion()
    {
        $response = $this->getJson('/');

        $response
            ->assertStatus(200)
            ->assertJsonPath('version', config('app.version'));
    }
}
