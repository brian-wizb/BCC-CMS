@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Add Department Income</h1>
    <form method="POST" action="{{ route('department-income.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Department</label>
            <select name="department" class="form-select" required>
                <option value="">Select Department</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept }}">{{ $dept }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Income Type</label>
            <input type="text" name="income_type" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Amount</label>
            <input type="number" name="amount" class="form-control" step="0.01" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Received Date</label>
            <input type="date" name="received_date" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Attachment URL</label>
            <input type="url" name="attachment_url" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Comment</label>
            <textarea name="comment" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-success">Save</button>
        <a href="{{ route('department-income.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
