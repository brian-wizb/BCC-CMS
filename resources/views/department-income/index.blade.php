@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Department Income Records</h1>
    <form method="GET" class="mb-3">
        <select name="department" onchange="this.form.submit()" class="form-select w-auto d-inline-block">
            <option value="">All Departments</option>
            @foreach($departments as $dept)
                <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
            @endforeach
        </select>
        <a href="{{ route('department-income.create') }}" class="btn btn-primary ms-3">+ Add Department Income</a>
    </form>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Department</th>
                <th>Income Type</th>
                <th>Amount</th>
                <th>Received Date</th>
                <th>Attachment</th>
                <th>Comment</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $record)
            <tr>
                <td>{{ $record->id }}</td>
                <td>{{ $record->department }}</td>
                <td>{{ $record->income_type }}</td>
                <td>{{ number_format($record->amount, 2) }}</td>
                <td>{{ $record->received_date }}</td>
                <td>
                    @if($record->attachment_url)
                        <a href="{{ $record->attachment_url }}" target="_blank">View</a>
                    @else
                        &mdash;
                    @endif
                </td>
                <td>{{ $record->comment }}</td>
            </tr>
            @empty
            <tr><td colspan="7">No records found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
