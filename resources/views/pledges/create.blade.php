@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Add Pledge</h1>
    <form action="{{ route('pledges.store') }}" method="POST">
        @csrf
        <div class="mb-4 p-3 border rounded">
            <h5>Registered Member</h5>
            <select name="member_id" class="form-control mb-2">
                <option value="">Select Member</option>
                {{-- TODO: Populate with members if available --}}
            </select>
        </div>
        <div class="mb-4 p-3 border rounded">
            <h5>Unregistered Pledger</h5>
            <div class="row">
                <div class="col">
                    <input type="text" name="unregistered_name" class="form-control mb-2" placeholder="Name">
                </div>
                <div class="col">
                    <input type="text" name="unregistered_phone" class="form-control mb-2" placeholder="Phone Number">
                </div>
            </div>
        </div>
        <div class="mb-4 p-3 border rounded">
            <h5>Pledge Details</h5>
            <div class="row mb-2">
                <div class="col">
                    <label class="form-label">Pledge Type</label>
                    <select name="pledge_type" class="form-control">
                        <option value="Individual">Individual</option>
                        <option value="Family">Family</option>
                        <option value="Group">Group</option>
                    </select>
                </div>
                <div class="col">
                    <label class="form-label">Campaign</label>
                    <select name="campaign_id" class="form-control">
                        <option value="">Select Campaign</option>
                        @foreach($campaigns as $campaign)
                            <option value="{{ $campaign->id }}">{{ $campaign->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col">
                    <label class="form-label">Amount</label>
                    <input type="number" step="0.01" name="amount" class="form-control" placeholder="0">
                </div>
                <div class="col">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control">
                </div>
                <div class="col">
                    <label class="form-label">Due Date</label>
                    <input type="date" name="due_date" class="form-control">
                </div>
            </div>
        </div>
        <div class="mb-4 p-3 border rounded">
            <h5>Comment</h5>
            <textarea name="comment" class="form-control" placeholder="Optional comment..."></textarea>
        </div>
        <button type="submit" class="btn btn-success">Save Pledge</button>
    </form>
</div>
@endsection
