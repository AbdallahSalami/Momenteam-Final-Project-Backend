<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Validate the request data
            $request->validate([
                'memberId' => 'required|exists:memberDetails,id',
                'title' => 'required|string',
                'description' => 'required|string',
                'image' => 'nullable|file|image|max:2048', // Validate the image if provided
            ]);
    
            // Create a new article
            $article = Article::create([
                'memberId' => $request->memberId,   
                'title' => $request->title,
                'description' => $request->description,
                'status' => 'draft', // Assuming 'draft' is the initial status
            ]);
    
            // Check if an image file is provided in the request
            if ($request->hasFile('image')) {
                // Store the image and get the stored image name
                $imagePath = $request->file('image')->store('public/images');
                $imageName = basename($imagePath);
    
                // Update the article with the image name
                $article->image = $imageName;
                $article->save();
            }
    
            // Return a success response
            return response()->json(['message' => 'Article created successfully', 'article' => $article], 201);
        } catch (Exception $e) {
            // Log the error
            Log::error('Error creating article: ' . $e->getMessage());
            // Log the exception for detailed debugging
            Log::error('Exception: ', ['exception' => $e]);
    
            // Return a generic error response
            return response()->json(['message' => 'An error occurred while creating the article'], 500);
        }
    }
    
    public function index()
    {
        try {
            $articles = Article::with(['member', 'scientificAuditor', 'linguisticChecker', 'socialMedia'])->get();
            return response()->json(['articles' => $articles],   200);
        } catch (Exception $e) {
            Log::error('Error retrieving articles: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while retrieving articles'],   500);
        }
    }

    public function show($id)
    {
        try {
            $article = Article::with(['member', 'scientificAuditor', 'linguisticChecker', 'socialMedia'])->findOrFail($id);
            return response()->json(['article' => $article],   200);
        } catch (Exception $e) {
            Log::error('Error retrieving article: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while retrieving the article'],   500);
        }
    }

 
    public function update(Request $request, $id)
    {
        try {
            $article = Article::findOrFail($id);
    
            $validationRules = [
                'status' => 'sometimes|in:submitted,reviewed,approved,published', // Make 'status' optional
                'image' => 'nullable|file|image|max:2048', // Validate the image if provided
            ];
    
            // Validate the request data based on the current status of the article
            if ($article->status === 'draft' && $request->status === 'submitted') {
                $validationRules = array_merge($validationRules, [
                    'title' => 'required|string',
                    'description' => 'required|string',
                ]);
            } elseif ($article->status === 'submitted') {
                $validationRules = array_merge($validationRules, [
                    'scientificAuditorId' => 'required|exists:memberDetails,id',
                    'scientificAuditorApprovelDate' => 'required|date',
                ]);
            } elseif ($article->status === 'approved') {
                $validationRules = array_merge($validationRules, [
                    'linguisticCheckerId' => 'required|exists:memberDetails,id',
                    'linguisticCheckerApprovelDate' => 'required|date',
                ]);
            } elseif ($article->status === 'reviewed') {
                $validationRules = array_merge($validationRules, [
                    'socialMediaId' => 'required|exists:memberDetails,id',
                    'socialMediaApprovelDate' => 'required|date',
                ]);
            }
    
            $request->validate($validationRules);
    
            // Update the article based on the current status
            if ($article->status === 'draft' && $request->status === 'submitted') {
                $article->update([
                    'status' => 'submitted',
                    'title' => $request->title,
                    'description' => $request->description,
                ]);
            } elseif ($article->status === 'submitted') {
                $article->update([
                    'scientificAuditorId' => $request->scientificAuditorId,
                    'scientificAuditorApprovelDate' => $request->scientificAuditorApprovelDate,
                    'status' => 'approved',
                ]);
            } elseif ($article->status === 'approved') {
                $article->update([
                    'linguisticCheckerId' => $request->linguisticCheckerId,
                    'linguisticCheckerApprovelDate' => $request->linguisticCheckerApprovelDate,
                    'status' => 'reviewed',
                ]);
            } elseif ($article->status === 'reviewed') {
                $article->update([
                    'socialMediaId' => $request->socialMediaId,
                    'socialMediaApprovelDate' => $request->socialMediaApprovelDate,
                    'status' => 'published',
                ]);
            }
    
            if ($request->hasFile('image')) {
                // Delete the old image if it exists
                if ($article->image) {
                    Storage::delete('public/images/' . $article->image);
                }
    
                // Store the new image and update the path
                $imagePath = $request->file('image')->store('public/images');
                $imageName = basename($imagePath);
                $article->image = $imageName;
            }
    
            $article->save();
    
            return response()->json(['message' => 'Article updated successfully', 'article' => $article], 200);
        } catch (Exception $e) {
            Log::error('Error updating article: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while updating the article'], 500);
        }
    }
    
    public function destroy($id)
    {
        try {
            $article = Article::findOrFail($id);
            $article->delete();

            return response()->json(['message' => 'Article deleted successfully'],   200);
        } catch (Exception $e) {
            Log::error('Error deleting article: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while deleting the article'],   500);
        }
    }
}
