<?php

namespace App\Http\Middleware;

use App\Models\Site;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DynamicCors
{
    public function handle(Request $request, Closure $next): Response
    {
        $origin = $request->headers->get('Origin');

        if ($origin && $this->isAllowed($origin)) {
            if ($request->isMethod('OPTIONS')) {
                return response('', 204)
                    ->withHeaders($this->headers($origin));
            }

            $response = $next($request);
            foreach ($this->headers($origin) as $key => $value) {
                $response->headers->set($key, $value);
            }
            return $response;
        }

        if ($request->isMethod('OPTIONS')) {
            return response('', 403);
        }

        return $next($request);
    }

    private function isAllowed(string $origin): bool
    {
        $host = parse_url($origin, PHP_URL_HOST);
        return Site::all()->contains(function ($site) use ($host) {
            return parse_url($site->url, PHP_URL_HOST) === $host;
        });
    }

    private function headers(string $origin): array
    {
        return [
            'Access-Control-Allow-Origin'  => $origin,
            'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Site-Key',
            'Access-Control-Max-Age'       => '86400',
            'Vary'                         => 'Origin',
        ];
    }
}
