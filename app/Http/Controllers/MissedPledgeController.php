<?php

namespace App\Http\Controllers;

use App\Models\MissedPledge;
use Illuminate\Http\Request;

class MissedPledgeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        // Auto-compute: pledges whose due_date has passed and are not fully paid
        $query = \App\Models\Pledge::with('campaign', 'payments')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', now())
            ->orderBy('due_date');

        if (! empty($dateFrom)) {
            $query->whereDate('due_date', '>=', $dateFrom);
        }

        if (! empty($dateTo)) {
            $query->whereDate('due_date', '<=', $dateTo);
        }

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('pledger_name', 'like', "%{$search}%")
                  ->orWhere('pledger_phone', 'like', "%{$search}%")
                  ->orWhereHas('campaign', fn($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        $missedPledges = $query->get()
            ->filter(function ($pledge) {
                $paid = $pledge->payments->sum('amount');
                return $paid < $pledge->amount;
            })
            ->values();

        return view('missed-pledges.index', compact('missedPledges', 'search', 'dateFrom', 'dateTo'));
    }

    public function export(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = \App\Models\Pledge::with('campaign', 'payments')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', now())
            ->orderBy('due_date');

        if (! empty($dateFrom)) {
            $query->whereDate('due_date', '>=', $dateFrom);
        }

        if (! empty($dateTo)) {
            $query->whereDate('due_date', '<=', $dateTo);
        }

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('pledger_name', 'like', "%{$search}%")
                  ->orWhere('pledger_phone', 'like', "%{$search}%")
                  ->orWhereHas('campaign', fn($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        $records = $query->get()
            ->filter(function ($pledge) {
                $paid = $pledge->payments->sum('amount');
                return $paid < $pledge->amount;
            })
            ->values();

        $filename = 'missed-pledges-report-' . now()->format('YmdHis') . '.csv';
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
