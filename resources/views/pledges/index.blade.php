@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Pledges</h1>
    <a href="{{ route('pledges.create') }}" class="btn btn-primary mb-3">Add Pledge</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Type</th>
                <th>Campaign</th>
                <th>Amount</th>
                <th>Paid</th>
                <th>Due</th>
                <th>Start Date</th>
                <th>Due Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pledges as $i => $pledge)
            @php
                $paid = $pledge->paid ?? 0;
                $due = ($pledge->amount ?? 0) - $paid;
            @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $pledge->pledger_name ?? $pledge->unregistered_name ?? '—' }}</td>
                <td>{{ $pledge->phone ?? $pledge->unregistered_phone ?? '—' }}</td>
                <td>{{ $pledge->pledge_type ?? '—' }}</td>
                <td>{{ $pledge->campaign->name ?? '' }}</td>
                <td>{{ number_format($pledge->amount, 2) }}</td>
                <td>{{ number_format($paid, 2) }}</td>
                <td class="{{ $due > 0 ? 'text-danger font-weight-bold' : 'text-success' }}">{{ number_format($due, 2) }}</td>
                <td>{{ $pledge->start_date ? \Carbon\Carbon::parse($pledge->start_date)->toDateString() : '' }}</td>
                <td>{{ $pledge->due_date ? \Carbon\Carbon::parse($pledge->due_date)->toDateString() : '' }}</td>
                <td>
                    <a href="{{ route('pledges.edit', $pledge) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('pledges.destroy', $pledge) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Delete this pledge?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
