<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query  = \App\Models\Campaign::query();
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        $perPage = in_array((int)$request->input('per_page'), [10,25,50,100]) ? (int)$request->input('per_page') : 20;
        $campaigns = $query->orderByDesc('start_date')->paginate($perPage)->withQueryString();
        return view('campaigns.index', compact('campaigns', 'search', 'perPage'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('campaigns.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'target_amount' => 'nullable|numeric',
        ]);
        \App\Models\Campaign::create($data);
        return redirect()->route('campaigns.index')->with('success', 'Campaign created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Campaign $campaign)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Campaign $campaign)
    {
        return view('campaigns.edit', compact('campaign'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Campaign $campaign)
    {
        $data = $request->validate([
            'name'          => 'required|string',
            'description'   => 'nullable|string',
            'start_date'    => 'nullable|date',
            'end_date'      => 'nullable|date',
            'target_amount' => 'nullable|numeric',
        ]);
        $campaign->update($data);
        return redirect()->route('campaigns.index')->with('success', 'Campaign updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Campaign $campaign)
    {
        $campaign->delete();
        return redirect()->route('campaigns.index')->with('success', 'Campaign deleted.');
    }
}
