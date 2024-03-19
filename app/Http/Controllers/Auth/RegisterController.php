<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Exception;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        try {
            // Log the request data
            Log::info('Registration request:', $request->all());

            $request->validate([
                'username' => 'required|string|max:255|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'password_confirmation' => 'required|string|same:password',
                'firstName' => 'required|string|max:255',
                'secondName' => 'required|string|max:255',
                'lastName' => 'required|string|max:255',
                'highestDegree' => 'required|string|max:255',
                'major' => 'required|string|max:255',
                'educationalInstitution' => 'required|string|max:255',
                'phoneNumber' => 'required|string|max:255|unique:users',
            ]);

            // Manually compare the password and password_confirmation fields
            if ($request->password !== $request->password_confirmation) {
                throw new Exception('The password confirmation does not match the password',   400);
            }

            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'firstName' => $request->firstName,
                'secondName' => $request->secondName,
                'lastName' => $request->lastName,
                'highestDegree' => $request->highestDegree,
                'major' => $request->major,
                'educationalInstitution' => $request->educationalInstitution,
                'phoneNumber' => $request->phoneNumber,
                'emailVerification' => 'not verificate',
                'roleId' => null,
                'status' => 'pending',
            ]);


            event(new \Illuminate\Auth\Events\Registered($user));

            // Prepare the response data
            $responseData = [
                'message' => 'User registered successfully',
                'user' => $user,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'userId' => $user->id,
                'username' => $user->username,
                'roleId' => $user->roleId,
                'roleName' => $user->role?->name,

            ];

            // Log the response data
            Log::info('Registration response:', $responseData);

            // Return the response
            return response()->json($responseData,   201);
        } catch (Exception $e) {
            // Log the exception if needed
            Log::error($e->getMessage());

            // Return a JSON response with the error message and status code
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
