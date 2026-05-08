@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Payroll Records</h1>
    <a href="{{ route('payroll.create') }}" class="btn btn-primary mb-3">Add Payroll</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Employee</th>
                <th>Designation</th>
                <th>Payment Method</th>
                <th>Gross Salary</th>
                <th>Net Salary</th>
                <th>Paid Amount</th>
                <th>Tax (%)</th>
                <th>PAYE</th>
                <th>Payment Date</th>
                <th>Attachment</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payrolls as $i => $payroll)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $payroll->employee->name ?? '' }}</td>
                <td>{{ $payroll->employee->designation ?? '—' }}</td>
                <td>{{ $payroll->method }}</td>
                <td>{{ number_format(($payroll->salary + ($payroll->church_staffs_addition ?? 0) + ($payroll->other_amount ?? 0)), 2) }}</td>
                <td>{{ number_format($payroll->net_salary, 2) }}</td>
                <td>{{ number_format($payroll->paid_amount, 2) }}</td>
                <td>{{ number_format($payroll->tax_percent, 2) }}</td>
                <td>{{ number_format($payroll->paye, 2) }}</td>
                <td>{{ $payroll->payment_date }}</td>
                <td>
                    @if($payroll->attachment_url)
                        <a href="{{ $payroll->attachment_url }}" target="_blank">View</a>
                    @else
                        —
                    @endif
                </td>
                <td>
                    <a href="{{ route('payroll.edit', $payroll) }}" class="btn btn-sm btn-warning">Edit</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
