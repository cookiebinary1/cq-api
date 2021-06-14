<?php

namespace Tests\Feature;

use App\Models\User;
use Cache;
use DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Psr\SimpleCache\InvalidArgumentException;
use Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * url: /register
     * method: POST
     * @throws InvalidArgumentException
     * @todo data
     */
    public function testAuthRegister()
    {
        $this->deleteUser();

        $response = $this
            ->postJson('/api/v1/register', [
                'name'                  => 'temporary-test-user',
                'email'                 => $this->getTestUserEmail(),
                'password'              => 'asdfasdf',
                'password_confirmation' => 'asdfasdf',
                'country_id'            => 1,
            ]);

        $response->assertStatus(201);
    }

    /**
     * url: /login
     * method: POST
     * @throws InvalidArgumentException
     * @todo data
     */
    public function testAuthLogin()
    {
        $user = $this->getUser();

        $response = $this
            ->actingAs($user)
            ->postJson('/api/v1/login', [
                'email'    => 'test-user@test.com',
                'password' => 'asdfasdf',
            ]);

        $response->assertStatus(201);

        $this->setToken($response->json('token'));
    }

    /**
     * url: /me
     * method: GET
     * @todo data
     */
    public function testAuthMe()
    {
        $response = $this
            ->authorize()
            ->getJson('/api/v1/me');

        $response->assertOk();
        $response->assertJsonPath('currentUser.email', 'test-user@test.com');
    }

    /**
     * url: /logout
     * method: DELETE
     * @throws InvalidArgumentException
     */
    public function testAuthLogout()
    {
        $response = $this
            ->authorize()
            ->deleteJson('/api/v1/logout');

        $response->assertOk();

        $this->removeToken();

        $this->testAuthLogin();
    }
}
