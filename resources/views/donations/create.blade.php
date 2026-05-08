@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Add Donation</h1>
    <form action="{{ route('donations.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-4">
            <label class="form-label">Member</label>
            <select name="member_id" class="form-control">
                <option value="">Select Member</option>
                {{-- TODO: Populate with members if available --}}
            </select>
        </div>
        <div class="row mb-4">
            <div class="col">
                <label class="form-label">Donation Type</label>
                <select name="donation_type" class="form-control">
                    <option value="">Select</option>
                    <option value="Sadaka ya Shukrani">Sadaka ya Shukrani</option>
                    <option value="Mission">Mission</option>
                    <option value="Tithe">Tithe (Zaka)</option>
                </select>
            </div>
            <div class="col">
                <label class="form-label">Amount</label>
                <input type="number" name="amount" class="form-control" placeholder="0" required>
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label">Tithe Code</label>
            <input type="text" name="tithe_code" class="form-control" readonly>
        </div>
        <div class="row mb-4">
            <div class="col">
                <label class="form-label">Reference</label>
                <input type="text" name="reference" class="form-control">
            </div>
            <div class="col">
                <label class="form-label">Payment Method</label>
                <select name="method" class="form-control" required>
                    <option value="Cash">Cash</option>
                    <option value="Mobile">Mobile</option>
                    <option value="Credit">Credit</option>
                    <option value="Cheque">Cheque</option>
                    <option value="Bank">Bank</option>
                </select>
            </div>
            <div class="col">
                <label class="form-label">Date</label>
                <input type="date" name="donation_date" class="form-control" required>
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label">Attachments (eg. Bank slip)</label>
            <input type="file" name="attachment" class="form-control">
        </div>
        <div class="mb-4">
            <label class="form-label">Details</label>
            <textarea name="notes" class="form-control" placeholder="if any"></textarea>
        </div>
        <button type="submit" class="btn btn-success">Save Donation</button>
    </form>
</div>
@endsection
