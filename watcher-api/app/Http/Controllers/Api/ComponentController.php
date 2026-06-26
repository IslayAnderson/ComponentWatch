<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ComponentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $site = $request->attributes->get('site');

        $components = $site->components()->with('macros')->get()->map(fn ($c) => [
            'id' => $c->id,
            'name' => $c->name,
            'screen_blank' => (bool) $c->screen_blank,
            'macros' => $c->macros->map(fn ($m) => [
                'type' => $m->type,
                'value' => $m->value,
                'priority' => $m->priority,
            ]),
        ]);

        return response()->json(['components' => $components]);
    }
}
