<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PayrollCategory;

class PayrollCategoryController extends Controller
{
    public function index()
    {
        $categories = PayrollCategory::all();
        return view('payroll-categories.index', compact('categories'));
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

