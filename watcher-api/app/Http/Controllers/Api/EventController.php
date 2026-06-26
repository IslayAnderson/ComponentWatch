<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'events' => 'required|array',
            'events.*.discovery_id' => 'required|integer|exists:discoveries,id',
            'events.*.type' => 'required|in:click,mouseover,mouseout,hover_time,screenshot',
            'events.*.payload' => 'nullable|array',
            'events.*.occurred_at' => 'required|date',
        ]);

        $events = collect($validated['events'])->map(fn ($e) => Event::create([
            'discovery_id' => $e['discovery_id'],
            'type' => $e['type'],
            'payload' => $e['payload'] ?? null,
            'occurred_at' => $e['occurred_at'],
        ]));

        return response()->json(['count' => $events->count()], 201);
    }
}
