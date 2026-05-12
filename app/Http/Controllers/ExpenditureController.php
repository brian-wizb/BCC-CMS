<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expenditure;

class ExpenditureController extends Controller
{
    private array $paymentMethods = ['Cash', 'Mobile', 'Credit', 'Cheque', 'Bank'];

    public function index(Request $request)
    {
        $search = $request->input('search');
        $query  = Expenditure::query();
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('expense_category', 'like', "%{$search}%")
                  ->orWhere('reference_no', 'like', "%{$search}%");
            });
        }
        $perPage = in_array((int)$request->input('per_page'), [10,25,50,100]) ? (int)$request->input('per_page') : 20;
        $expenditures = $query->orderByDesc('expense_date')->paginate($perPage)->withQueryString();
        return view('expenditures.index', compact('expenditures', 'search', 'perPage'));
    }

    public function create()
    {
        $paymentMethods = $this->paymentMethods;
        return view('expenditures.create', compact('paymentMethods'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'expense_category' => 'required|string|max:255',
            'payment_method'   => 'required|string',
            'amount'           => 'required|numeric|min:0',
            'expense_date'     => 'required|date',
            'reference_no'     => 'nullable|string|max:100',
            'comment'          => 'nullable|string',
            'attachment'       => 'nullable|file|max:10240',
            'status'           => 'nullable|string',
        ]);
        if ($request->hasFile('attachment')) {
            $file     = $request->file('attachment');
            $filename = uniqid() . '-' . preg_replace('/[^\w.\-]+/', '_', $file->getClientOriginalName());
            $path     = $file->storeAs('uploads/expenditures', $filename, 'public');
            $data['attachment_url'] = '/storage/' . $path;
        }
        unset($data['attachment']);
        $data['status'] = $data['status'] ?? 'Paid';
        Expenditure::create($data);
        return redirect()->route('expenditures.index')->with('success', 'Expense recorded successfully.');
    }

    public function edit($id)
    {
        $expenditure    = Expenditure::findOrFail($id);
        $paymentMethods = $this->paymentMethods;
        return view('expenditures.edit', compact('expenditure', 'paymentMethods'));
    }

    public function update(Request $request, $id)
    {
        $expenditure = Expenditure::findOrFail($id);
        $data = $request->validate([
            'expense_category' => 'required|string|max:255',
            'payment_method'   => 'required|string',
            'amount'           => 'required|numeric|min:0',
            'expense_date'     => 'required|date',
            'reference_no'     => 'nullable|string|max:100',
            'comment'          => 'nullable|string',
            'attachment'       => 'nullable|file|max:10240',
            'status'           => 'nullable|string',
        ]);
        if ($request->hasFile('attachment')) {
            $file     = $request->file('attachment');
            $filename = uniqid() . '-' . preg_replace('/[^\w.\-]+/', '_', $file->getClientOriginalName());
            $path     = $file->storeAs('uploads/expenditures', $filename, 'public');
            $data['attachment_url'] = '/storage/' . $path;
        }
        unset($data['attachment']);
        $data['status'] = $data['status'] ?? 'Paid';
        $expenditure->update($data);
        return redirect()->route('expenditures.index')->with('success', 'Expense updated successfully.');
    }

    public function destroy($id)
    {
        Expenditure::findOrFail($id)->delete();
        return redirect()->route('expenditures.index')->with('success', 'Expense deleted successfully.');
    }
}
