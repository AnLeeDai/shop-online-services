<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash, Log};
use Illuminate\Validation\ValidationException;
use App\Models\{User, Role};

class AuthenticationController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|min:4',
                'email' => 'required|email|max:255|unique:users,email',
                'password' => 'required|string|min:8',
                'role_id' => 'nullable|integer|exists:roles,id',
            ]);

            $roleId = $validated['role_id']
                ?? Role::firstOrCreate(['role_name' => 'user'])->id;

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'avatar' => 'https://avatar.iran.liara.run/public',
                'password' => Hash::make($validated['password']),
                'role_id' => $roleId,
            ]);

            $scope = $user->role->role_name === 'admin'
                ? ['admin:*'] : ['user:*'];

            $token = $user->createToken('authToken', $scope)->plainTextToken;

            return response()->json([
                'response_code' => 201,
                'status' => 'success',
                'message' => 'Registration successful',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role->role_name,
                    'avatar' => $user->avatar,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ], 201);

        } catch (ValidationException $e) {
            return $this->validationError($e);
        } catch (\Throwable $e) {
            Log::error('Registration Error: ' . $e->getMessage());
            return $this->serverError('Registration failed');
        }
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            if (!Auth::guard('web')->attempt($credentials)) {
                return $this->unauthorized();
            }

            $user = Auth::user();
            $scope = $user->role_id === 1
                ? ['admin:*']
                : ['user:*'];

            $token = $user->createToken('authToken', $scope)->plainTextToken;

            return response()->json([
                'response_code' => 200,
                'status' => 'success',
                'message' => 'Login successful',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role->role_name,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ]);

        } catch (ValidationException $e) {
            return $this->validationError($e);
        } catch (\Throwable $e) {
            Log::error('Login Error: ' . $e->getMessage());
            return $this->serverError('Login failed');
        }
    }

    public function userInfo(Request $request)
    {
        $users = User::with('role')
            ->where('role_id', '!=', 1)
            ->latest()
            ->paginate(10);

        return response()->json([
            'response_code' => 200,
            'status' => 'success',
            'message' => 'Fetched user list successfully',
            'data' => $users,
        ]);
    }

    public function logOut(Request $request)
    {
        try {
            $request->user()->tokens()->delete();

            return response()->json([
                'response_code' => 200,
                'status' => 'success',
                'message' => 'Successfully logged out',
            ]);
        } catch (\Throwable $e) {
            Log::error('Logout Error: ' . $e->getMessage());
            return $this->serverError('Logout failed');
        }
    }

    private function validationError(ValidationException $e)
    {
        return response()->json([
            'response_code' => 422,
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $e->errors(),
        ], 422);
    }

    private function unauthorized($msg = 'Unauthorized')
    {
        return response()->json([
            'response_code' => 401,
            'status' => 'error',
            'message' => $msg,
        ], 401);
    }

    private function serverError($msg)
    {
        return response()->json([
            'response_code' => 500,
            'status' => 'error',
            'message' => $msg,
        ], 500);
    }
}
