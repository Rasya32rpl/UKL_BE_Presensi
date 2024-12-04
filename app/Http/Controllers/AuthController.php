<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // Model User
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth; // JWT facade

class AuthController extends Controller
{
    // Fungsi untuk Register User
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:siswa,admin'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Membuat token untuk user yang baru dibuat
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil mendaftar',
            'user' => $user,
            'token' => $token
        ], 201);
    }

    // Fungsi untuk Login User
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->only('email', 'password');

        // Verifikasi kredensial dan buat token
        if ($token = JWTAuth::attempt($credentials)) {
            $user = JWTAuth::user(); // Ambil data user setelah login berhasil

            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil login',
                'token' => $token,
                'user' => $user
            ]);
        } else {
            // Jika kredensial salah
            return response()->json([
                'status' => 'error',
                'message' => 'Email atau password salah'
            ], 401);
        }
    }

    // Fungsi untuk mendapatkan user yang sedang login
    public function getAuthenticatedUser()
    {
        try {
            // Autentikasi token
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Token tidak valid'], 401);
        }

        return response()->json(compact('user'));
    }
}
