<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Ensure the request data is in the correct format
            $data = $request->validate([
                'memberId' => 'required|integer',
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'location' => 'required|string',
                'date' => 'required|date',
                'status' => 'required|string|in:active,inactive',
            ]);

            $event = Event::create($data);
            return response()->json($event, 201);
        } catch (Exception $e) {
            Log::error('Error creating event: ', ['exception' => $e]);
            return response()->json(['error' => 'Error creating event.'], 500);
        }
    }

    // Read (GET)
    public function index()
    {
        try {
            $events = Event::all();
            return response()->json($events);
        } catch (Exception $e) {
            Log::error('Error retrieving events: ' . $e->getMessage());
            return response()->json(['error' => 'Error retrieving events.'],  500);
        }
    }

    // Show (GET)
    public function show(Event $event)
    {
        try {
            return response()->json($event);
        } catch (Exception $e) {
            Log::error('Error retrieving event: ' . $e->getMessage());
            return response()->json(['error' => 'Error retrieving event.'],  500);
        }
    }

    // Update (PUT/PATCH)
    public function update(Request $request, Event $event)
    {
        try {
            $event->update($request->all());
            return response()->json($event);
        } catch (Exception $e) {
            Log::error('Error updating event: ' . $e->getMessage());
            return response()->json(['error' => 'Error updating event.'],  500);
        }
    }

    // Delete (DELETE)
    public function destroy(Event $event)
    {
        try {
            $event->delete();
            return response()->json(['message' => 'Event deleted successfully.'],  200);
        } catch (Exception $e) {
            Log::error('Error deleting event: ' . $e->getMessage());
            return response()->json(['error' => 'Error deleting event.'],  500);
        }
    }
}
