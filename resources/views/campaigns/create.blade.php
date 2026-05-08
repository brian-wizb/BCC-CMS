@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Add Campaign</h1>
    <form action="{{ route('campaigns.store') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label for="name" class="form-label">Campaign Name</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="e.g., Construction" required>
        </div>
        <div class="mb-4">
            <label for="target_amount" class="form-label">Amount Required (Tsh)</label>
            <input type="number" step="0.01" name="target_amount" id="target_amount" class="form-control" placeholder="0" required>
        </div>
        <div class="row mb-4">
            <div class="col">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" name="start_date" id="start_date" class="form-control" required>
            </div>
            <div class="col">
                <label for="end_date" class="form-label">Final Date</label>
                <input type="date" name="end_date" id="end_date" class="form-control" required>
            </div>
        </div>
        <div class="mb-4">
            <label for="description" class="form-label">Comment</label>
            <textarea name="description" id="description" class="form-control" placeholder="Optional"></textarea>
        </div>
        <button type="submit" class="btn btn-success">Save Campaign</button>
    </form>
</div>
@endsection
