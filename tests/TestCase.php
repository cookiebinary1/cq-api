<?php

namespace Tests;

use App\Models\User;
use Cache;
use DB;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Psr\SimpleCache\InvalidArgumentException;
use Tests\Feature\AuthTest;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    const TOKEN_CACHE = 'auth_token';
    const USER_CACHE = 'auth_user';
    const URL_PREFIX = '/api/v1';
    const TEST_EMAIL = 'test-user@test.com';

    /**
     * @throws \Exception
     * @throws InvalidArgumentException
     */
    protected function getTestUserEmail()
    {
        if (!$testId = Cache::get('test_user_id')) {
            $testId = random_int(1000000, 9999999);
            Cache::set('test_user_id', $testId, now()->addSeconds(5));
        }
        return "test-user$testId" . "@test.com";
    }

    /**
     * @return mixed
     * @throws InvalidArgumentException
     */
    protected function getUser()
    {
        return User::whereEmail($this->getTestUserEmail())->first();
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function deleteUser()
    {
        DB::table('users')
            ->where('email', $this->getTestUserEmail())
            ->delete();
    }

    /**
     * @param string $token
     * @return $this
     * @throws InvalidArgumentException
     */
    protected function setToken(string $token): self
    {
        Cache::set(self::TOKEN_CACHE, $token, now()->addMinutes(15));

        return $this;
    }

    /**
     * @return $this
     */
    protected function removeToken(): self
    {
        Cache::forget(self::TOKEN_CACHE);

        return $this;
    }

    /**
     * @return AuthTest
     */
    protected function authorize(): self
    {
        return $this->withToken(Cache::get(self::TOKEN_CACHE));
    }


}
