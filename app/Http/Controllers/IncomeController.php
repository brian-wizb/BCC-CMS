<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Income;
use App\Models\IncomeType;
use App\Models\Member;

class IncomeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query  = Income::with('incomeType');
        if ($search) {
            $query->whereHas('incomeType', fn($q) => $q->where('type', 'like', "%$search%"));
        }
        $perPage = in_array((int)$request->input('per_page'), [10,25,50,100]) ? (int)$request->input('per_page') : 20;
        $records = $query->orderByDesc('received_date')->paginate($perPage)->withQueryString();
        return view('income.index', compact('records', 'search', 'perPage'));
    }

    public function create()
    {
        $incomeTypes = IncomeType::orderBy('type')->get();
        $members     = Member::orderBy('full_name')->get();
        return view('income.create', compact('incomeTypes', 'members'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'income_type_id'       => 'required|exists:income_types,id',
            'amount'               => 'required|numeric|min:0',
            'received_date'        => 'required|date',
            'member_id'            => 'nullable|integer|exists:members,id',
            'contributor_name'     => 'nullable|string|max:255',
            'contributor_contacts' => 'nullable|string|max:100',
            'contributor_address'  => 'nullable|string|max:255',
            'comment'              => 'nullable|string',
            'attachment'           => 'nullable|file|max:10240',
            'status'               => 'nullable|string',
        ]);

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = uniqid() . '-' . preg_replace('/[^\w.\-]+/', '_', $file->getClientOriginalName());
            $path = $file->storeAs('uploads/income', $filename, 'public');
            $data['attachment_url'] = '/storage/' . $path;
        }
        unset($data['attachment']);
        $data['status'] = $data['status'] ?? 'Received';

        Income::create($data);
        return redirect()->route('income.index')->with('success', 'Income record added successfully!');
    }

    public function edit($id)
    {
        $income      = Income::findOrFail($id);
        $incomeTypes = IncomeType::orderBy('type')->get();
        $members     = Member::orderBy('full_name')->get();
        return view('income.edit', compact('income', 'incomeTypes', 'members'));
    }

    public function update(Request $request, $id)
    {
        $income = Income::findOrFail($id);
        $data   = $request->validate([
            'income_type_id'       => 'required|exists:income_types,id',
            'amount'               => 'required|numeric|min:0',
            'received_date'        => 'required|date',
            'member_id'            => 'nullable|integer|exists:members,id',
            'contributor_name'     => 'nullable|string|max:255',
            'contributor_contacts' => 'nullable|string|max:100',
            'contributor_address'  => 'nullable|string|max:255',
            'comment'              => 'nullable|string',
            'attachment'           => 'nullable|file|max:10240',
            'status'               => 'nullable|string',
        ]);

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = uniqid() . '-' . preg_replace('/[^\w.\-]+/', '_', $file->getClientOriginalName());
            $path = $file->storeAs('uploads/income', $filename, 'public');
            $data['attachment_url'] = '/storage/' . $path;
        }
        unset($data['attachment']);
        $data['status'] = $data['status'] ?? 'Received';

        $income->update($data);
        return redirect()->route('income.index')->with('success', 'Income record updated successfully!');
    }

    public function destroy($id)
    {
        Income::findOrFail($id)->delete();
        return redirect()->route('income.index')->with('success', 'Income record deleted successfully!');
    }
}
