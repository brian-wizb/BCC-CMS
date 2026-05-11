<?php

namespace App\Http\Controllers;

use App\Models\DepartmentExpense;
use Illuminate\Http\Request;

class DepartmentExpenseController extends Controller
{
    public function index(Request $request)
    {
        $department = $request->input('department');
        $from       = $request->input('from');
        $to         = $request->input('to');

        $query = DepartmentExpense::query();

        if ($department) {
            $query->where('department', $department);
        }
        if ($from) {
            $query->whereDate('expense_date', '>=', $from);
        }
        if ($to) {
            $query->whereDate('expense_date', '<=', $to);
        }

        $records    = $query->orderByDesc('expense_date')->get();
        $total      = $records->sum('amount');
        $departments = ['CMF', 'WWK', "Youth (CA's)"];

        return view('department-expenses.index', compact('records', 'total', 'departments', 'department', 'from', 'to'));
    }

    public function create()
    {
        $departments    = ['CMF', 'WWK', "Youth (CA's)"];
        $paymentMethods = ['Cash', 'Mobile', 'Credit', 'Cheque', 'Bank'];
        return view('department-expenses.create', compact('departments', 'paymentMethods'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'department'     => 'required|string',
            'expense'        => 'required|string|max:255',
            'payment_method' => 'required|string',
            'amount'         => 'required|numeric|min:0',
            'expense_date'   => 'required|date',
            'reference_no'   => 'nullable|string|max:100',
            'comment'        => 'nullable|string',
            'attachment'     => 'nullable|file|max:10240',
        ]);

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = uniqid() . '-' . preg_replace('/[^\w.\-]+/', '_', $file->getClientOriginalName());
            $path = $file->storeAs('uploads/dept-expenses', $filename, 'public');
            $data['attachment_url'] = '/storage/' . $path;
        }
        unset($data['attachment']);

        DepartmentExpense::create($data);
        return redirect()->route('department-expenses.index')
            ->with('success', 'Department expense added successfully!');
    }

    public function edit($id)
    {
        $record      = DepartmentExpense::findOrFail($id);
        $departments    = ['CMF', 'WWK', "Youth (CA's)"];
        $paymentMethods = ['Cash', 'Mobile', 'Credit', 'Cheque', 'Bank'];
        return view('department-expenses.edit', compact('record', 'departments', 'paymentMethods'));
    }

    public function update(Request $request, $id)
    {
        $record = DepartmentExpense::findOrFail($id);
        $data   = $request->validate([
            'department'     => 'required|string',
            'expense'        => 'required|string|max:255',
            'payment_method' => 'required|string',
            'amount'         => 'required|numeric|min:0',
            'expense_date'   => 'required|date',
            'reference_no'   => 'nullable|string|max:100',
            'comment'        => 'nullable|string',
            'attachment'     => 'nullable|file|max:10240',
        ]);

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = uniqid() . '-' . preg_replace('/[^\w.\-]+/', '_', $file->getClientOriginalName());
            $path = $file->storeAs('uploads/dept-expenses', $filename, 'public');
            $data['attachment_url'] = '/storage/' . $path;
        }
        unset($data['attachment']);

        $record->update($data);
        return redirect()->route('department-expenses.index')
            ->with('success', 'Department expense updated successfully!');
    }

    public function destroy($id)
    {
        DepartmentExpense::findOrFail($id)->delete();
        return redirect()->route('department-expenses.index')
            ->with('success', 'Department expense deleted successfully!');
    }
}
