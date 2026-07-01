<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Income;
use App\Models\IncomeType;
use App\Models\Member;

class IncomeController extends Controller
{
    public function index(Request $request)
    {
        $search   = trim((string) $request->input('search'));
        $dateFrom = $request->input('date_from');
        $dateTo   = $request->input('date_to');

        $query = $this->incomeQuery($search, $dateFrom, $dateTo);

        $perPage = in_array((int)$request->input('per_page'), [10,25,50,100]) ? (int)$request->input('per_page') : 20;
        $records = $query->orderByDesc('received_date')->paginate($perPage)->withQueryString();

        return view('income.index', compact('records', 'search', 'perPage', 'dateFrom', 'dateTo'));
    }

    public function export(Request $request)
    {
        $search   = trim((string) $request->input('search'));
        $dateFrom = $request->input('date_from');
        $dateTo   = $request->input('date_to');

        $records = $this->incomeQuery($search, $dateFrom, $dateTo)
            ->orderByDesc('received_date')
            ->get();

        $filename = 'income-report-' . now()->format('YmdHis') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->streamDownload(function () use ($records) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID',
                'Type',
                'Amount',
                'Received Date',
                'Contributor',
                'Member',
                'Status',
                'Comment',
                'Attachment',
            ]);

            foreach ($records as $income) {
                fputcsv($file, [
                    $income->id,
                    $income->incomeType->type ?? '—',
                    $income->amount,
                    $income->received_date ? \Carbon\Carbon::parse($income->received_date)->format('Y-m-d') : '',
                    $income->contributor_name ?: ($income->contributor_contacts ?: '—'),
                    optional($income->member)->full_name ?: optional($income->member)->name ?: '—',
                    $income->status,
                    $income->comment,
                    $income->attachment_url ?: '',
                ]);
            }

            fclose($file);
        }, $filename, $headers);
    }

    private function incomeQuery(string $search = '', ?string $dateFrom = null, ?string $dateTo = null)
    {
        $query = Income::with(['incomeType', 'member']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->whereHas('incomeType', fn($q) => $q->where('type', 'like', "%{$search}%"))
                    ->orWhere('contributor_name', 'like', "%{$search}%")
                    ->orWhere('contributor_contacts', 'like', "%{$search}%")
                    ->orWhere('contributor_address', 'like', "%{$search}%")
                    ->orWhereHas('member', fn($q) => $q->where('full_name', 'like', "%{$search}%"));
            });
        }

        if ($dateFrom) {
            $query->whereDate('received_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('received_date', '<=', $dateTo);
        }

        return $query;
    }

    public function create()
    {
        $incomeTypes = IncomeType::orderBy('type')->get();
        $members     = Member::orderBy('full_name')->get();
        return view('income.create', compact('incomeTypes', 'members'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'income_type_id'       => 'required|exists:income_types,id',
            'amount'               => 'required|numeric|min:0',
            'received_date'        => 'required|date',
            'member_id'            => 'nullable|integer|exists:members,id',
            'contributor_name'     => 'nullable|string|max:255',
            'contributor_contacts' => 'nullable|string|max:100',
            'contributor_address'  => 'nullable|string|max:255',
            'comment'              => 'nullable|string',
            'attachment'           => 'nullable|file|max:10240',
            'status'               => 'nullable|string',
        ]);

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = uniqid() . '-' . preg_replace('/[^\w.\-]+/', '_', $file->getClientOriginalName());
            $path = $file->storeAs('uploads/income', $filename, 'public');
            $data['attachment_url'] = '/storage/' . $path;
        }
        unset($data['attachment']);
        $data['status'] = $data['status'] ?? 'Received';

        Income::create($data);
        return redirect()->route('income.index')->with('success', 'Income record added successfully!');
    }

    public function edit($id)
    {
        $income      = Income::findOrFail($id);
        $incomeTypes = IncomeType::orderBy('type')->get();
        $members     = Member::orderBy('full_name')->get();
        return view('income.edit', compact('income', 'incomeTypes', 'members'));
    }

    public function update(Request $request, $id)
    {
        $income = Income::findOrFail($id);
        $data   = $request->validate([
            'income_type_id'       => 'required|exists:income_types,id',
            'amount'               => 'required|numeric|min:0',
            'received_date'        => 'required|date',
            'member_id'            => 'nullable|integer|exists:members,id',
            'contributor_name'     => 'nullable|string|max:255',
            'contributor_contacts' => 'nullable|string|max:100',
            'contributor_address'  => 'nullable|string|max:255',
            'comment'              => 'nullable|string',
            'attachment'           => 'nullable|file|max:10240',
            'status'               => 'nullable|string',
        ]);

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = uniqid() . '-' . preg_replace('/[^\w.\-]+/', '_', $file->getClientOriginalName());
            $path = $file->storeAs('uploads/income', $filename, 'public');
            $data['attachment_url'] = '/storage/' . $path;
        }
        unset($data['attachment']);
        $data['status'] = $data['status'] ?? 'Received';

        $income->update($data);
        return redirect()->route('income.index')->with('success', 'Income record updated successfully!');
    }

    public function destroy($id)
    {
        Income::findOrFail($id)->delete();
        return redirect()->route('income.index')->with('success', 'Income record deleted successfully!');
    }
}
