<?php

namespace App\Http\Controllers;

use App\Models\Pledge;
use App\Services\AlertService;
use Illuminate\Http\Request;

class PledgeController extends Controller
{
    private function pledgeQuery(?string $search = null, ?string $dateFrom = null, ?string $dateTo = null)
    {
        $query = \App\Models\Pledge::with('campaign', 'payments');

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('pledger_name', 'like', "%{$search}%")
                  ->orWhere('pledger_phone', 'like', "%{$search}%")
                  ->orWhereHas('campaign', fn($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        if (! empty($dateFrom)) {
            $query->whereDate('pledge_date', '>=', $dateFrom);
        }

        if (! empty($dateTo)) {
            $query->whereDate('pledge_date', '<=', $dateTo);
        }

        return $query;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = $this->pledgeQuery($search, $dateFrom, $dateTo);

        $perPage = in_array((int)$request->input('per_page'), [10,25,50,100]) ? (int)$request->input('per_page') : 20;
        $pledges = $query->orderByDesc('pledge_date')->paginate($perPage)->withQueryString();
        return view('pledges.index', compact('pledges', 'search', 'perPage', 'dateFrom', 'dateTo'));
    }

    public function export(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $records = $this->pledgeQuery($search, $dateFrom, $dateTo)
            ->orderByDesc('pledge_date')
            ->get();

        $filename = 'pledges-report-' . now()->format('YmdHis') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->streamDownload(function () use ($records) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Pledger Name', 'Phone', 'Campaign', 'Amount', 'Paid Amount', 'Due Amount', 'Pledge Date', 'Due Date']);

            foreach ($records as $pledge) {
                $paid = $pledge->payments->sum('amount');
                fputcsv($file, [
                    $pledge->id,
                    $pledge->pledger_name ?? '',
                    $pledge->pledger_phone ?? '',
                    $pledge->campaign?->name ?? '',
                    $pledge->amount,
                    $paid,
                    max(0, $pledge->amount - $paid),
                    $pledge->pledge_date ? \Carbon\Carbon::parse($pledge->pledge_date)->format('Y-m-d') : '',
                    $pledge->due_date ? \Carbon\Carbon::parse($pledge->due_date)->format('Y-m-d') : '',
                ]);
            }

            fclose($file);
        }, $filename, $headers);
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
            'pledger_name'  => 'nullable|string',
            'pledger_phone' => 'nullable|string|max:30',
            'pledger_email' => 'nullable|email',
            'amount'        => 'required|numeric',
            'pledge_date'   => 'required|date',
            'due_date'      => 'nullable|date|after_or_equal:pledge_date',
            'campaign_id'   => 'nullable|exists:campaigns,id',
            'notes'         => 'nullable|string',
        ]);
        \App\Models\Pledge::create($data);
        app(AlertService::class)->generateAlerts();
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
            'pledger_phone' => 'nullable|string|max:30',
            'pledger_email' => 'nullable|email',
            'amount'        => 'required|numeric',
            'pledge_date'   => 'required|date',
            'due_date'      => 'nullable|date|after_or_equal:pledge_date',
            'campaign_id'   => 'nullable|exists:campaigns,id',
            'notes'         => 'nullable|string',
        ]);
        $pledge->update($data);
        app(AlertService::class)->generateAlerts();
        return redirect()->route('pledges.index')->with('success', 'Pledge updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pledge $pledge)
    {
        $pledge->delete();
        app(AlertService::class)->generateAlerts();
        return redirect()->route('pledges.index')->with('success', 'Pledge deleted.');
    }
}
