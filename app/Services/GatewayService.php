<?php

namespace App\Services;

use App\Constants\Constants;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class GatewayService
{
    protected $authDomain, $routePrefix;

    public function __construct()
    {
        $this->authDomain = config('auth.domain');
        $this->routePrefix = '/api/user';
    }

    protected function postAuth(string $route, array $payload, ?string $token = null)
    {
        $domainRoutes = "{$this->authDomain}{$this->routePrefix}{$route}";

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ]);

        if (!is_null($token)) {
            $response = $response->withToken($token);
        }

        $response = $response
            ->post($domainRoutes, $payload);

        return $response;
    }

    public function setHashToken(string $secretKey, string $token)
    {
        return hash_hmac('sha256', $token, $secretKey);
    }

    public function login(string $secretKey, string $email, string $password)
    {
        $route = '/login';
        $payload = [
            'email' => $email,
            'password' => $password
        ];

        $response = $this
            ->postAuth($route, $payload)
            ->collect();

        $token = $response->get('token');
        $hashedToken = $this->setHashToken($token, $secretKey);

        $settings = Cache::remember(
            "key-info-{$hashedToken}",
            Constants::DEFAULT_CACHE_TIMEOUT_MIN,
            fn () => $response
        );

        return $settings;
    }

    public function logout(string $secretKey, string $token)
    {
        $route = '/logout';
        $response = $this
            ->postAuth($route, [], $token);

        if ($response->failed()) {
            return [
                'message' => 'Something went wrong !',
            ];
        }

        $this->removeCacheToken($token, $secretKey);

        return [
            'message' => 'Logged out successfully',
        ];
    }

    public function refresh(string $secretKey, string $token)
    {
        $route = '/refresh';
        $response = $this
            ->postAuth($route, [], $token);

        if ($response->failed()) {
            return [
                'message' => 'Something went wrong !',
            ];
        }

        $this->removeCacheToken($token, $secretKey);

        $response = $response->collect();
        $newToken = $response->get('token');

        $hashedToken = $this->setHashToken($newToken, $secretKey);

        $response = Cache::remember(
            "key-info-{$hashedToken}",
            Constants::DEFAULT_CACHE_TIMEOUT_MIN,
            fn () => $response
        );

        return $response;
    }

    private function removeCacheToken(string $token, string $secretKey)
    {
        $hashedToken = $this->setHashToken($token, $secretKey);
        Cache::forget("key-info-{$hashedToken}");

        return true;
    }
}