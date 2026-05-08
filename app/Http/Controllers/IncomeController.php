<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Income;
use App\Models\IncomeType;

class IncomeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Income::with('incomeType');
        if ($search) {
            $query->whereHas('incomeType', function ($q) use ($search) {
                $q->where('type', 'like', "%$search%");
            });
        }
        $records = $query->orderByDesc('received_date')->get();
        return view('income.index', compact('records', 'search'));
    }

    public function create()
    {
        $incomeTypes = IncomeType::all();
        return view('income.create', compact('incomeTypes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'income_type_id' => 'required|exists:income_types,id',
            'amount' => 'required|numeric',
            'received_date' => 'required|date',
            'member_id' => 'nullable|integer',
            'attachment_url' => 'nullable|string',
            'comment' => 'nullable|string',
        ]);
        Income::create($data);
        return redirect()->route('income.index')->with('success', 'Income record added successfully!');
    }
}
