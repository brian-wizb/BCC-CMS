<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PayrollCategory;

class PayrollCategoryController extends Controller
{
    private function categoryQuery(?string $search = null, ?string $dateFrom = null, ?string $dateTo = null)
    {
        $query = PayrollCategory::query();

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhere('charge_in', 'like', "%{$search}%");
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

        $query = $this->categoryQuery($search, $dateFrom, $dateTo);
        $perPage = in_array((int)$request->input('per_page'), [10,25,50,100]) ? (int)$request->input('per_page') : 20;
        $categories = $query->orderByDesc('created_at')->paginate($perPage)->withQueryString();

        return view('payroll-categories.index', compact('categories', 'search', 'perPage', 'dateFrom', 'dateTo'));
    }

    public function export(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $records = $this->categoryQuery($search, $dateFrom, $dateTo)
            ->orderByDesc('created_at')
            ->get();

        $filename = 'payroll-categories-report-' . now()->format('YmdHis') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->streamDownload(function () use ($records) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Name', 'Type', 'Charge In', 'Charge', 'Deduct After PAYE', 'Created At']);

            foreach ($records as $category) {
                fputcsv($file, [
                    $category->id,
                    $category->name,
                    $category->type,
                    $category->charge_in,
                    $category->charge,
                    $category->deduct_after_paye ? 'Yes' : 'No',
                    $category->created_at ? \Carbon\Carbon::parse($category->created_at)->format('Y-m-d') : '',
                ]);
            }

            fclose($file);
        }, $filename, $headers);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:100',
            'type'              => 'required|in:Addition,Deduction',
            'charge_in'         => 'required|in:Percent,Amount',
            'charge'            => 'required|numeric|min:0',
            'deduct_after_paye' => 'nullable|boolean',
            'comment'           => 'nullable|string',
        ]);
        $data['deduct_after_paye'] = $request->has('deduct_after_paye');
        PayrollCategory::create($data);
        return redirect()->route('payroll-categories.index')->with('success', 'Category created successfully.');
    }

    public function destroy($id)
    {
        PayrollCategory::findOrFail($id)->delete();
        return redirect()->route('payroll-categories.index')->with('success', 'Category deleted.');
    }
}

