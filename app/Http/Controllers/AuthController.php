<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // LOGIC REGISTER
    public function register(Request $request)
    {
        // 1. Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 2. Simpan ke database
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Password di-enkripsi
        ]);

        return response()->json(['message' => 'User berhasil dibuat!'], 201);
    }

    // LOGIC LOGIN
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Cek email dan password, jika benar dapat token
        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Email atau Password salah!'], 401);
        }

        return response()->json([
            'message' => 'Login Berhasil',
            'token' => $token,
            'type' => 'bearer'
        ]);
    }

    public function logout()
{
    auth()->logout();
    return response()->json(['message' => 'Berhasil keluar (Logout)']);
}
}