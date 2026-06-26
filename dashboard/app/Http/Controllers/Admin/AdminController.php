<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    public function index(): Response
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $users = User::withCount('sites')
            ->with(['sites' => fn ($q) => $q->withCount('components')])
            ->orderBy('created_at')
            ->get()
            ->map(fn ($user) => [
                'id'              => $user->id,
                'name'            => $user->name,
                'email'           => $user->email,
                'is_admin'        => $user->is_admin,
                'sites_count'     => $user->sites_count,
                'components_count' => $user->sites->sum('components_count'),
                'created_at'      => $user->created_at->toDateString(),
            ]);

        return Inertia::render('Admin/Index', [
            'users' => $users,
        ]);
    }
}
