<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CountryTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCountryList()
    {
        $response = $this->getJson(self::URL_PREFIX . '/country');

        $response->assertStatus(200);
        self::assertTrue(@$response->json()[0]['id'] == 1, 'First item isn\'t id = 1');
    }
}
