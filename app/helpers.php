<?php
/**
 * @author Cookie
 */

use App\Exceptions\EntityException;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response as ResponseAlias;
use JetBrains\PhpStorm\Pure;
use Solarium\Client;
use Solarium\Core\Client\Adapter\Curl;
use Symfony\Component\EventDispatcher\EventDispatcher;

if (!function_exists('error')) {
    /**
     * @param int $code
     * @param int|null $statusCode
     * @param string|null $message
     * @param array|null $additionalData
     * @return Application|ResponseFactory|ResponseAlias
     */
    function error(int $code, ?int $statusCode = null, ?string $message = null, ?array $additionalData = null)
    {
        $response = [
            'status'          => 'error',
            'code'            => $code,
            'message'         => $message ?? config('errors.' . $code),
            'additional_data' => $additionalData,
        ];

        return response($response, $statusCode ?? 404);
    }
}

if (!function_exists('get_entity')) {
    /**
     * @param string $entity
     * @param $id
     * @param false $multiple
     * @return mixed
     * @throws EntityException
     */
    function get_entity(string $entity, $id, $multiple = false)
    {
        $className = !str_starts_with($entity, 'App\\Models') ? "App\\Models\\$entity" : $entity;

        $ids = explode(',', $id);
        $instance = $multiple
            ? call_user_func($className . "::whereIn", 'id', $ids)->get()
            : call_user_func($className . "::find", $id);

        if (!$instance)
            throw new EntityException("Entity(s) `$entity` ID(s): `$id` do(es) not exist");

        return $instance;
    }
}

if (!function_exists('get_entity_by_slug')) {
    /**
     * @param string $entity
     * @param null $slug
     * @return mixed
     * @throws EntityException
     */
    function get_entity_by_slug(string $entity, $slug)
    {
        $className = !str_starts_with($entity, 'App\\Models') ? "App\\Models\\$entity" : $entity;
        $instance = optional(call_user_func($className . "::whereSlug", $slug))->first();

        if (!$instance)
            throw new EntityException("Entity `$entity` slug: `$slug` doesn't exist");

        return $instance;
    }
}

if (!function_exists('cache')) {
    /**
     * Get or set function
     *
     * @param string $name
     * @param callable $function
     * @param null $expireAt By default two weeks
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @author Cookie
     */
    function cache(string $name, callable $function, $expireAt = null)
    {
        if ($result = Cache::get($name)) {
            Cache::set($name, $result = $function(), $expireAt ?? now()->addWeek());
        }

        return $result;
    }
}

if (!function_exists('disable_service')) {
    /**
     * @return ResponseAlias
     * @author Cookie
     */
    function disable_service()
    {
        return error(1001, 503, "Service is under reconstruction")->send();
    }
}

if (!function_exists('solr_client')) {
    /**
     * @param null $core
     * @return Client
     */
    function solr_client($core)
    {
        $adapter = new Curl(); // or any other adapter implementing AdapterInterface
        $eventDispatcher = new EventDispatcher();

        $config = config('solr');
        $config['endpoint']['localhost']['core'] = $core;

        // create a client instance
        return new Client($adapter, $eventDispatcher, $config);
    }
}

if (!function_exists('date_from_range')) {
    /**
     * @param $range
     * @return Carbon|\Illuminate\Support\Carbon
     */
    function date_from_range($range): \Illuminate\Support\Carbon|Carbon
    {
        return match ($range) {
            'month' => now()->subMonth(),
            'year' => now()->subYear(),
            'week' => now()->subWeek(),
            'day' => now()->subDay(),
            'hour' => now()->subHour(),
            default => Carbon::parse('1970-01-01'),
        };
    }
}

if (!function_exists('only_host_name')) {
    /**
     * @param string $url
     * @return string
     */
    #[Pure] function only_host_name(string $url): string
    {
        return implode('.', array_slice(explode('.', parse_url($url, PHP_URL_HOST)), -2));
    }
}

if (!function_exists('real_ip_address')) {
    /**
     * @return mixed
     */
    function real_ip_address(): mixed
    {
        return @$_SERVER['HTTP_X_REAL_IP'];
    }
}
