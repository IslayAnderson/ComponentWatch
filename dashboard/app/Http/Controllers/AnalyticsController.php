<?php

namespace App\Http\Controllers;

use App\Models\Component;
use App\Models\Screenshot;
use App\Models\Site;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class AnalyticsController extends Controller
{
    public function show(Site $site, Component $component): Response
    {
        Gate::authorize('view', $site);

        $discoveries = $component->discoveries()->latest()->get();
        $discoveryIds = $discoveries->pluck('id');

        $totalDiscoveries = $discoveries->count();
        $uniquePages = $discoveries->pluck('page_url')->unique()->values();
        $uniqueSessions = $discoveries->pluck('session_id')->unique()->count();

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

        $htmlHashes = $discoveries
            ->whereNotNull('html_hash')
            ->groupBy('html_hash')
            ->map(fn ($group) => [
                'hash' => $group->first()->html_hash,
                'count' => $group->count(),
                'pages' => $group->pluck('page_url')->unique()->values(),
            ])
            ->values();

        $pageBreakdown = $discoveries
            ->groupBy('page_url')
            ->map(fn ($group, $url) => [
                'url' => $url,
                'discoveries' => $group->count(),
                'clicks' => DB::table('events')
                    ->whereIn('discovery_id', $group->pluck('id'))
                    ->where('type', 'click')
                    ->count(),
            ])
            ->values();

        $watcherApiUrl = config('services.watcher_api.url', 'http://localhost:8001');

        $screenshots = Screenshot::where('component_id', $component->id)
            ->latest()
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'page_url' => $s->page_url,
                'created_at' => $s->created_at->toDateTimeString(),
                'image_url' => $watcherApiUrl . '/api/screenshot/image/' . $component->id . '/' . basename($s->path),
            ]);

        return Inertia::render('Analytics/Show', [
            'site' => $site,
            'component' => $component->load('macros'),
            'stats' => [
                'total_discoveries' => $totalDiscoveries,
                'unique_pages' => $uniquePages->count(),
                'unique_sessions' => $uniqueSessions,
                'clicks' => $eventCounts->get('click', 0),
                'mouseovers' => $eventCounts->get('mouseover', 0),
                'hover_time_events' => $eventCounts->get('hover_time', 0),
                'avg_hover_ms' => $avgHoverMs ? round($avgHoverMs) : null,
                'screenshot_detections' => $eventCounts->get('screenshot', 0),
            ],
            'page_breakdown' => $pageBreakdown,
            'html_hashes' => $htmlHashes,
            'screenshots' => $screenshots,
            'watcherApiUrl' => $watcherApiUrl,
        ]);
    }
}
