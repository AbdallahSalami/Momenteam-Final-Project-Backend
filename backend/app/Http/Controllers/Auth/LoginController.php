<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\MemberDetail;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Payload;

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
            throw new Exception('Invalid credentials');
        }

        // Check if the user has a roleId
        $memberId = null;
        if ($user->roleId) {
            // Fetch the memberId from the MemberDetail table
            $memberDetail = MemberDetail::where('userId', $user->id)->first();
            if ($memberDetail) {
                $memberId = $memberDetail->id;
                
            }
        }

        // Customize the payload
        $customClaims = [
            'sub' => $user->id,
            'username' => $user->username,
            'roleId' => $user->roleId,
            'roleName' => $user->role?->name,
            'emailVerification' => $user->emailVerification,
            'memberId' => $memberId, // Include the memberId in the custom claims
        ];

        // Generate a new token with the customized payload
        $customToken = JWTAuth::claims($customClaims)->fromUser($user);

        // Prepare the response data
        $responseData = [
            'message' => 'User logged in successfully',
            'token' => $customToken,
        ];

        // Log the response data
        Log::info('Login response:', $responseData);

        // Create the response with a 200 status code
        $response = response()->json($responseData, 200);

        // Log the status code of the response
        Log::info('Response status code:', ['statusCode' => $response->getStatusCode()]);

        return $response;
    } catch (Exception $e) {
        // Log the exception if needed
        Log::error($e->getMessage());

        // Return a JSON response with the error message and status code 401 for unauthorized
        return response()->json(['message' => $e->getMessage()], 401);
    }
}

}
