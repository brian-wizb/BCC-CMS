@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Missed Pledges</h1>
    <a href="{{ route('missed-pledges.create') }}" class="btn btn-primary mb-3">Add Missed Pledge</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Campaign</th>
                <th>Amount</th>
                <th>Paid</th>
                <th>Due</th>
                <th>Missed Date</th>
                <th>Reason</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($missedPledges as $i => $missed)
            @php
                $pledge = $missed->pledge;
                $paid = $pledge->paid ?? 0;
                $due = ($pledge->amount ?? 0) - $paid;
            @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $pledge->pledger_name ?? $pledge->unregistered_name ?? '—' }}</td>
                <td>{{ $pledge->phone ?? $pledge->unregistered_phone ?? '—' }}</td>
                <td>{{ $pledge->campaign->name ?? '' }}</td>
                <td>{{ number_format($pledge->amount, 2) }}</td>
                <td>{{ number_format($paid, 2) }}</td>
                <td class="text-danger font-weight-bold">{{ number_format($due, 2) }}</td>
                <td>{{ $missed->missed_date }}</td>
                <td>{{ $missed->reason }}</td>
                <td>
                    <a href="{{ route('missed-pledges.edit', $missed) }}" class="btn btn-sm btn-warning">Edit</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
