<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\GatewayService;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class VerifyTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // $token = $request->header('Authorization');
        $token = $request->bearerToken();
        $secretKey = $request->header('X-SECRET-KEY');

        $gatewayService = new GatewayService;
        $hashedToken = $gatewayService->hashToken($token, $secretKey);
        $jwtCachedData = Cache::get("key-info-{$hashedToken}");
        if (!$token || is_null($jwtCachedData)) {
            return response()->json(
                [
                    'error' => 'Unauthorized'
                ],
                Response::HTTP_UNAUTHORIZED
            );
        }

        // $expiresAt = $jwtCachedData->get('expires_in') ?? null;
        // if (!$expiresAt || now()->timestamp > $expiresAt) {
        //     return response()->json(
        //         [
        //             'error' => 'Token expired'
        //         ],
        //         Response::HTTP_UNAUTHORIZED
        //     );
        // }

        return $next($request);
    }
}
