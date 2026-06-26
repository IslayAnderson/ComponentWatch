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

        // Fire-and-forget batched new-hash notifications to the dashboard (one per component)
        $dashboardUrl = config('services.dashboard.url');
        $secret = config('services.dashboard.secret');
        $newHashesByComponent = [];
        foreach ($created as $i => $discovery) {
            if (!$needsScreenshot[$i] || !$discovery->html_hash) continue;
            $newHashesByComponent[$discovery->component_id]['hashes'][] = $discovery->html_hash;
            $newHashesByComponent[$discovery->component_id]['page_url'] = $discovery->page_url;
        }
        foreach ($newHashesByComponent as $componentId => $data) {
            $payload = json_encode([
                'component_id' => $componentId,
                'hashes'       => array_unique($data['hashes']),
                'page_url'     => $data['page_url'],
                'secret'       => $secret,
            ]);
            @file_get_contents($dashboardUrl . '/api/internal/new-hash', false, stream_context_create([
                'http' => [
                    'method'  => 'POST',
                    'header'  => "Content-Type: application/json\r\nContent-Length: " . strlen($payload),
                    'content' => $payload,
                    'timeout' => 3,
                    'ignore_errors' => true,
                ],
            ]));
        }

        return response()->json([
            'discovery_ids' => $created->pluck('id'),
            'needs_screenshot' => $needsScreenshot,
        ], 201);
    }
}
