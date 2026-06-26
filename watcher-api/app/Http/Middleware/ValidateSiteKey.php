<?php

namespace App\Http\Middleware;

use App\Models\Site;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateSiteKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = $request->header('X-Site-Key');

        if (!$key) {
            return response()->json(['message' => 'Missing X-Site-Key header.'], 401);
        }

        $site = Site::where('api_key', $key)->first();

        if (!$site) {
            return response()->json(['message' => 'Invalid site key.'], 401);
        }

        $origin = $request->header('Origin');
        if ($origin && parse_url($origin, PHP_URL_HOST) !== parse_url($site->url, PHP_URL_HOST)) {
            return response()->json(['message' => 'Origin not permitted.'], 403);
        }

        $request->attributes->set('site', $site);

        return $next($request);
    }
}
