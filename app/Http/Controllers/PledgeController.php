<?php

namespace App\Http\Controllers;

use App\Models\Pledge;
use Illuminate\Http\Request;

class PledgeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pledges = \App\Models\Pledge::with('campaign')->orderByDesc('pledge_date')->get();
        return view('pledges.index', compact('pledges'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $campaigns = \App\Models\Campaign::all();
        return view('pledges.create', compact('campaigns'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'pledger_name' => 'nullable|string',
            'pledger_email' => 'nullable|email',
            'amount' => 'required|numeric',
            'pledge_date' => 'required|date',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'notes' => 'nullable|string',
        ]);
        \App\Models\Pledge::create($data);
        return redirect()->route('pledges.index')->with('success', 'Pledge recorded.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pledge $pledge)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pledge $pledge)
    {
        $campaigns = \App\Models\Campaign::all();
        return view('pledges.edit', compact('pledge', 'campaigns'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pledge $pledge)
    {
        $data = $request->validate([
            'pledger_name'  => 'nullable|string',
            'pledger_email' => 'nullable|email',
            'amount'        => 'required|numeric',
            'pledge_date'   => 'required|date',
            'campaign_id'   => 'nullable|exists:campaigns,id',
            'notes'         => 'nullable|string',
        ]);
        $pledge->update($data);
        return redirect()->route('pledges.index')->with('success', 'Pledge updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pledge $pledge)
    {
        $pledge->delete();
        return redirect()->route('pledges.index')->with('success', 'Pledge deleted.');
    }
}
