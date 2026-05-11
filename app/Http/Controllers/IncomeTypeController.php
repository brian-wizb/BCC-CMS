<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IncomeType;

class IncomeTypeController extends Controller
{
    public function index()
    {
        $types = IncomeType::withCount('incomes')->orderBy('type')->get();
        return view('income-types.index', compact('types'));
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
