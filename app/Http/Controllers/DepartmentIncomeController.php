<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DepartmentIncome;

class DepartmentIncomeController extends Controller
{
    private array $departments = ['CMF', 'WWK', "Youth (CA's)"];

    private function departmentIncomeQuery(?string $department = null, ?string $search = null, ?string $dateFrom = null, ?string $dateTo = null)
    {
        $query = DepartmentIncome::query();

        if ($department) {
            $query->where('department', $department);
        }

        if ($dateFrom) {
            $query->whereDate('received_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('received_date', '<=', $dateTo);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('income_type', 'like', "%{$search}%")
                  ->orWhere('comment', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    public function index(Request $request)
    {
        $department = $request->input('department');
        $dateFrom   = $request->input('date_from', $request->input('from'));
        $dateTo     = $request->input('date_to', $request->input('to'));
        $search     = trim((string) $request->input('search'));

        $query = $this->departmentIncomeQuery($department, $search, $dateFrom, $dateTo);

        $total       = (clone $query)->sum('amount');
        $perPage = in_array((int)$request->input('per_page'), [10,25,50,100]) ? (int)$request->input('per_page') : 20;
        $records     = $query->orderByDesc('received_date')->paginate($perPage)->withQueryString();
        $departments = $this->departments;
        return view('department-income.index', compact('records', 'total', 'departments', 'department', 'dateFrom', 'dateTo', 'search', 'perPage'));
    }

    public function export(Request $request)
    {
        $department = $request->input('department');
        $dateFrom   = $request->input('date_from', $request->input('from'));
        $dateTo     = $request->input('date_to', $request->input('to'));
        $search     = trim((string) $request->input('search'));

        $records = $this->departmentIncomeQuery($department, $search, $dateFrom, $dateTo)
            ->orderByDesc('received_date')
            ->get();

        $filename = 'department-income-report-' . now()->format('YmdHis') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->streamDownload(function () use ($records) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Department', 'Income Type', 'Amount', 'Received Date', 'Comment']);

            foreach ($records as $record) {
                fputcsv($file, [
                    $record->id,
                    $record->department,
                    $record->income_type,
                    $record->amount,
                    $record->received_date ? \Carbon\Carbon::parse($record->received_date)->format('Y-m-d') : '',
                    $record->comment ?? '',
                ]);
            }

            fclose($file);
        }, $filename, $headers);
    }

    public function create()
    {
        $departments = $this->departments;
        return view('department-income.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'department'    => 'required|string',
            'income_type'   => 'required|string|max:255',
            'amount'        => 'required|numeric|min:0',
            'received_date' => 'required|date',
            'comment'       => 'nullable|string',
            'attachment'    => 'nullable|file|max:10240',
        ]);

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = uniqid() . '-' . preg_replace('/[^\w.\-]+/', '_', $file->getClientOriginalName());
            $path = $file->storeAs('uploads/dept-income', $filename, 'public');
            $data['attachment_url'] = '/storage/' . $path;
        }
        unset($data['attachment']);

        DepartmentIncome::create($data);
        return redirect()->route('department-income.index')->with('success', 'Department income added successfully!');
    }

    public function edit($id)
    {
        $record      = DepartmentIncome::findOrFail($id);
        $departments = $this->departments;
        return view('department-income.edit', compact('record', 'departments'));
    }

    public function update(Request $request, $id)
    {
        $record = DepartmentIncome::findOrFail($id);
        $data   = $request->validate([
            'department'    => 'required|string',
            'income_type'   => 'required|string|max:255',
            'amount'        => 'required|numeric|min:0',
            'received_date' => 'required|date',
            'comment'       => 'nullable|string',
            'attachment'    => 'nullable|file|max:10240',
        ]);

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = uniqid() . '-' . preg_replace('/[^\w.\-]+/', '_', $file->getClientOriginalName());
            $path = $file->storeAs('uploads/dept-income', $filename, 'public');
            $data['attachment_url'] = '/storage/' . $path;
        }
        unset($data['attachment']);

        $record->update($data);
        return redirect()->route('department-income.index')->with('success', 'Department income updated successfully!');
    }

    public function destroy($id)
    {
        DepartmentIncome::findOrFail($id)->delete();
        return redirect()->route('department-income.index')->with('success', 'Department income deleted successfully!');
    }
}
