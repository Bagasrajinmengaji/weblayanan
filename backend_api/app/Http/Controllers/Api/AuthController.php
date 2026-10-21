<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login menggunakan Email / Username dan Password (khusus Admin & Mahasiswa manual).
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])
            ->orWhere('nim', $validated['email'])
            ->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email / Username atau password yang Anda masukkan salah.',
            ], 401);
        }

        // Generate personal access token Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil.',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role ?? 'mahasiswa',
                'nim' => $user->nim,
                'avatar' => $user->avatar ?? 'https://api.dicebear.com/7.x/bottts/svg?seed=' . urlencode($user->email),
            ],
        ]);
    }

    /**
     * Login / Registrasi Otomatis via SSO Google (Mahasiswa).
     */
    public function googleLogin(Request $request): JsonResponse
    {
        $email = null;
        $name = null;
        $googleId = null;
        $avatar = null;

        // Mendukung Google Identity Services JWT credential
        if ($request->filled('credential')) {
            $credential = $request->input('credential');
            $parts = explode('.', $credential);
            if (count($parts) >= 2) {
                $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
                if ($payload && isset($payload['email'])) {
                    $email = $payload['email'];
                    $name = $payload['name'] ?? explode('@', $email)[0];
                    $googleId = $payload['sub'] ?? null;
                    $avatar = $payload['picture'] ?? null;
                }
            }
        }

        // Fallback jika dikirim objek langsung
        if (! $email && $request->filled('email')) {
            $email = $request->input('email');
            $name = $request->input('name', explode('@', $email)[0]);
            $googleId = $request->input('google_id', Str::random(16));
            $avatar = $request->input('avatar');
        }

        if (! $email) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data profil Google tidak valid.',
            ], 422);
        }

        // Ekstrak NIM dari email kampus jika berformat nim@students...
        $nim = null;
        if (preg_match('/^([0-9]{8,15})@/i', $email, $matches)) {
            $nim = $matches[1];
        }

        // Cari atau daftarkan akun mahasiswa baru
        $user = User::where('email', $email)->first();

        if (! $user) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'role' => 'mahasiswa',
                'nim' => $nim,
                'google_id' => $googleId,
                'avatar' => $avatar ?? 'https://api.dicebear.com/7.x/bottts/svg?seed=' . urlencode($email),
                'password' => Hash::make(Str::random(32)),
            ]);
        } else {
            // Update info google terbaru
            $user->google_id = $googleId ?? $user->google_id;
            if ($avatar && ! $user->avatar) {
                $user->avatar = $avatar;
            }
            if ($nim && ! $user->nim) {
                $user->nim = $nim;
            }
            $user->save();
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login SSO Google berhasil.',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role ?? 'mahasiswa',
                'nim' => $user->nim,
                'avatar' => $user->avatar ?? 'https://api.dicebear.com/7.x/bottts/svg?seed=' . urlencode($user->email),
            ],
        ]);
    }

    /**
     * Registrasi Akun Mahasiswa Baru Manual.
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'nim' => 'required|string|max:30|unique:users,nim',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nim' => $validated['nim'],
            'role' => 'mahasiswa',
            'password' => Hash::make($validated['password']),
            'avatar' => 'https://api.dicebear.com/7.x/bottts/svg?seed=' . urlencode($validated['email']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Registrasi berhasil.',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'nim' => $user->nim,
                'avatar' => $user->avatar,
            ],
        ], 201);
    }

    /**
     * Mengambil Sesi Pengguna Saat Ini.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Belum terautentikasi.',
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role ?? 'mahasiswa',
                'nim' => $user->nim,
                'avatar' => $user->avatar ?? 'https://api.dicebear.com/7.x/bottts/svg?seed=' . urlencode($user->email),
            ],
        ]);
    }

    /**
     * Logout & Revoke Token Sesi.
     */
    public function logout(Request $request): JsonResponse
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Logout berhasil.',
        ]);
    }
}
