<?php

namespace App\Http\Controllers;

use App\Models\PledgePayment;
use Illuminate\Http\Request;

class PledgePaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pledgePayments = \App\Models\PledgePayment::with('pledge')->orderByDesc('payment_date')->get();
        return view('pledge-payments.index', compact('pledgePayments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pledges = \App\Models\Pledge::all();
        $campaigns = \App\Models\Campaign::all();
        return view('pledge-payments.create', compact('pledges', 'campaigns'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'pledge_id' => 'required|exists:pledges,id',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'phone' => 'nullable|string',
            'invoice_number' => 'nullable|string',
            'amount' => 'required|numeric',
            'payment_date' => 'required|date',
            'method' => 'nullable|string',
            'notes' => 'nullable|string',
            'attachment' => 'nullable|file',
        ]);
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = uniqid() . '-' . preg_replace('/[^\w.\-]+/', '_', $file->getClientOriginalName());
            $path = $file->storeAs('uploads', $filename, 'public');
            $data['attachment'] = '/storage/' . $path;
        }
        \App\Models\PledgePayment::create($data);
        return redirect()->route('pledge-payments.index')->with('success', 'Pledge payment recorded.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PledgePayment $pledgePayment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PledgePayment $pledgePayment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PledgePayment $pledgePayment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PledgePayment $pledgePayment)
    {
        //
    }
}
