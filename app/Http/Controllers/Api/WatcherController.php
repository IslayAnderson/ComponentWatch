<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Watcher;
use Illuminate\Http\Request;

class WatcherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $watchers = Watcher::all();
        return response()->json([
            'status' => true,
            'watchers' => $watchers
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWatcherRequest $request)
    {
        $watcher = Watcher::create($request->all());

        return response()->json([
            'status' => true,
            'message' => "Watcher Created successfully!",
            'watcher' => $watcher
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Watcher $watcher)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Watcher $watcher)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Watcher $watcher)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Watcher $watcher)
    {
        //
    }
}
