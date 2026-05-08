@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Add Missed Pledge</h1>
    <form action="{{ route('missed-pledges.store') }}" method="POST">
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
            <label class="form-label">Missed Date</label>
            <input type="date" name="missed_date" class="form-control">
        </div>
        <div class="mb-4">
            <label class="form-label">Reason</label>
            <textarea name="reason" class="form-control" placeholder="Optional"></textarea>
        </div>
        <button type="submit" class="btn btn-success">Save Missed Pledge</button>
    </form>
</div>
@endsection
