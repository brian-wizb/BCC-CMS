@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Pledge Payments</h1>
    <a href="{{ route('pledge-payments.create') }}" class="btn btn-primary mb-3">Add Payment</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Pledger Name</th>
                <th>Campaign</th>
                <th>Phone</th>
                <th>Amount</th>
                <th>Payment Date</th>
                <th>Method</th>
                <th>Notes</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pledgePayments as $i => $payment)
            @php
                $pledge = $payment->pledge;
            @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $pledge->pledger_name ?? $pledge->unregistered_name ?? '—' }}</td>
                <td>{{ $pledge->campaign->name ?? '' }}</td>
                <td>{{ $pledge->phone ?? $pledge->unregistered_phone ?? '—' }}</td>
                <td>{{ number_format($payment->amount, 2) }}</td>
                <td>{{ $payment->payment_date }}</td>
                <td>{{ $payment->method }}</td>
                <td>{{ $payment->notes }}</td>
                <td>
                    <a href="{{ route('pledge-payments.edit', $payment) }}" class="btn btn-sm btn-warning">Edit</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
