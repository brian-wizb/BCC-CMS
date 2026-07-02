<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Member;
use App\Services\SmsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DonationController extends Controller
{
    private function donationQuery(?string $search = null, ?string $dateFrom = null, ?string $dateTo = null)
    {
        $query = Donation::with('member');

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('donor_name', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhere('tithe_code', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%");
            });
        }

        if (! empty($dateFrom)) {
            $query->whereDate('donation_date', '>=', $dateFrom);
        }

        if (! empty($dateTo)) {
            $query->whereDate('donation_date', '<=', $dateTo);
        }

        return $query;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = $this->donationQuery($search, $dateFrom, $dateTo)->latest('donation_date');

        $perPage = in_array((int)$request->input('per_page'), [10,25,50,100]) ? (int)$request->input('per_page') : 25;
        $donations = $query->paginate($perPage)->withQueryString();

        return view('donations.index', compact('donations', 'search', 'perPage', 'dateFrom', 'dateTo'));
    }

    public function export(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $records = $this->donationQuery($search, $dateFrom, $dateTo)
            ->latest('donation_date')
            ->get();

        $filename = 'donations-report-' . now()->format('YmdHis') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->streamDownload(function () use ($records) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID',
                'Member',
                'Tithe Code',
                'Donation Type',
                'Amount',
                'Method',
                'Reference',
                'Date',
                'Donor Email',
                'Notes',
            ]);

            foreach ($records as $donation) {
                fputcsv($file, [
                    $donation->id,
                    $donation->member?->full_name ?? $donation->donor_name ?? '—',
                    $donation->tithe_code ?? '',
                    $donation->type ?? '',
                    $donation->amount,
                    $donation->method ?? '',
                    $donation->reference ?? '',
                    $donation->donation_date ? \Carbon\Carbon::parse($donation->donation_date)->format('Y-m-d') : '',
                    $donation->donor_email ?? '',
                    $donation->notes ?? '',
                ]);
            }

            fclose($file);
        }, $filename, $headers);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $members = Member::orderBy('full_name')->get(['id', 'full_name', 'tithe_code']);
        return view('donations.create', compact('members'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'member_id'     => ['nullable', 'exists:members,id'],
            'type'          => ['required', 'string'],
            'tithe_code'    => ['nullable', 'string', 'max:50'],
            'amount'        => ['required', 'numeric', 'min:0'],
            'reference'     => ['nullable', 'string', 'max:100'],
            'method'        => ['required', 'string'],
            'donation_date' => ['required', 'date'],
            'notes'         => ['nullable', 'string'],
            'attachment'    => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('donations', 'public');
        }

        // Auto-fill donor info from member if selected
        if (!empty($data['member_id'])) {
            $member = Member::find($data['member_id']);
            $data['donor_name']  = $member->full_name;
            $data['donor_email'] = $member->email;
            if (empty($data['tithe_code'])) {
                $data['tithe_code'] = $member->tithe_code;
            }
        }

        $donation = Donation::create($data);

        // Send SMS confirmation to member when a Tithe is recorded
        if ($data['type'] === 'Tithe [Zaka]' && !empty($data['member_id'])) {
            $member = $member ?? Member::find($data['member_id']);
            if ($member && filled($member->phone)) {
                try {
                    $amount = number_format((float) $data['amount'], 2);
                    $date   = \Carbon\Carbon::parse($data['donation_date'])->format('d M Y');
                    $code   = $data['tithe_code'] ?? $member->tithe_code ?? '';
                    $msg    = "Ndugu {$member->full_name}, Zaka yako ya TZS {$amount} imepokelewa tarehe {$date}."
                            . ($code ? " Nambari ya Zaka: {$code}." : '')
                            . " Mungu akubariki. - BCC";
                    app(SmsService::class)->send($member->phone, $msg);
                } catch (\Throwable $e) {
                    Log::warning("Tithe SMS failed for member {$data['member_id']}: {$e->getMessage()}");
                }
            }
        }

        return redirect()->route('donations.index')->with('status', 'Donation recorded successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Donation $donation): View
    {
        $members = Member::orderBy('full_name')->get(['id', 'full_name', 'tithe_code']);
        return view('donations.edit', compact('donation', 'members'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Donation $donation): RedirectResponse
    {
        $data = $request->validate([
            'member_id'     => ['nullable', 'exists:members,id'],
            'type'          => ['required', 'string'],
            'tithe_code'    => ['nullable', 'string', 'max:50'],
            'amount'        => ['required', 'numeric', 'min:0'],
            'reference'     => ['nullable', 'string', 'max:100'],
            'method'        => ['required', 'string'],
            'donation_date' => ['required', 'date'],
            'notes'         => ['nullable', 'string'],
            'attachment'    => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        if ($request->hasFile('attachment')) {
            if ($donation->attachment) {
                Storage::disk('public')->delete($donation->attachment);
            }
            $data['attachment'] = $request->file('attachment')->store('donations', 'public');
        }

        if (!empty($data['member_id'])) {
            $member = Member::find($data['member_id']);
            $data['donor_name']  = $member->full_name;
            $data['donor_email'] = $member->email;
            if (empty($data['tithe_code'])) {
                $data['tithe_code'] = $member->tithe_code;
            }
        }

        $donation->update($data);

        return redirect()->route('donations.index')->with('status', 'Donation updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Donation $donation): RedirectResponse
    {
        if ($donation->attachment) {
            Storage::disk('public')->delete($donation->attachment);
        }
        $donation->delete();

        return redirect()->route('donations.index')->with('status', 'Donation deleted.');
    }
}
