<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payrolls = \App\Models\Payroll::with('employee')->orderByDesc('payment_date')->get();
        return view('payroll.index', compact('payrolls'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = \App\Models\Employee::all();
        return view('payroll.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'payment_date' => 'required|date',
            'method' => 'required|string',
            'account_name' => 'nullable|string',
            'account_number' => 'nullable|string',
            'salary' => 'required|numeric',
            'tax_percent' => 'nullable|numeric',
            'church_staffs_addition' => 'nullable|numeric',
            'paye' => 'nullable|numeric',
            'other_amount' => 'nullable|numeric',
            'details' => 'nullable|string',
            'attachment' => 'nullable|file',
        ]);

        // Derived values
        $gross = $data['salary'] + ($data['church_staffs_addition'] ?? 0) + ($data['other_amount'] ?? 0);
        $net_salary = $gross - ($data['paye'] ?? 0);
        $take_home = $net_salary;
        $paid_amount = $take_home;

        $attachment_url = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = uniqid() . '-' . preg_replace('/[^\w.\-]+/', '_', $file->getClientOriginalName());
            $path = $file->storeAs('uploads', $filename, 'public');
            $attachment_url = '/storage/' . $path;
        }

        $payroll = \App\Models\Payroll::create([
            'employee_id' => $data['employee_id'],
            'payment_date' => $data['payment_date'],
            'method' => $data['method'],
            'account_name' => $data['account_name'] ?? null,
            'account_number' => $data['account_number'] ?? null,
            'salary' => $data['salary'],
            'tax_percent' => $data['tax_percent'] ?? 0,
            'church_staffs_addition' => $data['church_staffs_addition'] ?? 0,
            'paye' => $data['paye'] ?? 0,
            'other_amount' => $data['other_amount'] ?? 0,
            'net_salary' => $net_salary,
            'take_home' => $take_home,
            'paid_amount' => $paid_amount,
            'details' => $data['details'] ?? null,
            'attachment_url' => $attachment_url,
        ]);

        return redirect()->route('payroll.index')->with('success', 'Payroll record created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Payroll $payroll)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payroll $payroll)
    {
        $employees = \App\Models\Employee::all();
        return view('payroll.edit', compact('payroll', 'employees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payroll $payroll)
    {
        $data = $request->validate([
            'employee_id'           => 'required|exists:employees,id',
            'payment_date'          => 'required|date',
            'method'                => 'required|string',
            'account_name'          => 'nullable|string',
            'account_number'        => 'nullable|string',
            'salary'                => 'required|numeric',
            'tax_percent'           => 'nullable|numeric',
            'church_staffs_addition'=> 'nullable|numeric',
            'paye'                  => 'nullable|numeric',
            'details'               => 'nullable|string',
            'attachment'            => 'nullable|file',
        ]);

        $gross      = $data['salary'] + ($data['church_staffs_addition'] ?? 0);
        $net_salary = $gross - ($data['paye'] ?? 0);
        $data['net_salary']   = $net_salary;
        $data['take_home']    = $net_salary;
        $data['paid_amount']  = $net_salary;

        if ($request->hasFile('attachment')) {
            $file     = $request->file('attachment');
            $filename = uniqid() . '-' . preg_replace('/[^\w.\-]+/', '_', $file->getClientOriginalName());
            $path     = $file->storeAs('uploads', $filename, 'public');
            $data['attachment_url'] = '/storage/' . $path;
        }
        unset($data['attachment']);

        $payroll->update($data);
        return redirect()->route('payroll.index')->with('success', 'Payroll record updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payroll $payroll)
    {
        $payroll->delete();
        return redirect()->route('payroll.index')->with('success', 'Payroll record deleted.');
    }
}
