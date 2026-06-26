<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Screenshot;
use App\Models\ScreenshotToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ScreenshotController extends Controller
{
    public function validate(Request $request): JsonResponse
    {
        $token = ScreenshotToken::where('token', $request->query('token'))->first();

        if (!$token || !$token->isValid()) {
            return response()->json(['message' => 'Invalid or expired token.'], 401);
        }

        return response()->json([
            'component_id' => $token->component_id,
            'component' => [
                'id' => $token->component->id,
                'name' => $token->component->name,
                'macros' => $token->component->macros->map(fn ($m) => [
                    'type' => $m->type,
                    'value' => $m->value,
                    'priority' => $m->priority,
                ]),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'image' => 'required|string', // base64 data URL
            'page_url' => 'required|url',
        ]);

        $token = ScreenshotToken::where('token', $validated['token'])->first();

        if (!$token || !$token->isValid()) {
            return response()->json(['message' => 'Invalid or expired token.'], 401);
        }

        // Decode base64 data URL
        $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $validated['image']);
        $imageData = base64_decode($imageData);

        $filename = 'screenshots/' . $token->component_id . '/' . uniqid() . '.png';
        Storage::disk('local')->put($filename, $imageData);

        Screenshot::create([
            'component_id' => $token->component_id,
            'path' => $filename,
            'page_url' => $validated['page_url'],
        ]);

        $token->update(['used_at' => now()]);

        return response()->json(['message' => 'Screenshot saved.'], 201);
    }
}
