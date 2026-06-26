<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Discovery;
use App\Models\Screenshot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AutoScreenshotController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'discovery_id' => 'required|integer|exists:discoveries,id',
            'image'        => 'required|string',
            'page_url'     => 'required|url',
        ]);

        $discovery = Discovery::findOrFail($validated['discovery_id']);

        // Only save one auto-screenshot per html_hash per component
        if ($discovery->html_hash) {
            $alreadyExists = Screenshot::whereHas('discovery', fn ($q) =>
                $q->where('component_id', $discovery->component_id)
                  ->where('html_hash', $discovery->html_hash)
            )->exists();

            if ($alreadyExists) {
                return response()->json(['status' => 'skipped'], 200);
            }
        }

        $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $validated['image']);
        $filename = uniqid() . '.png';
        $path = 'screenshots/' . $discovery->component_id . '/' . $filename;
        Storage::put($path, base64_decode($base64));

        Screenshot::create([
            'component_id' => $discovery->component_id,
            'discovery_id' => $discovery->id,
            'page_url'     => $validated['page_url'],
            'path'         => $path,
        ]);

        return response()->json(['status' => 'saved'], 201);
    }
}
