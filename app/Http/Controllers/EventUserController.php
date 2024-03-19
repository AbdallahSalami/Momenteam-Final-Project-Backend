<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;

class EventUserController extends Controller
{
    // Add a user to an event
    public function addUserToEvent(Request $request)
    {
        try {
            $user = User::findOrFail($request->user_id);
            $event = Event::findOrFail($request->event_id);

            $user->events()->attach($event->id);

            return response()->json(['message' => 'User added to event successfully.'],  200);
        } catch (Exception $e) {
            Log::error('Error adding user to event: ' . $e->getMessage());
            return response()->json(['error' => 'Error adding user to event.'],  500);
        }
    }

    // Remove a user from an event
    public function removeUserFromEvent(Request $request)
    {
        try {
            $user = User::findOrFail($request->user_id);
            $event = Event::findOrFail($request->event_id);

            $user->events()->detach($event->id);

            return response()->json(['message' => 'User removed from event successfully.'],  200);
        } catch (Exception $e) {
            Log::error('Error removing user from event: ' . $e->getMessage());
            return response()->json(['error' => 'Error removing user from event.'],  500);
        }
    }







    // Get a specific event with its registered users
    public function getEventWithUsers($eventId)
    {
        try {
            $event = Event::with('users')->findOrFail($eventId);
            return response()->json($event);
        } catch (Exception $e) {
            Log::error('Error retrieving event with users: ' . $e->getMessage());
            return response()->json(['error' => 'Error retrieving event with users.'],  500);
        }
    }

    // Get a user with all events they are registered for
    public function getUserWithEvents($userId)
    {
        try {
            $user = User::with('events')->findOrFail($userId);
            return response()->json($user);
        } catch (Exception $e) {
            Log::error('Error retrieving user with events: ' . $e->getMessage());
            return response()->json(['error' => 'Error retrieving user with events.'],  500);
        }
    }
}
