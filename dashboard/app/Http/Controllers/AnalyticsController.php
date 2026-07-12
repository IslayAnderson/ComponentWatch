<?php

namespace App\Http\Controllers;

use App\Models\Component;
use App\Models\Screenshot;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class AnalyticsController extends Controller
{
    public function show(Request $request, Site $site, Component $component): Response
    {
        Gate::authorize('view', $site);

        $watcherApiUrl = config('services.watcher_api.url', 'http://localhost:8001');

        $dateFrom = $request->query('date_from');
        $dateTo   = $request->query('date_to');

        $query = $component->discoveries()->latest();
        if ($dateFrom) $query->whereDate('created_at', '>=', $dateFrom);
        if ($dateTo)   $query->whereDate('created_at', '<=', $dateTo);

        $discoveries  = $query->get();
        $discoveryIds = $discoveries->pluck('id');

        $totalDiscoveries = $discoveries->count();
        $uniquePages      = $discoveries->pluck('page_url')->unique()->values();
        $uniqueSessions   = $discoveries->pluck('session_id')->unique()->count();

        $eventCounts = DB::table('events')
            ->whereIn('discovery_id', $discoveryIds)
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type');

        $avgHoverMs = DB::table('events')
            ->whereIn('discovery_id', $discoveryIds)
            ->where('type', 'hover_time')
            ->get()
            ->avg(fn ($e) => json_decode($e->payload, true)['ms'] ?? 0);

        $screenshotsByHash = Screenshot::whereIn('discovery_id', $discoveryIds)
            ->with('discovery')
            ->get()
            ->keyBy(fn ($s) => $s->discovery?->html_hash);

        $htmlHashes = $discoveries
            ->whereNotNull('html_hash')
            ->groupBy('html_hash')
            ->map(function ($group) use ($screenshotsByHash, $watcherApiUrl, $component) {
                $hash       = $group->first()->html_hash;
                $screenshot = $screenshotsByHash->get($hash);
                return [
                    'hash'           => $hash,
                    'count'          => $group->count(),
                    'pages'          => $group->pluck('page_url')->unique()->values(),
                    'screenshot_url' => $screenshot
                        ? $watcherApiUrl . '/api/screenshot/image/' . $component->id . '/' . basename($screenshot->path)
                        : null,
                ];
            })
            ->values();

        $pageBreakdown = $discoveries
            ->groupBy('page_url')
            ->map(fn ($group, $url) => [
                'url'         => $url,
                'discoveries' => $group->count(),
                'clicks'      => DB::table('events')
                    ->whereIn('discovery_id', $group->pluck('id'))
                    ->where('type', 'click')
                    ->count(),
            ])
            ->values();

        // UTM breakdown — parse utm_* params from all page URLs
        $utmKeys = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'];
        $utms = [];
        foreach ($utmKeys as $key) {
            $values = [];
            foreach ($discoveries as $d) {
                $parsed = parse_url($d->page_url, PHP_URL_QUERY);
                if (!$parsed) continue;
                parse_str($parsed, $params);
                if (!empty($params[$key])) {
                    $values[] = $params[$key];
                }
            }
            if (empty($values)) continue;
            $counts = array_count_values($values);
            arsort($counts);
            $utms[$key] = array_map(
                fn ($v, $c) => ['value' => $v, 'count' => $c],
                array_keys($counts),
                array_values($counts)
            );
        }

        $screenshots = Screenshot::where('component_id', $component->id)
            ->latest()
            ->limit(3)
            ->get()
            ->map(fn ($s) => [
                'id'         => $s->id,
                'page_url'   => $s->page_url,
                'created_at' => $s->created_at->toDateTimeString(),
                'image_url'  => $watcherApiUrl . '/api/screenshot/image/' . $component->id . '/' . basename($s->path),
            ]);

        return Inertia::render('Analytics/Show', [
            'site'          => $site,
            'component'     => $component->load('macros'),
            'filters'       => ['date_from' => $dateFrom, 'date_to' => $dateTo],
            'stats'         => [
                'total_discoveries'    => $totalDiscoveries,
                'unique_pages'         => $uniquePages->count(),
                'unique_sessions'      => $uniqueSessions,
                'clicks'               => $eventCounts->get('click', 0),
                'mouseovers'           => $eventCounts->get('mouseover', 0),
                'hover_time_events'    => $eventCounts->get('hover_time', 0),
                'avg_hover_ms'         => $avgHoverMs ? round($avgHoverMs) : null,
                'screenshot_detections' => $eventCounts->get('screenshot', 0),
            ],
            'page_breakdown' => $pageBreakdown,
            'html_hashes'    => $htmlHashes,
            'utms'           => $utms,
            'screenshots'    => $screenshots,
            'watcherApiUrl'  => $watcherApiUrl,
        ]);
    }
}
