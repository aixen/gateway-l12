<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GatewayController extends Controller
{
    public function login(Request $request)
    {
        // TODO: add service on business logic to authenticate user
        extract($request->only(['email', 'password']));

        return response()->json([
            'request' => [
                'email' => $email,
                'password' => $password
            ]
        ]);
    }
}
