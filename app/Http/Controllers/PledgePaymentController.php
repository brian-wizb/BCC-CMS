<?php

namespace App\Http\Controllers;

use App\Models\PledgePayment;
use App\Services\AlertService;
use Illuminate\Http\Request;

class PledgePaymentController extends Controller
{
    private function pledgePaymentQuery(?string $search = null, ?string $dateFrom = null, ?string $dateTo = null)
    {
        $query = \App\Models\PledgePayment::with('pledge.campaign');

        if (! empty($search)) {
            $query->whereHas('pledge', fn($q) => $q
                ->where('pledger_name', 'like', "%{$search}%")
                ->orWhere('pledger_phone', 'like', "%{$search}%")
            );
        }

        if (! empty($dateFrom)) {
            $query->whereDate('payment_date', '>=', $dateFrom);
        }

        if (! empty($dateTo)) {
            $query->whereDate('payment_date', '<=', $dateTo);
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

        $query = $this->pledgePaymentQuery($search, $dateFrom, $dateTo);

        $perPage = in_array((int)$request->input('per_page'), [10,25,50,100]) ? (int)$request->input('per_page') : 20;
        $pledgePayments = $query->orderByDesc('payment_date')->paginate($perPage)->withQueryString();
        return view('pledge-payments.index', compact('pledgePayments', 'search', 'perPage', 'dateFrom', 'dateTo'));
    }

    public function export(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $records = $this->pledgePaymentQuery($search, $dateFrom, $dateTo)
            ->orderByDesc('payment_date')
            ->get();

        $filename = 'pledge-payments-report-' . now()->format('YmdHis') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->streamDownload(function () use ($records) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Pledger', 'Campaign', 'Phone', 'Invoice', 'Amount', 'Method', 'Payment Date', 'Notes']);

            foreach ($records as $payment) {
                fputcsv($file, [
                    $payment->id,
                    $payment->pledge?->pledger_name ?? '',
                    $payment->pledge?->campaign?->name ?? '',
                    $payment->phone ?? '',
                    $payment->invoice_number ?? '',
                    $payment->amount,
                    $payment->method ?? '',
                    $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') : '',
                    $payment->notes ?? '',
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
        app(AlertService::class)->generateAlerts();
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
        $pledges   = \App\Models\Pledge::all();
        $campaigns = \App\Models\Campaign::all();
        return view('pledge-payments.edit', compact('pledgePayment', 'pledges', 'campaigns'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PledgePayment $pledgePayment)
    {
        $data = $request->validate([
            'pledge_id'      => 'required|exists:pledges,id',
            'campaign_id'    => 'nullable|exists:campaigns,id',
            'phone'          => 'nullable|string',
            'invoice_number' => 'nullable|string',
            'amount'         => 'required|numeric',
            'payment_date'   => 'required|date',
            'method'         => 'nullable|string',
            'notes'          => 'nullable|string',
            'attachment'     => 'nullable|file',
        ]);
        if ($request->hasFile('attachment')) {
            $file     = $request->file('attachment');
            $filename = uniqid() . '-' . preg_replace('/[^\w.\-]+/', '_', $file->getClientOriginalName());
            $path     = $file->storeAs('uploads', $filename, 'public');
            $data['attachment'] = '/storage/' . $path;
        } else {
            unset($data['attachment']);
        }
        $pledgePayment->update($data);
        app(AlertService::class)->generateAlerts();
        return redirect()->route('pledge-payments.index')->with('success', 'Pledge payment updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PledgePayment $pledgePayment)
    {
        $pledgePayment->delete();
        app(AlertService::class)->generateAlerts();
        return redirect()->route('pledge-payments.index')->with('success', 'Pledge payment deleted.');
    }
}
