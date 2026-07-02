<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    private function campaignQuery(?string $search = null, ?string $dateFrom = null, ?string $dateTo = null)
    {
        $query = \App\Models\Campaign::query();

        if (! empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (! empty($dateFrom)) {
            $query->whereDate('start_date', '>=', $dateFrom);
        }

        if (! empty($dateTo)) {
            $query->whereDate('start_date', '<=', $dateTo);
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

        $query = $this->campaignQuery($search, $dateFrom, $dateTo);

        $perPage = in_array((int)$request->input('per_page'), [10,25,50,100]) ? (int)$request->input('per_page') : 20;
        $campaigns = $query->orderByDesc('start_date')->paginate($perPage)->withQueryString();
        return view('campaigns.index', compact('campaigns', 'search', 'perPage', 'dateFrom', 'dateTo'));
    }

    public function export(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $records = $this->campaignQuery($search, $dateFrom, $dateTo)
            ->orderByDesc('start_date')
            ->get();

        $filename = 'campaigns-report-' . now()->format('YmdHis') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->streamDownload(function () use ($records) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Name', 'Description', 'Target Amount', 'Start Date', 'End Date']);

            foreach ($records as $campaign) {
                fputcsv($file, [
                    $campaign->id,
                    $campaign->name,
                    $campaign->description ?? '',
                    $campaign->target_amount ?? 0,
                    $campaign->start_date ? \Carbon\Carbon::parse($campaign->start_date)->format('Y-m-d') : '',
                    $campaign->end_date ? \Carbon\Carbon::parse($campaign->end_date)->format('Y-m-d') : '',
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
