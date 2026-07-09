<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Crypt;

class ConnectController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'key' => ['required', 'string', 'max:255'],
        ]);

        $token = Crypt::encryptString($validated['key']);

        return response()->json([
            'token' => $token,
            'url' => 'https://termiimcp.com?key='.urlencode($token),
        ]);
    }
}
