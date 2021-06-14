<?php

namespace Tests\Feature;

use Cache;
use DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Psr\SimpleCache\InvalidArgumentException;
use Tests\TestCase;

class CreatorTest extends TestCase
{
    const CREATOR_TEST_NAME = "Creator test name";

    public function testCreatorCreate()
    {
        DB::table('creators')
            ->where(['name' => self::CREATOR_TEST_NAME])
            ->delete();

        $response = $this
            ->authorize()
            ->postJson('/api/v1/creator/create', [
                "name"            => self::CREATOR_TEST_NAME,
                "description"     => "short description",
                "categories"      => [1],
                "country_id"      => 1,
                "additional_info" => [
                    "date_of_birth"       => "10.9.1981",
                    "alternative_website" => "http=>//www.example.com"
                ]
            ]);

        $response->assertStatus(201);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testCreatorSearch()
    {
        $response = $this->getJson('/api/v1/creator/search?' . http_build_query([
                'query' => self::CREATOR_TEST_NAME,
                'limit' => null
            ]));

        $response->assertStatus(200);
        $response->assertJsonPath('0.name', self::CREATOR_TEST_NAME);

        $id = $response->json('0.id');
        Cache::set('cached_creator_id', $response->json('0.id'), now()->addMinutes(15));
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCreatorDetail()
    {
        $response = $this->getJson('/api/v1/creator/detail/' . Cache::get('cached_creator_id'), [
            'query' => self::CREATOR_TEST_NAME,
            'limit' => null
        ]);

        $response->assertStatus(200);
        $response->assertSee(self::CREATOR_TEST_NAME);
    }

    public function testCreatorAvatar()
    {
        // @todo
    }

    public function testCreatorUrl()
    {
        // @todo
    }
}
