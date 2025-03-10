<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\GatewayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GatewayController extends Controller
{
    public function __construct(
        protected GatewayService $gateway,
    ) {

    }

    public function register(RegisterRequest $request)
    {
        $secretKey = $request->header('X-SECRET-KEY');
        extract($request->only(['email', 'name', 'password']));

        $response = $this->gateway->register($secretKey, $email, $name, $password);

        return response()->json($response);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $secretKey = $request->header('X-SECRET-KEY');
        extract($request->only(['email', 'password']));

        $response = $this->gateway->login($secretKey, $email, $password);

        return response()->json($response);
    }

    public function logout(Request $request): JsonResponse
    {
        $secretKey = $request->header('X-SECRET-KEY');
        $token = $request->bearerToken();

        $response = $this->gateway->logout($secretKey, $token);

        return response()->json($response);
    }

    public function refresh(Request $request)
    {
        $secretKey = $request->header('X-SECRET-KEY');
        $token = $request->bearerToken();

        $response = $this->gateway->refresh($secretKey, $token);

        return response()->json($response);
    }

    public function getProfile(Request $request)
    {
        $secretKey = $request->header('X-SECRET-KEY');
        $token = $request->bearerToken();

        $userProfile = $this->gateway->getProfile($token, $secretKey);

        return response()->json($userProfile);
    }
}
