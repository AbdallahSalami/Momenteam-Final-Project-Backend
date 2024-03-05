<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;

class ArticleController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'memberId' => 'required|exists:memberDetails,id',
                'title' => 'required|string',
                'description' => 'required|string',
                'image' => 'nullable|string', // Validate the image if provided
            ]);

            $article = Article::create([
                'memberId' => $request->memberId,
                'title' => $request->title,
                'description' => $request->description,
                'image' => $request->image, // Store the image path if provided
                'status' => 'draft',
            ]);

            return response()->json(['message' => 'Article created successfully', 'article' => $article],   201);
        } catch (Exception $e) {
            Log::error('Error creating article: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while creating the article'],   500);
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

            // Validate the request data based on the current status of the article
            $validationRules = [
                'status' => 'sometimes|in:submitted,reviewed,approved,published', // Make 'status' optional
                'image' => 'nullable|string', // Validate the image if provided
            ];

            // If the member is confirming their own article, they can only update the status to 'submitted'
            if ($article->status === 'draft' && $request->status === 'submitted') {
                // No additional validation rules needed for the member to submit their article
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
                // The member is confirming their own article, so we only update the status
                $article->update([
                    'status' => 'submitted',
                    'title' => $request->title ?? $article->title,
                    'description' => $request->description ?? $article->description,
                    'image' => $request->image ?? $article->image, // Update the image if provided
               
                ]);
                return response()->json(['message' => 'Article status updated to submitted', 'article' => $article],   200);
            } elseif ($article->status === 'submitted') {
                $article->update([
                    'scientificAuditorId' => $request->scientificAuditorId,
                    'scientificAuditorApprovelDate' => $request->scientificAuditorApprovelDate,
                    'status' => 'approved',
                    'title' => $request->title ?? $article->title,
                    'description' => $request->description ?? $article->description,
                    'image' => $request->image ?? $article->image, // Update the image if provided
                ]);
                return response()->json(['message' => 'Article status updated to approved', 'article' => $article],   200);
            } elseif ($article->status === 'approved') {
                $article->update([
                    'linguisticCheckerId' => $request->linguisticCheckerId,
                    'linguisticCheckerApprovelDate' => $request->linguisticCheckerApprovelDate,
                    'status' => 'reviewed',
                    'title' => $request->title ?? $article->title,
                    'description' => $request->description ?? $article->description,
                    'image' => $request->image ?? $article->image, // Update the image if provided
                ]);
                return response()->json(['message' => 'Article status updated to reviewed', 'article' => $article],   200);
            } elseif ($article->status === 'reviewed') {
                $article->update([
                    'socialMediaId' => $request->socialMediaId,
                    'socialMediaApprovelDate' => $request->socialMediaApprovelDate,
                    'status' => 'published',
                    'title' => $request->title ?? $article->title,
                    'description' => $request->description ?? $article->description,
                    'image' => $request->image ?? $article->image, // Update the image if provided
                ]);
                return response()->json(['message' => 'Article status updated to published', 'article' => $article],   200);
            }

            return response()->json(['message' => 'No valid update parameters provided'],   400);
        } catch (Exception $e) {
            // Log the exception for debugging purposes
            Log::error('Error updating article: ' . $e->getMessage());

            // Return a generic error response
            return response()->json(['message' => 'An error occurred while updating the article' . $e->getMessage()],   500);
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
