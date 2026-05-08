@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Add Payroll Category</h1>
    <form action="{{ route('payroll-categories.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" id="name" class="form-control">
        </div>
        <div class="mb-3">
            <label for="type" class="form-label">Type</label>
            <select name="type" id="type" class="form-control">
                <option value="Addition">Addition</option>
                <option value="Deduction">Deduction</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="charge_in" class="form-label">Charge In</label>
            <select name="charge_in" id="charge_in" class="form-control">
                <option value="Amount">Amount</option>
                <option value="Percent">Percent</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="charge" class="form-label">Charge</label>
            <input type="number" step="0.01" name="charge" id="charge" class="form-control">
        </div>
        <div class="mb-3">
            <label for="deduct_after_paye" class="form-label">Deduct After PAYE</label>
            <input type="checkbox" name="deduct_after_paye" id="deduct_after_paye" value="1">
        </div>
        <button type="submit" class="btn btn-success">Save Category</button>
    </form>
</div>
@endsection
