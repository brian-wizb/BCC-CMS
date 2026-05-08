@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Donation Records</h1>
    <a href="{{ route('donations.create') }}" class="btn btn-primary mb-3">Add Donation</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Member</th>
                <th>Phone</th>
                <th>Tithe Code</th>
                <th>Total Donated</th>
                <th>Donations Count</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($donationSummaries as $idx => $summary)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td>{{ $summary['full_name'] }}</td>
                <td>{{ $summary['phone'] }}</td>
                <td>{{ $summary['tithe_code'] }}</td>
                <td>{{ number_format($summary['total_donated'], 2) }}</td>
                <td>{{ $summary['donation_count'] }}</td>
                <td>
                    <a href="{{ route('donations.show', $summary['member_id']) }}" class="btn btn-sm btn-info">View</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
