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
            'hash'         => 'required|string',
            'page_url'     => 'required|string',
            'secret'       => 'required|string',
        ]);

        if ($request->secret !== config('services.internal.secret')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $component = Component::with('site.user')->findOrFail($request->component_id);

        file_put_contents(storage_path('logs/mail-debug.log'),
            date('Y-m-d H:i:s') . " Sending to: " . $component->site->user->email . " hash: " . $request->hash . "\n",
            FILE_APPEND
        );

        Mail::to($component->site->user->email)
            ->send(new NewHashDetected($component, $request->hash, $request->page_url));

        file_put_contents(storage_path('logs/mail-debug.log'),
            date('Y-m-d H:i:s') . " Mail sent.\n",
            FILE_APPEND
        );

        return response()->json(['status' => 'sent']);
    }
}
