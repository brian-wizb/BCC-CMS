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
        $search     = $request->input('search');

        $query = DepartmentExpense::query();
        if ($department) $query->where('department', $department);
        if ($from)       $query->whereDate('expense_date', '>=', $from);
        if ($to)         $query->whereDate('expense_date', '<=', $to);
        if ($search)     $query->where(function($q) use ($search) {
            $q->where('expense', 'like', "%{$search}%")
              ->orWhere('comment', 'like', "%{$search}%")
              ->orWhere('reference_no', 'like', "%{$search}%");
        });

        $total      = (clone $query)->sum('amount');
        $perPage = in_array((int)$request->input('per_page'), [10,25,50,100]) ? (int)$request->input('per_page') : 20;
        $records    = $query->orderByDesc('expense_date')->paginate($perPage)->withQueryString();
        $departments = ['CMF', 'WWK', "Youth (CA's)"];
        return view('department-expenses.index', compact('records', 'total', 'departments', 'department', 'from', 'to', 'search', 'perPage'));
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
