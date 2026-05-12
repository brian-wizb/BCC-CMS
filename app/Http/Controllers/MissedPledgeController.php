<?php

namespace App\Http\Controllers;

use App\Models\MissedPledge;
use Illuminate\Http\Request;

class MissedPledgeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Auto-compute: pledges whose due_date has passed and are not fully paid
        $missedPledges = \App\Models\Pledge::with('campaign', 'payments')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', now())
            ->orderBy('due_date')
            ->get()
            ->filter(function ($pledge) {
                $paid = $pledge->payments->sum('amount');
                return $paid < $pledge->amount;
            })
            ->values();
        return view('missed-pledges.index', compact('missedPledges'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pledges = \App\Models\Pledge::all();
        return view('missed-pledges.create', compact('pledges'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'pledge_id' => 'required|exists:pledges,id',
            'missed_date' => 'required|date',
            'reason' => 'nullable|string',
        ]);
        \App\Models\MissedPledge::create($data);
        return redirect()->route('missed-pledges.index')->with('success', 'Missed pledge recorded.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MissedPledge $missedPledge)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MissedPledge $missedPledge)
    {
        $pledges = \App\Models\Pledge::orderBy('pledge_date', 'desc')->get();
        return view('missed-pledges.edit', compact('missedPledge', 'pledges'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MissedPledge $missedPledge)
    {
        $data = $request->validate([
            'pledge_id'   => 'required|exists:pledges,id',
            'missed_date' => 'required|date',
            'reason'      => 'nullable|string',
        ]);
        $missedPledge->update($data);
        return redirect()->route('missed-pledges.index')->with('success', 'Missed pledge updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MissedPledge $missedPledge)
    {
        $missedPledge->delete();
        return redirect()->route('missed-pledges.index')->with('success', 'Missed pledge deleted.');
    }
}
