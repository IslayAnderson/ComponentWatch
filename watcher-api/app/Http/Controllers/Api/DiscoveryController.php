<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Discovery;
use App\Models\Screenshot;
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

        $needsScreenshot = [];
        $created = collect($validated['discoveries'])->map(function ($d) use ($validated, &$needsScreenshot) {
            $hash = $d['html_hash'] ?? null;
            $isNew = $hash && !Screenshot::whereHas('discovery', fn ($q) =>
                $q->where('component_id', $d['component_id'])
                  ->where('html_hash', $hash)
            )->exists();
            $needsScreenshot[] = $isNew;

            return Discovery::create([
                'component_id' => $d['component_id'],
                'page_url' => $d['page_url'],
                'html_hash' => $hash,
                'stack_position' => $d['stack_position'] ?? null,
                'session_id' => $validated['session_id'],
            ]);
        });

        return response()->json([
            'discovery_ids' => $created->pluck('id'),
            'needs_screenshot' => $needsScreenshot,
        ], 201);
    }
}
