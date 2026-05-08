<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Group donations by member (using donor_name, phone, tithe_code if available)
        $summaries = \App\Models\Donation::selectRaw('
                COALESCE(members.id, 0) as member_id,
                COALESCE(members.full_name, MIN(donations.donor_name)) as full_name,
                COALESCE(members.phone, "") as phone,
                COALESCE(members.tithe_code, "") as tithe_code,
                SUM(donations.amount) as total_donated,
                COUNT(donations.id) as donation_count
            ')
            ->leftJoin('members', function($join) {
                $join->on('donations.donor_email', '=', 'members.email');
            })
            ->groupBy('members.id', 'members.full_name', 'members.phone', 'members.tithe_code')
            ->orderByDesc('total_donated')
            ->get()
            ->toArray();
        return view('donations.index', ['donationSummaries' => $summaries]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $campaigns = \App\Models\Campaign::all();
        return view('donations.create', compact('campaigns'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'donor_name' => 'nullable|string',
            'donor_email' => 'nullable|email',
            'amount' => 'required|numeric',
            'method' => 'nullable|string',
            'donation_date' => 'required|date',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'notes' => 'nullable|string',
        ]);
        \App\Models\Donation::create($data);
        return redirect()->route('donations.index')->with('success', 'Donation recorded.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Donation $donation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Donation $donation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Donation $donation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Donation $donation)
    {
        //
    }
}
