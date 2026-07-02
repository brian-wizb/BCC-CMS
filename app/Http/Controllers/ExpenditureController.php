<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expenditure;

class ExpenditureController extends Controller
{
    private array $paymentMethods = ['Cash', 'Mobile', 'Credit', 'Cheque', 'Bank'];

    private function expenditureQuery(?string $search = null, ?string $dateFrom = null, ?string $dateTo = null)
    {
        $query = Expenditure::query();

        if (! empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('expense_category', 'like', "%{$search}%")
                  ->orWhere('reference_no', 'like', "%{$search}%");
            });
        }

        if (! empty($dateFrom)) {
            $query->whereDate('expense_date', '>=', $dateFrom);
        }

        if (! empty($dateTo)) {
            $query->whereDate('expense_date', '<=', $dateTo);
        }

        return $query;
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = $this->expenditureQuery($search, $dateFrom, $dateTo);

        $perPage = in_array((int)$request->input('per_page'), [10,25,50,100]) ? (int)$request->input('per_page') : 20;
        $expenditures = $query->orderByDesc('expense_date')->paginate($perPage)->withQueryString();
        return view('expenditures.index', compact('expenditures', 'search', 'perPage', 'dateFrom', 'dateTo'));
    }

    public function export(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $records = $this->expenditureQuery($search, $dateFrom, $dateTo)
            ->orderByDesc('expense_date')
            ->get();

        $filename = 'expenditures-report-' . now()->format('YmdHis') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->streamDownload(function () use ($records) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Expense Category', 'Payment Method', 'Amount', 'Expense Date', 'Reference', 'Status', 'Comment']);

            foreach ($records as $expense) {
                fputcsv($file, [
                    $expense->id,
                    $expense->expense_category,
                    $expense->payment_method,
                    $expense->amount,
                    $expense->expense_date ? \Carbon\Carbon::parse($expense->expense_date)->format('Y-m-d') : '',
                    $expense->reference_no ?? '',
                    $expense->status ?? 'Paid',
                    $expense->comment ?? '',
                ]);
            }

            fclose($file);
        }, $filename, $headers);
    }

    public function create()
    {
        $paymentMethods = $this->paymentMethods;
        return view('expenditures.create', compact('paymentMethods'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'expense_category' => 'required|string|max:255',
            'payment_method'   => 'required|string',
            'amount'           => 'required|numeric|min:0',
            'expense_date'     => 'required|date',
            'reference_no'     => 'nullable|string|max:100',
            'comment'          => 'nullable|string',
            'attachment'       => 'nullable|file|max:10240',
            'status'           => 'nullable|string',
        ]);
        if ($request->hasFile('attachment')) {
            $file     = $request->file('attachment');
            $filename = uniqid() . '-' . preg_replace('/[^\w.\-]+/', '_', $file->getClientOriginalName());
            $path     = $file->storeAs('uploads/expenditures', $filename, 'public');
            $data['attachment_url'] = '/storage/' . $path;
        }
        unset($data['attachment']);
        $data['status'] = $data['status'] ?? 'Paid';
        Expenditure::create($data);
        return redirect()->route('expenditures.index')->with('success', 'Expense recorded successfully.');
    }

    public function edit($id)
    {
        $expenditure    = Expenditure::findOrFail($id);
        $paymentMethods = $this->paymentMethods;
        return view('expenditures.edit', compact('expenditure', 'paymentMethods'));
    }

    public function update(Request $request, $id)
    {
        $expenditure = Expenditure::findOrFail($id);
        $data = $request->validate([
            'expense_category' => 'required|string|max:255',
            'payment_method'   => 'required|string',
            'amount'           => 'required|numeric|min:0',
            'expense_date'     => 'required|date',
            'reference_no'     => 'nullable|string|max:100',
            'comment'          => 'nullable|string',
            'attachment'       => 'nullable|file|max:10240',
            'status'           => 'nullable|string',
        ]);
        if ($request->hasFile('attachment')) {
            $file     = $request->file('attachment');
            $filename = uniqid() . '-' . preg_replace('/[^\w.\-]+/', '_', $file->getClientOriginalName());
            $path     = $file->storeAs('uploads/expenditures', $filename, 'public');
            $data['attachment_url'] = '/storage/' . $path;
        }
        unset($data['attachment']);
        $data['status'] = $data['status'] ?? 'Paid';
        $expenditure->update($data);
        return redirect()->route('expenditures.index')->with('success', 'Expense updated successfully.');
    }

    public function destroy($id)
    {
        Expenditure::findOrFail($id)->delete();
        return redirect()->route('expenditures.index')->with('success', 'Expense deleted successfully.');
    }
}
