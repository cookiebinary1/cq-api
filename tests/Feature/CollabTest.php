<?php

namespace Tests\Feature;

use App\Models\Creator;
use Cache;
use DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Psr\SimpleCache\InvalidArgumentException;
use Tests\TestCase;

class CollabTest extends TestCase
{
    private function deleteTestCollab()
    {
        DB::table('collabs')
            ->where(["creator1_id" => 1,
                "creator2_id" => 2])
            ->delete();
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testCollabCreate()
    {
        $this->deleteTestCollab();


        list($creatorId1, $creatorId2) = Creator::orderByDesc('priority')
            ->limit(2)
            ->pluck('id');

        $response = $this
            ->authorize()
            ->postJson('/api/v1/collab/create', [
                "creator1_id" => $creatorId1,
                "creator2_id" => $creatorId2,
            ]);

        $response
            ->assertStatus(201)
            ->assertJsonFragment(["creator1_id" => $creatorId1,
                "creator2_id" => $creatorId2]);

        Cache::set('collab_test_id', $response->json('id'), now()->addMinutes(15));
    }

    /**
     *
     */
    public function testCollabLike()
    {
        DB::table('likes')
            ->where('collab_id', Cache::get('collab_test_id'))
            ->where('user_id', $this->getUser()->id)
            ->delete();

        $response = $this
            ->authorize()
            ->postJson('/api/v1/collab/like', [
                "collab_id" => Cache::get('collab_test_id'),
            ]);

        $response
            ->assertStatus(200)
            ->assertJsonFragment(["creator1_id" => 1,
                "creator2_id" => 2]);
    }

    public function testCollabList()
    {
        // @todo

        $this->deleteTestCollab();
    }
}
