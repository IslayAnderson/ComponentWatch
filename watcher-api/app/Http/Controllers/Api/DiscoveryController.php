<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Discovery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DiscoveryController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_id' => 'required|string',
            'discoveries' => 'required|array',
            'discoveries.*.component_id' => 'required|integer|exists:components,id',
            'discoveries.*.page_url' => 'required|url',
            'discoveries.*.html_hash' => 'nullable|string',
            'discoveries.*.stack_position' => 'nullable|integer',
        ]);

        $created = collect($validated['discoveries'])->map(fn ($d) => Discovery::create([
            'component_id' => $d['component_id'],
            'page_url' => $d['page_url'],
            'html_hash' => $d['html_hash'] ?? null,
            'stack_position' => $d['stack_position'] ?? null,
            'session_id' => $validated['session_id'],
        ]));

        return response()->json(['discovery_ids' => $created->pluck('id')], 201);
    }
}
