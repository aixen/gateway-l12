<?php

namespace App\Http\Middleware;

use App\Constants\Constants;
use App\Models\ApplicationSettingsModel;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class GatewayMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-KEY');
        $requestSignature = $request->header('X-SIGNATURE');

        if (!$apiKey || !$requestSignature) {
            return response()->json(
                [
                    'error' => 'Unauthorized'
                ],
                Response::HTTP_UNAUTHORIZED
            );
        }

        $cacheKey = "key-{$apiKey}";
        $settings = Cache::remember(
            $cacheKey,
            Constants::DEFAULT_CACHE_TIMEOUT_MIN,
        function () use ($apiKey) {
            return ApplicationSettingsModel::where('api_key', $apiKey)->first();
        });

        if (!$settings) {
            return response()->json(['error' => 'Invalid API Key'], Response::HTTP_UNAUTHORIZED);
        }

        $secretKey = $settings->secret_key;

        $data = $request->getContent();
        $expectedSignature = hash_hmac('sha256', $data, $secretKey);

        // Validate the signature
        if (!hash_equals($expectedSignature, $requestSignature)) {
            // TODO: Logs must be on ElasticSearch setup.
            Log::warning('Invalid signature detected', [
                'expected' => $expectedSignature,
                'received' => $requestSignature
            ]);

            return response()->json(['error' => 'Invalid signature'], 403);
        }

        $request->headers->set('X-SECRET-KEY', $secretKey);

        return $next($request);
    }
}
