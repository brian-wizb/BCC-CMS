<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExpenditureController extends Controller
{
    public function index()
    {
        $expenditures = \App\Models\Expenditure::orderByDesc('date')->paginate(20);
        return view('expenditures.index', compact('expenditures'));
    }

    public function create()
    {
        return view('expenditures.create');
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'category' => 'nullable|string|max:100',
            'attachment' => 'nullable|file',
            'notes' => 'nullable|string',
        ]);
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('uploads/expenditures', 'public');
            $data['attachment'] = '/storage/' . $path;
        }
        \App\Models\Expenditure::create($data);
        return redirect()->route('expenditures.index')->with('success', 'Expenditure recorded successfully.');
    }

    public function edit($id)
    {
        $expenditure = \App\Models\Expenditure::findOrFail($id);
        return view('expenditures.edit', compact('expenditure'));
    }

    public function update(\Illuminate\Http\Request $request, $id)
    {
        $expenditure = \App\Models\Expenditure::findOrFail($id);
        $data = $request->validate([
            'date' => 'required|date',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'category' => 'nullable|string|max:100',
            'attachment' => 'nullable|file',
            'notes' => 'nullable|string',
        ]);
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('uploads/expenditures', 'public');
            $data['attachment'] = '/storage/' . $path;
        }
        $expenditure->update($data);
        return redirect()->route('expenditures.index')->with('success', 'Expenditure updated successfully.');
    }

    public function destroy($id)
    {
        $expenditure = \App\Models\Expenditure::findOrFail($id);
        $expenditure->delete();
        return redirect()->route('expenditures.index')->with('success', 'Expenditure deleted successfully.');
    }
}
