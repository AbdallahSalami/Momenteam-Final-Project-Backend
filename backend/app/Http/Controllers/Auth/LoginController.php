<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Exception;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                // Throw an exception with a message only
                throw new Exception('Invalid credentials');
            }

            // Log the user in using the Auth facade
            Auth::login($user);

            // Generate a token for the authenticated user using Sanctum
            $token = $user->createToken('authToken')->plainTextToken;

            // Prepare the response data
            $responseData = [
                'message' => 'User logged in successfully',
                'userId' => $user->id,
                'username' => $user->username,
                'roleId' => $user->roleId,
                'token' => $token,
               

            ];

            // Log the response data
            Log::info('Login response:', $responseData);

            // Create the response with a   200 status code
            $response = response()->json($responseData,   200);

            // Log the status code of the response
            Log::info('Response status code:', ['statusCode' => $response->getStatusCode()]);

            return $response;
        } catch (Exception $e) {
            // Log the exception if needed
            Log::error($e->getMessage());

            // Return a JSON response with the error message and status code   401 for unauthorized
            return response()->json(['message' => $e->getMessage()],   401);
        }
    }
}
