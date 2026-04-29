<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // ─── POST /api/auth/register ──────────────────────────────────
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username'  => [
                'required',
                'string',
                'min:3',
                'max:30',
                'regex:/^[a-z0-9_]+$/',
                'unique:users,username',
            ],
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'  => ['required', 'confirmed', Password::min(8)],
        ], [
            'username.required'  => 'Username wajib diisi.',
            'username.min'       => 'Username minimal 3 karakter.',
            'username.max'       => 'Username maksimal 30 karakter.',
            'username.regex'     => 'Username hanya boleh berisi huruf kecil, angka, dan underscore.',
            'username.unique'    => 'Username sudah digunakan, coba yang lain.',
            'name.required'      => 'Nama lengkap wajib diisi.',
            'email.required'     => 'Email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.unique'       => 'Email sudah terdaftar.',
            'password.required'  => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min'       => 'Password minimal 8 karakter.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'id'       => (string) Str::uuid(),
            'username' => $request->username,
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'user',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registrasi berhasil.',
            'user'    => [
                'id'       => $user->id,
                'username' => $user->username,
                'name'     => $user->name,
                'email'    => $user->email,
                'role'     => $user->role,
            ],
            'token'      => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    // ─── POST /api/auth/login ─────────────────────────────────────
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identifier' => ['required', 'string'],   // email ATAU username
            'password'   => ['required', 'string'],
        ], [
            'identifier.required' => 'Email atau username wajib diisi.',
            'password.required'   => 'Password wajib diisi.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $identifier = $request->identifier;

        // Cari user berdasarkan email atau username
        $user = filter_var($identifier, FILTER_VALIDATE_EMAIL)
            ? User::where('email', $identifier)->first()
            : User::where('username', $identifier)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Email/username atau password salah.',
                'errors'  => [
                    'identifier' => ['Kredensial tidak valid.'],
                ],
            ], 401);
        }

        // Hapus token lama (opsional: bisa dibuang jika ingin multi-session)
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil.',
            'user'    => [
                'id'         => $user->id,
                'username'   => $user->username,
                'name'       => $user->name,
                'email'      => $user->email,
                'role'       => $user->role,
                'avatar_url' => $user->avatar_url,
            ],
            'token'      => $token,
            'token_type' => 'Bearer',
        ]);
    }

    // ─── POST /api/auth/logout ────────────────────────────────────
    public function logout(Request $request)
    {
        // Hapus token yang sedang dipakai
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout berhasil.']);
    }

    // ─── GET /api/auth/me ─────────────────────────────────────────
    public function me(Request $request)
    {
        $user = $request->user()->load('kecamatan');

        return response()->json([
            'user' => [
                'id'            => $user->id,
                'username'      => $user->username,
                'name'          => $user->name,
                'email'         => $user->email,
                'role'          => $user->role,
                'avatar_url'    => $user->avatar_url,
                'no_hp'         => $user->no_hp,
                'tanggal_lahir' => $user->tanggal_lahir,
                'jenis_kelamin' => $user->jenis_kelamin,
                'kecamatan'     => $user->kecamatan?->nama,
            ],
        ]);
    }
}