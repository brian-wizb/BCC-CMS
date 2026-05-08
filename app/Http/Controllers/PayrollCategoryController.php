<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PayrollCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = \App\Models\PayrollCategory::all();
        return view('payroll-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('payroll-categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'type' => 'required|string',
            'charge_in' => 'required|string',
            'charge' => 'required|numeric',
            'deduct_after_paye' => 'nullable|boolean',
        ]);
        $data['deduct_after_paye'] = $request->has('deduct_after_paye');
        \App\Models\PayrollCategory::create($data);
        return redirect()->route('payroll-categories.index')->with('success', 'Category created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
