<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class EventController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'memberId' => 'required|integer',
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'location' => 'required|string',
                'date' => 'required|date',
                'status' => 'required|string|in:active,inactive',
                'image' => 'nullable|file|image|max:2048', // Ensure this matches the frontend
            ]);

            $eventData = $request->all();
            $eventData['date'] = Carbon::parse($request->date)->timezone('Asia/Beirut');

            $event = Event::create($eventData);

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('public/images');
                $imageName = basename($imagePath);
                $event->image = $imageName;
                $event->save();
            }

            return response()->json($event, 201);
        } catch (Exception $e) {
            Log::error('Error creating event: ', ['exception' => $e]);
            return response()->json(['error' => 'Error creating event: ' . $e->getMessage()], 500);
        }
    }

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

    public function show(Event $event)
    {
        try {
            return response()->json($event);
        } catch (Exception $e) {
            Log::error('Error retrieving event: ' . $e->getMessage());
            return response()->json(['error' => 'Error retrieving event.'],  500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $event = Event::findOrFail($id);

            $request->validate([
                'memberId' => 'required|integer',
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'location' => 'required|string',
                'date' => 'required|date',
                'status' => 'required|string|in:active,inactive',
                'image' => 'nullable|file|image|max:2048', // Ensure this matches the frontend
            ]);

            $eventData = $request->except('image');
            $eventData['date'] = Carbon::parse($request->date)->timezone('Asia/Beirut');

            $event->update($eventData);

            if ($request->hasFile('image')) {
                if ($event->image) {
                    Storage::delete('public/images/' . $event->image);
                }

                $imagePath = $request->file('image')->store('public/images');
                $imageName = basename($imagePath);
                $event->image = $imageName;
            }

            $event->save();

            return response()->json(['message' => 'Event updated successfully', 'event' => $event], 200);
        } catch (Exception $e) {
            Log::error('Error updating event: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while updating the event'], 500);
        }
    }

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
