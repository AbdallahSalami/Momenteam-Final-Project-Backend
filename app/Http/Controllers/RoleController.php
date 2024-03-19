<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    /**
     * Store a newly created role in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'name' => 'required|max:255|unique:roles', // Ensure the name is unique in the roles table
                'confirm' => 'required|boolean',
                'private' => 'required|boolean',
            ], [
                'name.unique' => 'The role name has already been taken.', // Custom error message for unique name validation
            ]);
    
            // If validation passes, create the role
            $role = Role::create($validatedData);
    
            return response()->json(['message' => 'Role created successfully.', 'data' => $role],   201);
        } catch (Exception $e) {
            Log::error('Error creating role: ' . $e->getMessage());
            return response()->json(['error' => 'Error creating role.'],   500);
        }
    }
    

    /**
     * Display a listing of the roles.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $roles = Role::all();

            return response()->json(['message' => 'Roles retrieved successfully.', 'data' => $roles]);
        } catch (Exception $e) {
            Log::error('Error fetching roles: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching roles.'],   500);
        }
    }

    /**
     * Display the specified role.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Role $role)
    {
        try {
            return response()->json(['message' => 'Role retrieved successfully.', 'data' => $role]);
        } catch (Exception $e) {
            Log::error('Error fetching role: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching role.'],   500);
        }
    }

    /**
     * Update the specified role in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Role $role)
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'name' => 'required|max:255|unique:roles,name,' . $role->id, // Ensure the name is unique in the roles table, except for the current role
                'confirm' => 'sometimes|required|boolean',
                'private' => 'sometimes|required|boolean',
            ], [
                'name.unique' => 'The role name has already been taken.', // Custom error message for unique name validation
            ]);
    
            // If validation passes, update the role
            $role->update($validatedData);
    
            return response()->json(['message' => 'Role updated successfully.', 'data' => $role]);
        } catch (Exception $e) {
            Log::error('Error updating role: ' . $e->getMessage());
            return response()->json(['error' => 'Error updating role.'],   500);
        }
    }
    
    /**
     * Remove the specified role from storage.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Role $role)
    {
        try {
            $role->delete();

            return response()->json(['message' => 'Role deleted successfully.'],   200);
        } catch (Exception $e) {
            Log::error('Error deleting role: ' . $e->getMessage());
            return response()->json(['error' => 'Error deleting role.'],   500);
        }
    }
}
