<?php

namespace App\Http\Controllers;

use App\Models\MemberDetail;
use Illuminate\Http\Request;

use Exception;
use Illuminate\Support\Facades\Log;

class MemberDetailController extends Controller
{
    
        // Create (POST)
        public function store(Request $request)
        {
            try {
                // Validate that the userId is not already associated with a MemberDetail
                $request->validate([
                    'userId' => 'required|unique:memberDetails,userId',
                    'image' => 'image|max:2048', // Validate the image file

                    // Add other validation rules as needed
                ]);
    
                // Create the MemberDetail
                $memberDetail = MemberDetail::create($request->all());
                return response()->json($memberDetail,   201);
            } catch (\Exception $e) {
                // Log the full exception stack trace for debugging
                Log::error('Error creating member detail: ' . $e->getMessage());
                // Log the full exception stack trace for debugging
                Log::error($e);
                return response()->json(['error' => 'Error creating member detail.'],   500);
            }
        }

    

        // Read (GET)
        public function index()
        {
            try {
                $memberDetails = MemberDetail::with(['user' => function ($query) {
                    $query->with('role'); // Eager load the role relationship
                }])->get();
                return response()->json($memberDetails);
            } catch (Exception $e) {
                Log::error('Error retrieving member details: ' . $e->getMessage());
                // Provide a more detailed error message
                return response()->json(['error' => 'Error retrieving member details: ' . $e->getMessage()],   500);
            }
        }



        public function show(MemberDetail $memberDetail)
        {
            try {
                // Eager load the user and the user's role
                $memberDetail->load(['user' => function ($query) {
                    $query->with('role');
                }]);

                return response()->json($memberDetail);
            } catch (Exception $e) {
                Log::error('Error retrieving member detail: ' . $e->getMessage());
                return response()->json(['error' => 'Error retrieving member detail.'],   500);
            }
        }

        // Update (PUT/PATCH)
        public function update(Request $request, MemberDetail $memberDetail)
        {
            try {
                // Validate that the userId is not already associated with another MemberDetail
                $request->validate([
                    'userId' => 'required|unique:memberDetails,userId,' . $memberDetail->id,
                    'image' => 'image|max:2048', // Validate the image file

                    // Add other validation rules as needed
                ]);

                // Update the MemberDetail
                $memberDetail->update($request->all());
                return response()->json($memberDetail);
            } catch (Exception $e) {
                Log::error('Error updating member detail: ' . $e->getMessage());
                return response()->json(['error' => 'Error updating member detail.'],   500);
            }
        }


        // Delete (DELETE)
        public function destroy(MemberDetail $memberDetail)
        {
            try {
                $memberDetail->delete();
                return response()->json(['message' => 'Member detail deleted successfully.'],  200);
            } catch (Exception $e) {
                Log::error('Error deleting member detail: ' . $e->getMessage());
                return response()->json(['error' => 'Error deleting member detail.'],  500);
            }
        }
    }
