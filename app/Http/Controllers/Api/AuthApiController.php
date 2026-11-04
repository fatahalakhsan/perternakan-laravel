<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthApiController extends Controller
{
    /** POST /api/login  body JSON: email, password  ->  200 + token, 400 input salah, 401 salah sandi */
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Email atau kata sandi salah.'], 401);
        }

        return response()->json([
            'token'      => $user->createToken('api')->plainTextToken,
            'token_type' => 'Bearer',
            'user'       => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'role' => $user->role],
        ]);
    }

    /** GET /api/me  -> data pengguna yang sedang masuk */
    public function me(Request $request): JsonResponse
    {
        $u = $request->user();

        return response()->json(['id' => $u->id, 'name' => $u->name, 'email' => $u->email, 'role' => $u->role]);
    }

    /** POST /api/logout  -> mencabut semua token pengguna */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Berhasil keluar.']);
    }
}
