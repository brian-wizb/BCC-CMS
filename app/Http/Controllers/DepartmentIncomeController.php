<?php
namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\DepartmentIncome;

class DepartmentIncomeController extends Controller
{
    public function index(Request $request)
    {
        $department = $request->input('department');
        $query = DepartmentIncome::query();
        if ($department) {
            $query->where('department', $department);
        }
        $records = $query->orderByDesc('received_date')->get();
        $departments = ['CMF', 'WWK', "Youth (CA's)"];
        return view('department-income.index', compact('records', 'department', 'departments'));
    }

    public function create()
    {
        $departments = ['CMF', 'WWK', "Youth (CA's)"];
        return view('department-income.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'department' => 'required|string',
            'income_type' => 'required|string',
            'amount' => 'required|numeric',
            'received_date' => 'required|date',
            'attachment_url' => 'nullable|string',
            'comment' => 'nullable|string',
        ]);
        DepartmentIncome::create($data);
        return redirect()->route('department-income.index')->with('success', 'Department income added successfully!');
    }
}
