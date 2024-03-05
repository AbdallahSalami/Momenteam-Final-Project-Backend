<?php
namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{ public function store(Request $request)
    {
        try {
            $request->validate([
                'memberId' => 'required|exists:memberDetails,id',
                'title' => 'required|string',
                'description' => 'required|string',
            ]);

            $post = Post::create([
                'memberId' => $request->memberId,
                'title' => $request->title,
                'description' => $request->description,
                'status' => 'draft',
            ]);

            return response()->json(['message' => 'Post created successfully', 'post' => $post],   201);
        } catch (Exception $e) {
            Log::error('Error creating post: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while creating the post'],   500);
        }
    }
    public function index()
    {
        try {
            $posts = Post::with(['member', 'scientificAuditor', 'linguisticChecker', 'socialMedia'])->get();
            return response()->json(['posts' => $posts],   200);
        } catch (Exception $e) {
            Log::error('Error retrieving posts: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while retrieving posts'],   500);
        }
    }

   
    public function show($id)
    {
        try {
            $post = Post::with(['member', 'scientificAuditor', 'linguisticChecker', 'socialMedia'])->findOrFail($id);
            return response()->json(['post' => $post],   200);
        } catch (Exception $e) {
            Log::error('Error retrieving post: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while retrieving the post'],   500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $post = Post::findOrFail($id);
    
            // Validate the request data based on the current status of the post
            $validationRules = [
                'status' => 'sometimes|in:submitted,reviewed,approved,published', // Make 'status' optional
            ];
    
            // If the member is confirming their own post, they can only update the status to 'submitted'
            if ($post->status === 'draft' && $request->status === 'submitted') {
                // No additional validation rules needed for the member to submit their post
            }elseif ($post->status === 'submitted') {
                $validationRules = array_merge($validationRules, [
                    'scientificAuditorId' => 'required|exists:memberDetails,id',
                    'scientificAuditorApprovelDate' => 'required|date',
                  
                ]);
            } 
            elseif ($post->status === 'approved') {
                $validationRules = array_merge($validationRules, [
                    'linguisticCheckerId' => 'required|exists:memberDetails,id',
                    'linguisticCheckerApprovelDate' => 'required|date',
                ]);
            } elseif ($post->status === 'reviewed') {
                $validationRules = array_merge($validationRules, [
                    'socialMediaId' => 'required|exists:memberDetails,id',
                    'socialMediaApprovelDate' => 'required|date',
                ]);
            }
    
            $request->validate($validationRules);
    
            // Update the post based on the current status
            if ($post->status === 'draft' && $request->status === 'submitted') {
                // The member is confirming their own post, so we only update the status
                $post->update([
                    'status' => 'submitted',
                    'status' => 'approved',
                    'title' => $request->title ?? $post-> title,

                    'description' => $request->description ?? $post-> description
               
                ]);
                return response()->json(['message' => 'Post status updated to submitted', 'post' => $post],   200);
            } elseif ($post->status === 'submitted') {
                $post->update([
                    'scientificAuditorId' => $request->scientificAuditorId,
                    'scientificAuditorApprovelDate' => $request->scientificAuditorApprovelDate,
                    'status' => 'approved',
                    'title' => $request->title ?? $post-> title,

                    'description' => $request->description ?? $post-> description
                ]); 
                return response()->json(['message' => 'Post status updated to approved', 'post' => $post],   200);
            } elseif ($post->status === 'approved') {
                $post->update([
                    'linguisticCheckerId' => $request->linguisticCheckerId,
                    'linguisticCheckerApprovelDate' => $request->linguisticCheckerApprovelDate,
                    'status' => 'reviewed',
                    'title' => $request->title ?? $post-> title,
                    'description' => $request->description ?? $post-> description

                ]);
                return response()->json(['message' => 'Post status updated to reviewed', 'post' => $post],   200);
            } elseif ($post->status === 'reviewed') {
                $post->update([
                    'socialMediaId' => $request->socialMediaId,
                    'socialMediaApprovelDate' => $request->socialMediaApprovelDate,
                    'status' => 'published',
                    'title' => $request->title ?? $post-> title,
                    'description' => $request->description ?? $post-> description

                ]);
                return response()->json(['message' => 'Post status updated to published', 'post' => $post],   200);
            }
    
            return response()->json(['message' => 'No valid update parameters provided'],   400);
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            Log::error('Error updating post: ' . $e->getMessage());
    
            // Return a generic error response
            return response()->json(['message' => 'An error occurred while updating the post' . $e->getMessage()],   500);
        }
    }
    
    public function destroy($id)
    {
        try {
            $post = Post::findOrFail($id);
            $post->delete();

            return response()->json(['message' => 'Post deleted successfully'],   200);
        } catch (Exception $e) {
            Log::error('Error deleting post: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while deleting the post'],   500);
        }
    }


}