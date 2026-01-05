<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string|max:20|unique:users',
            'email' => 'required|string|email|max:100|unique:users',
            'name' => 'required|string|max:100',
            'password' => 'required|string|min:8',
            'gender' => 'boolean',
            'birthday' => 'date',
        ]);

        $user = User::create([
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'name' => $request->name,
            'password' => Hash::make($request->password),
            'gender' => $request->gender ?? true,
            'birthday' => $request->birthday ?? now(),
            'role' => true,
            'is_admin' => false,
            'is_active' => true,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$user->is_active) {
            return response()->json([
                'message' => 'Account is inactive.'
            ], 403);
        }

        if ($user->is_admin == 1 || ($user->is_admin == 0 && $user->role == 0)) {
            return response()->json([
                'message' => 'Hệ thống này chỉ dành cho người dùng.'
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:100',
            'phone_number' => 'sometimes|string|max:20|unique:users,phone_number,' . $user->id,
            'email' => 'sometimes|string|email|max:100|unique:users,email,' . $user->id,
            'gender' => 'sometimes|boolean',
            'birthday' => 'sometimes|date',
            'avatar_id' => 'nullable|exists:images,id',
            'cover_image_id' => 'nullable|exists:images,id',
        ]);

        $user->update($validated);
        $user->load(['avatar', 'coverImage']);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8',
            'password_confirmation' => 'required|same:new_password',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect'
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'message' => 'Password changed successfully'
        ]);
    }

    public function deleteAccount(Request $request)
    {
        $user = $request->user();

        $user->update([
            'is_active' => false
        ]);

        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Account deactivated successfully'
        ]);
    }
}
