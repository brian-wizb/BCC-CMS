<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IncomeType;

class IncomeTypeController extends Controller
{
    private function incomeTypeQuery(?string $search = null, ?string $dateFrom = null, ?string $dateTo = null)
    {
        $query = IncomeType::withCount('incomes');

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('type', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (! empty($dateFrom)) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if (! empty($dateTo)) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        return $query;
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = $this->incomeTypeQuery($search, $dateFrom, $dateTo);
        $perPage = in_array((int)$request->input('per_page'), [10,25,50,100]) ? (int)$request->input('per_page') : 20;
        $types = $query->orderBy('type')->paginate($perPage)->withQueryString();

        return view('income-types.index', compact('types', 'search', 'perPage', 'dateFrom', 'dateTo'));
    }

    public function export(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $records = $this->incomeTypeQuery($search, $dateFrom, $dateTo)
            ->orderBy('type')
            ->get();

        $filename = 'income-types-report-' . now()->format('YmdHis') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->streamDownload(function () use ($records) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Type', 'Description', 'Incomes Count', 'Created At']);

            foreach ($records as $type) {
                fputcsv($file, [
                    $type->id,
                    $type->type,
                    $type->description ?? '',
                    $type->incomes_count ?? 0,
                    $type->created_at ? \Carbon\Carbon::parse($type->created_at)->format('Y-m-d') : '',
                ]);
            }

            fclose($file);
        }, $filename, $headers);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type'        => 'required|string|max:150|unique:income_types,type',
            'description' => 'nullable|string',
        ]);
        IncomeType::create($data);
        return redirect()->route('income-types.index')->with('success', 'Income type added successfully.');
    }

    public function update(Request $request, $id)
    {
        $type = IncomeType::findOrFail($id);
        $data = $request->validate([
            'type'        => 'required|string|max:150|unique:income_types,type,' . $id,
            'description' => 'nullable|string',
        ]);
        $type->update($data);
        return redirect()->route('income-types.index')->with('success', 'Income type updated.');
    }

    public function destroy($id)
    {
        IncomeType::findOrFail($id)->delete();
        return redirect()->route('income-types.index')->with('success', 'Income type deleted.');
    }
}
