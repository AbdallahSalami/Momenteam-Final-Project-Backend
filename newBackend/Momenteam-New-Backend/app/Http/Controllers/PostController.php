<?php
namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{  public function store(Request $request)
    {
        try {
            $request->validate([
                'memberId' => 'required|exists:memberDetails,id',
                'title' => 'required|string',
                'description' => 'required|string',
                'image' => 'nullable|file|image|max:2048', // Validate the image if provided
            ]);

            $post = Post::create([
                'memberId' => $request->memberId,
                'title' => $request->title,
                'description' => $request->description,
                'status' => 'draft',
            ]);

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('public/images');
                $imageName = basename($imagePath);
                $post->image = $imageName;
                $post->save();
            }

            return response()->json(['message' => 'Post created successfully', 'post' => $post], 201);
        } catch (Exception $e) {
            Log::error('Error creating post: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while creating the post'], 500);
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
            Log::info('Update request data:', $request->all());
    
            // Initialize validation rules
            $validationRules = [
                'status' => 'sometimes|in:submitted,reviewed,approved,published', // Make 'status' optional
                'image' => 'nullable|file|image|max:2048', // Ensure this matches the frontend
            ];
    
            // Determine additional validation rules based on the current status of the post
            if ($post->status === 'draft' && $request->status === 'submitted') {
                // No additional validation rules needed for the member to submit their post
            } elseif ($post->status === 'submitted') {
                $validationRules = array_merge($validationRules, [
                    'scientificAuditorId' => 'required|exists:memberDetails,id',
                    'scientificAuditorApprovelDate' => 'required|date',
                ]);
            } elseif ($post->status === 'approved') {
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
    
            // Validate the request data
            $validatedData = $request->validate($validationRules);
    
            // Handle image update if a new image is provided
            if ($request->hasFile('image')) {
                // Delete the old image if it exists
                if ($post->image) {
                    Storage::delete('public/images/' . $post->image);
                }
    
                // Store the new image and update the path
                $imagePath = $request->file('image')->store('public/images');
                $imageName = basename($imagePath);
                $post->image = $imageName;
            }
    
            // Update the post based on the current status
            if ($request->has('status')) {
                $post->status = $request->status;
            }
    
            // Save the post after updating the status and image
            $post->save();
    
            return response()->json(['message' => 'Post updated successfully', 'post' => $post], 200);
        } catch (Exception $e) {
            // Log the exception for debugging purposes
            Log::error('Error updating post: ' . $e->getMessage());
    
            // Return a generic error response
            return response()->json(['message' => 'An error occurred while updating the post'], 500);
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