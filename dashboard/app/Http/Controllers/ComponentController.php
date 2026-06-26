<?php

namespace App\Http\Controllers;

use App\Models\Component;
use App\Models\Site;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ComponentController extends Controller
{
    public function index(Site $site): Response
    {
        Gate::authorize('view', $site);

        return Inertia::render('Components/Index', [
            'site' => $site,
            'components' => $site->components()->withCount('macros')->latest()->get(),
            'watcherApiUrl' => config('services.watcher_api.url', 'https://your-watcher-api.com'),
        ]);
    }

    public function create(Site $site): Response
    {
        Gate::authorize('view', $site);

        return Inertia::render('Components/Create', ['site' => $site]);
    }

    public function store(Request $request, Site $site): RedirectResponse
    {
        Gate::authorize('view', $site);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'screen_blank' => 'boolean',
            'macros' => 'array',
            'macros.*.type' => 'required|in:id,css,js',
            'macros.*.value' => 'required|string',
            'macros.*.priority' => 'integer',
        ]);

        $component = $site->components()->create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'screen_blank' => $validated['screen_blank'] ?? false,
        ]);

        foreach ($validated['macros'] ?? [] as $i => $macro) {
            $component->macros()->create([
                'type' => $macro['type'],
                'value' => $macro['value'],
                'priority' => $macro['priority'] ?? $i,
            ]);
        }

        return redirect()->route('sites.components.index', $site)->with('success', 'Component created.');
    }

    public function edit(Site $site, Component $component): Response
    {
        Gate::authorize('view', $site);

        return Inertia::render('Components/Edit', [
            'site' => $site,
            'component' => $component->load('macros'),
        ]);
    }

    public function update(Request $request, Site $site, Component $component): RedirectResponse
    {
        Gate::authorize('view', $site);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'screen_blank' => 'boolean',
            'macros' => 'array',
            'macros.*.id' => 'nullable|integer|exists:macros,id',
            'macros.*.type' => 'required|in:id,css,js',
            'macros.*.value' => 'required|string',
            'macros.*.priority' => 'integer',
        ]);

        $component->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'screen_blank' => $validated['screen_blank'] ?? false,
        ]);

        $incomingIds = collect($validated['macros'] ?? [])->pluck('id')->filter()->values();
        $component->macros()->whereNotIn('id', $incomingIds)->delete();

        foreach ($validated['macros'] ?? [] as $i => $macro) {
            $component->macros()->updateOrCreate(
                ['id' => $macro['id'] ?? null],
                ['type' => $macro['type'], 'value' => $macro['value'], 'priority' => $macro['priority'] ?? $i],
            );
        }

        return redirect()->route('sites.components.index', $site)->with('success', 'Component updated.');
    }

    public function destroy(Site $site, Component $component): RedirectResponse
    {
        Gate::authorize('view', $site);
        $component->delete();

        return redirect()->route('sites.components.index', $site)->with('success', 'Component deleted.');
    }
}
