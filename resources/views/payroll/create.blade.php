@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Add Payroll</h1>
    <form action="{{ route('payroll.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="employee_id" class="form-label">Employee</label>
            <select name="employee_id" id="employee_id" class="form-control">
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="payment_date" class="form-label">Payment Date</label>
            <input type="date" name="payment_date" id="payment_date" class="form-control">
        </div>
        <div class="mb-3">
            <label for="method" class="form-label">Method</label>
            <select name="method" id="method" class="form-control">
                <option value="cash">Cash</option>
                <option value="bank">Bank</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="account_name" class="form-label">Account Name</label>
            <input type="text" name="account_name" id="account_name" class="form-control">
        </div>
        <div class="mb-3">
            <label for="account_number" class="form-label">Account Number</label>
            <input type="text" name="account_number" id="account_number" class="form-control">
        </div>
        <div class="mb-3">
            <label for="salary" class="form-label">Salary</label>
            <input type="number" step="0.01" name="salary" id="salary" class="form-control">
        </div>
        <div class="mb-3">
            <label for="tax_percent" class="form-label">Tax Percent</label>
            <input type="number" step="0.01" name="tax_percent" id="tax_percent" class="form-control">
        </div>
        <div class="mb-3">
            <label for="church_staffs_addition" class="form-label">Church Staffs Addition</label>
            <input type="number" step="0.01" name="church_staffs_addition" id="church_staffs_addition" class="form-control">
        </div>
        <div class="mb-3">
            <label for="paye" class="form-label">PAYE</label>
            <input type="number" step="0.01" name="paye" id="paye" class="form-control">
        </div>
        <div class="mb-3">
            <label for="other_amount" class="form-label">Other Amount</label>
            <input type="number" step="0.01" name="other_amount" id="other_amount" class="form-control">
        </div>
        <div class="mb-3">
            <label for="details" class="form-label">Details</label>
            <textarea name="details" id="details" class="form-control"></textarea>
        </div>
        <div class="mb-3">
            <label for="attachment" class="form-label">Attachment</label>
            <input type="file" name="attachment" id="attachment" class="form-control">
        </div>
        <button type="submit" class="btn btn-success">Save Payroll</button>
    </form>
</div>
@endsection
