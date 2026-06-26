<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonateController extends Controller
{
    public function start(Request $request, User $user): RedirectResponse
    {
        abort_unless(Auth::user()->isAdmin(), 403);
        abort_if($user->isAdmin(), 403);

        $request->session()->put('impersonator_id', Auth::id());
        Auth::login($user);

        return redirect()->route('sites.index');
    }

    public function stop(Request $request): RedirectResponse
    {
        $impersonatorId = $request->session()->pull('impersonator_id');
        abort_unless($impersonatorId, 403);

        Auth::loginUsingId($impersonatorId);

        return redirect()->route('admin.index');
    }
}
