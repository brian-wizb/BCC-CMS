@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Campaigns</h1>
    <a href="{{ route('campaigns.create') }}" class="btn btn-primary mb-3">Add Campaign</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Required</th>
                <th>Paid</th>
                <th>Start</th>
                <th>End</th>
                <th>Progress</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($campaigns as $i => $campaign)
            @php
                $paid = $campaign->paid ?? 0;
                $required = $campaign->target_amount ?? 0;
                $now = \Carbon\Carbon::now();
                $start = $campaign->start_date ? \Carbon\Carbon::parse($campaign->start_date) : null;
                $end = $campaign->end_date ? \Carbon\Carbon::parse($campaign->end_date) : null;
                $progress = $start && $end ? ($now->lt($start) ? 'Upcoming' : ($now->gt($end) ? 'Closed' : 'On Going')) : '';
                $status = $paid >= $required ? 'Completed' : 'In Progress';
            @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $campaign->name }}</td>
                <td>{{ number_format($required, 2) }}</td>
                <td>{{ number_format($paid, 2) }}</td>
                <td>{{ $campaign->start_date ? \Carbon\Carbon::parse($campaign->start_date)->toDateString() : '' }}</td>
                <td>{{ $campaign->end_date ? \Carbon\Carbon::parse($campaign->end_date)->toDateString() : '' }}</td>
                <td>{{ $progress }}</td>
                <td>{{ $status }}</td>
                <td>
                    <a href="{{ route('campaigns.edit', $campaign) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('campaigns.destroy', $campaign) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Delete this campaign?');">
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
