@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Add Pledge Payment</h1>
    <form action="{{ route('pledge-payments.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-4">
            <label class="form-label">Pledge</label>
            <select name="pledge_id" class="form-control">
                @foreach($pledges as $pledge)
                    <option value="{{ $pledge->id }}">
                        {{ $pledge->pledger_name ?? $pledge->unregistered_name ?? '—' }} ({{ number_format($pledge->amount, 2) }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label class="form-label">Campaign</label>
            <select name="campaign_id" class="form-control">
                <option value="">Select Campaign</option>
                {{-- TODO: Populate with campaigns if available --}}
            </select>
        </div>
        <div class="mb-4">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control">
        </div>
        <div class="mb-4">
            <label class="form-label">Invoice Number</label>
            <input type="text" name="invoice_number" class="form-control">
        </div>
        <div class="mb-4">
            <label class="form-label">Amount</label>
            <input type="number" step="0.01" name="amount" class="form-control" required>
        </div>
        <div class="mb-4">
            <label class="form-label">Payment Method</label>
            <select name="method" class="form-control" required>
                <option value="Cash">Cash</option>
                <option value="Mobile">Mobile</option>
                <option value="Credit">Credit</option>
                <option value="Cheque">Cheque</option>
                <option value="Bank">Bank</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="form-label">Payment Date</label>
            <input type="date" name="payment_date" class="form-control" required>
        </div>
        <div class="mb-4">
            <label class="form-label">Attachment</label>
            <input type="file" name="attachment" class="form-control">
        </div>
        <div class="mb-4">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" placeholder="if any"></textarea>
        </div>
        <button type="submit" class="btn btn-success">Save Payment</button>
    </form>
</div>
@endsection
