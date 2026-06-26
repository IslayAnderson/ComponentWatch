<?php

namespace App\Http\Controllers;

use App\Models\Component;
use App\Models\Site;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class ScreenshotTokenController extends Controller
{
    public function store(Site $site, Component $component): JsonResponse
    {
        Gate::authorize('view', $site);

        $token = Str::random(48);

        DB::table('screenshot_tokens')->insert([
            'component_id' => $component->id,
            'token' => $token,
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'url' => $site->url . '?cw_screenshot=' . $token,
        ]);
    }
}
