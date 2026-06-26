<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Passport\ClientRepository;

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

    public function store(Request $request, ClientRepository $clients): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:255',
        ]);

        $site = auth()->user()->sites()->create($validated);

        $client = $clients->createClientCredentialsGrantClient(
            $site->name,
            $validated['url'],
        );

        $site->update([
            'oauth_client_id' => $client->id,
            'oauth_client_secret' => $client->plainSecret,
        ]);

        return redirect()->route('sites.components.index', $site)->with('success', 'Site created.');
    }

    public function destroy(Site $site): RedirectResponse
    {
        Gate::authorize('view', $site);
        $site->delete();

        return redirect()->route('sites.index')->with('success', 'Site deleted.');
    }
}
