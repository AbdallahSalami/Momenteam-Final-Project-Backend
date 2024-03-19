<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;


class UserController extends Controller
{
    public function index()
    {
        try {
            $users = User::with('role')->get();
            return response()->json(['message' => 'Users retrieved successfully.', 'data' => $users]);
        } catch (Exception $e) {
            Log::error('Error fetching Users: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching Users.'],  500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'username' => 'required|max:255|unique:users', // Ensure the username is unique in the users table
                'email' => 'required|email|unique:users', // Ensure the email is unique in the users table
                'password' => 'required|min:8',
                'firstName' => 'required|max:255',
                'secondName' => 'required|max:255',
                'lastName' => 'required|max:255',
                'highestDegree' => 'required|string|max:255',
                'major' => 'required|string|max:255',
                'educationalInstitution' => 'required|max:255',
                'phoneNumber' => 'required|integer|unique:users', // Ensure the phone number is unique in the users table
                'emailVerification' => 'required|max:255',
                'roleId' => 'nullable|exists:roles,id', // Allow roleId to be nullable
                'status' => 'required|in:active,inactive,pending',
            ]);
    
            $validatedData['password'] = Hash::make($validatedData['password']);

            $user = User::create($validatedData);
    
            return response()->json(['message' => 'User created successfully.', 'data' => $user],   201);
        } catch (ValidationException $e) {
            $errors = $e->errors();
            $errorMessages = [];
    
            if (isset($errors['username'])) {
                $errorMessages[] = 'You have entered an invalid username.';
            }
            if (isset($errors['email'])) {
                $errorMessages[] = 'You have entered an invalid email.';
            }
            if (isset($errors['phoneNumber'])) {
                $errorMessages[] = 'You have entered an invalid phone number.';
            }
            if (isset($errors['password'])) {
                $errorMessages[] = 'The password must be at least   8 characters long.';
            }
    
            $errorMessage = implode(', ', $errorMessages);
    
            Log::error('Error creating User: ' . $e->getMessage());
            return response()->json(['error' => $errorMessage],   422);
        } catch (Exception $e) {
            Log::error('Error creating User: ' . $e->getMessage());
            return response()->json(['error' => 'Error creating User.'],   500);
        }
    }
    
    public function show(User $user)
    {
        try {
            $user->load('role'); // Load the role relationship
            return response()->json(['message' => 'User retrieved successfully.', 'data' => $user]);
        } catch (Exception $e) {
            Log::error('Error fetching User: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching User.'],  500);
        }
    }

    public function update(Request $request, User $user, $fieldsToUpdate = null)
    {
        try {
            // Define the validation rules for each field
            $validationRules = [
                'username' => 'sometimes|required|max:255|unique:users,username,' . $user->id,
                'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
                'password' => 'sometimes|required|min:8',
                'firstName' => 'sometimes|required|max:255',
                'secondName' => 'sometimes|required|max:255',
                'lastName' => 'sometimes|required|max:255',
                'highestDegree' => 'sometimes|required|string|max:255',
                'major' => 'sometimes|required|string|max:255',
                'educationalInstitution' => 'sometimes|required|max:255',
                'phoneNumber' => 'sometimes|required|integer|unique:users,phoneNumber,' . $user->id,
                'emailVerification' => 'sometimes|required|max:255',
                'roleId' => 'sometimes|nullable|exists:roles,id',
                'status' => 'sometimes|required|in:active,inactive,pending',
            ];
    
            // If specific fields are provided, only validate those fields
            if ($fieldsToUpdate) {
                $validationRules = array_intersect_key($validationRules, array_flip($fieldsToUpdate));
            }
    
            // Validate the request data
            $validatedData = $request->validate($validationRules);
    
            // Hash the password if i   t's being updated
            if ($request->has('password')) {
                $validatedData['password'] = Hash::make($validatedData['password']);
            }
    
            // Update the user with the validated data
            $user->update($validatedData);
    
            return response()->json(['message' => 'User updated successfully.', 'data' => $user]);
        } catch (ValidationException $e) {
            $errors = $e->errors();
            $errorMessages = [];
    
            if (isset($errors['username'])) {
                $errorMessages[] = 'You have entered an invalid username.';
            }
            if (isset($errors['email'])) {
                $errorMessages[] = 'You have entered an invalid email.';
            }
            if (isset($errors['phoneNumber'])) {
                $errorMessages[] = 'You have entered an invalid phone number.';
            }
            if (isset($errors['password'])) {
                $errorMessages[] = 'The password must be at least  8 characters long.';
            }
    
            $errorMessage = implode(', ', $errorMessages);
    
            Log::error('Error updating User: ' . $e->getMessage());
            return response()->json(['error' => $errorMessage],  422);
        } catch (Exception $e) {
            Log::error('Error updating User: ' . $e->getMessage());
            return response()->json(['error' => 'Error updating User.'],  500);
        }
    }
    
    

    public function destroy(User $user)
    {
        try {
            $user->delete();
            return response()->json(['message' => 'User deleted successfully.'],   200);
        } catch (Exception $e) {
            Log::error('Error deleting User: ' . $e->getMessage());
            return response()->json(['error' => 'Error deleting User.'],   500);
        }
    }
    
}
