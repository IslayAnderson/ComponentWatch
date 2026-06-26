<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\NewHashDetected;
use App\Models\Component;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NewHashNotificationController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'component_id' => 'required|integer|exists:components,id',
            'hashes'       => 'required|array|min:1',
            'hashes.*'     => 'string',
            'page_url'     => 'required|string',
            'secret'       => 'required|string',
        ]);

        if ($request->secret !== config('services.internal.secret')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $component = Component::with('site.user')->findOrFail($request->component_id);

        Mail::to($component->site->user->email)
            ->send(new NewHashDetected($component, $request->hashes, $request->page_url));

        return response()->json(['status' => 'sent']);
    }
}
