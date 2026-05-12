<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DepartmentIncome;

class DepartmentIncomeController extends Controller
{
    private array $departments = ['CMF', 'WWK', "Youth (CA's)"];

    public function index(Request $request)
    {
        $department = $request->input('department');
        $from       = $request->input('from');
        $to         = $request->input('to');
        $search     = $request->input('search');

        $query = DepartmentIncome::query();
        if ($department) $query->where('department', $department);
        if ($from)       $query->whereDate('received_date', '>=', $from);
        if ($to)         $query->whereDate('received_date', '<=', $to);
        if ($search)     $query->where(function($q) use ($search) {
            $q->where('income_type', 'like', "%{$search}%")
              ->orWhere('comment', 'like', "%{$search}%");
        });

        $total       = (clone $query)->sum('amount');
        $perPage = in_array((int)$request->input('per_page'), [10,25,50,100]) ? (int)$request->input('per_page') : 20;
        $records     = $query->orderByDesc('received_date')->paginate($perPage)->withQueryString();
        $departments = $this->departments;
        return view('department-income.index', compact('records', 'total', 'departments', 'department', 'from', 'to', 'search', 'perPage'));
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
