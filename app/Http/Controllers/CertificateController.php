<?php
namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;

class CertificateController extends Controller
{
    // Create a new certificate
    public function store(Request $request)
    {
        try {
            $request->validate([
                'userId' => 'exists:users,id',
                'eventId' => 'exists:events,id', // Ensure the event exists
                'title' => 'required|string',
                'description' => 'required|string',
            ]);
    
            $certificate = Certificate::create([
                'userId' => $request->userId,
                'eventId' => $request->eventId, // Store the eventId
                'title' => $request->title,
                'description' => $request->description,
                'date' => now(),
                'status' => 'pending',
            ]);
    
            return response()->json(['message' => 'Certificate created successfully', 'certificate' => $certificate],   201);
        } catch (Exception $e) {
            Log::error('Error creating certificate: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while creating the certificate'],   500);
        }
    }
    
    public function index()
    {
        try {
            $certificates = Certificate::with([
                'user' => function ($query) {
                    $query->with('role'); // Eager load the role relationship for the user
                },
                'event' => function ($query) {
                    $query->select('id', 'title'); // Select only the id and title from the event
                },
                'secretary' => function ($query) {
                    $query->select('id', 'userId')->with(['user' => function ($query) {
                        $query->select('id', 'username', 'email', 'roleId')->with(['role' => function ($query) {
                            $query->select('id', 'name', 'confirm');
                        }]);
                    }]);
                },
                'manager' => function ($query) {
                    $query->select('id', 'userId')->with(['user' => function ($query) {
                        $query->select('id', 'username', 'email', 'roleId')->with(['role' => function ($query) {
                            $query->select('id', 'name', 'confirm');
                        }]);
                    }]);
                }
            ])->get();
    
            return response()->json(['certificates' => $certificates],   200);
        } catch (Exception $e) {
            Log::error('Error retrieving certificates: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while retrieving certificates'],   500);
        }
    }
    

    public function show($id)
    {
        try {
            $certificate = Certificate::with([
                'user' => function ($query) {
                    $query->with('role'); // Eager load the role relationship for the user
                },
                'event' => function ($query) {
                    $query->select('id', 'title'); // Select only the id and title from the event
                },
                'secretary' => function ($query) {
                    $query->select('id', 'userId')->with(['user' => function ($query) {
                        $query->select('id', 'username', 'email', 'roleId')->with(['role' => function ($query) {
                            $query->select('id', 'name', 'confirm');
                        }]);
                    }]);
                },
                'manager' => function ($query) {
                    $query->select('id', 'userId')->with(['user' => function ($query) {
                        $query->select('id', 'username', 'email', 'roleId')->with(['role' => function ($query) {
                            $query->select('id', 'name', 'confirm');
                        }]);
                    }]);
                }
            ])->findOrFail($id);
    
            return response()->json(['certificate' => $certificate],   200);
        } catch (Exception $e) {
            Log::error('Error retrieving certificate: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while retrieving the certificate'],   500);
        }
    }
    
    
    
    // Update a certificate by ID
  public function update(Request $request, $id)
  {
      try {
          $certificate = Certificate::findOrFail($id);
  
          // Validate the request data based on the current status of the certificate
          $validationRules = [
              'status' => 'sometimes|in:waiting,approved,sended', // Make 'status' optional
          ];
  
          if ($certificate->status === 'pending') {
              $validationRules = array_merge($validationRules, [
                  'secretaryId' => 'required|exists:memberDetails,id',
                  'secretaryFirstDate' => 'required|date',
              ]);
          } elseif ($certificate->status === 'waiting') {
              $validationRules = array_merge($validationRules, [
                  'managerId' => 'required|exists:memberDetails,id',
                  'managerApprovelDate' => 'required|date',
              ]);
          } elseif ($certificate->status === 'approved') {
              $validationRules = array_merge($validationRules, [
                  'secretarySecondDate' => 'required|date',
              ]);
          }
  
          $request->validate($validationRules);
  
          // Update the certificate based on the current status
          if ($certificate->status === 'pending') {
              $certificate->update([
                  'secretaryId' => $request->secretaryId,
                  'secretaryFirstDate' => $request->secretaryFirstDate,
                  'status' => 'waiting',
              ]);
              return response()->json(['message' => 'Certificate status updated to waiting', 'certificate' => $certificate],  200);
          } elseif ($certificate->status === 'waiting') {
              $certificate->update([
                  'managerId' => $request->managerId,
                  'managerApprovelDate' => $request->managerApprovelDate,
                  'status' => 'approved',
              ]);
              return response()->json(['message' => 'Certificate status updated to approved', 'certificate' => $certificate],  200);
          } elseif ($certificate->status === 'approved') {
              $certificate->update([
                  'secretarySecondDate' => $request->secretarySecondDate,
                  'status' => 'sended',
              ]);
              return response()->json(['message' => 'Certificate status updated to sended', 'certificate' => $certificate],  200);
          }
  
          return response()->json(['message' => 'No valid update parameters provided'],  400);
      } catch (\Exception $e) {
          // Log the exception for debugging purposes
          Log::error('Error updating certificate: ' . $e->getMessage());
  
          // Return a generic error response
          return response()->json(['message' => 'An error occurred while updating the certificate'],  500);
      }
  }
  


    // Delete a certificate by ID
    public function destroy($id)
    {
        try {   
            $certificate = Certificate::findOrFail($id);
            $certificate->delete();

            return response()->json(['message' => 'Certificate deleted successfully'],   200);
        } catch (Exception $e) {
            Log::error('Error deleting certificate: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while deleting the certificate'],   500);
        }
    }
}
