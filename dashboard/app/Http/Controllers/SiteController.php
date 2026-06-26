<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class SiteController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Sites/Index', [
            'sites' => auth()->user()->sites()->withCount('components')->latest()->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Sites/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:255',
        ]);

        auth()->user()->sites()->create($validated);

        return redirect()->route('sites.index')->with('success', 'Site created.');
    }

    public function edit(Site $site): Response
    {
        Gate::authorize('view', $site);
        return Inertia::render('Sites/Edit', ['site' => $site]);
    }

    public function update(Request $request, Site $site): RedirectResponse
    {
        Gate::authorize('view', $site);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url'  => 'required|url|max:255',
        ]);
        $site->update($validated);
        return redirect()->route('sites.index')->with('success', 'Site updated.');
    }

    public function destroy(Site $site): RedirectResponse
    {
        Gate::authorize('view', $site);
        $site->delete();

        return redirect()->route('sites.index')->with('success', 'Site deleted.');
    }
}
