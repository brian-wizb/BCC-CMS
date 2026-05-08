@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4 max-w-lg">
    <h1 class="text-2xl font-bold mb-4">Add Income Record</h1>
    <form method="POST" action="{{ route('income.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block mb-1 font-semibold">Income Type</label>
            <select name="income_type_id" class="w-full border rounded px-2 py-1" required>
                <option value="">Select type</option>
                @foreach($incomeTypes as $type)
                    <option value="{{ $type->id }}">{{ $type->type }}</option>
                @endforeach
            </select>
            @error('income_type_id')
                <div class="text-red-600 text-sm">{{ $message }}</div>
            @enderror
        </div>
        <div>
            <label class="block mb-1 font-semibold">Amount</label>
            <input type="number" name="amount" step="0.01" class="w-full border rounded px-2 py-1" required />
            @error('amount')
                <div class="text-red-600 text-sm">{{ $message }}</div>
            @enderror
        </div>
        <div>
            <label class="block mb-1 font-semibold">Received Date</label>
            <input type="date" name="received_date" class="w-full border rounded px-2 py-1" required />
            @error('received_date')
                <div class="text-red-600 text-sm">{{ $message }}</div>
            @enderror
        </div>
        <div>
            <label class="block mb-1 font-semibold">Comment</label>
            <textarea name="comment" class="w-full border rounded px-2 py-1"></textarea>
            @error('comment')
                <div class="text-red-600 text-sm">{{ $message }}</div>
            @enderror
        </div>
        <div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Save</button>
            <a href="{{ route('income.index') }}" class="ml-2 text-gray-600">Cancel</a>
        </div>
    </form>
</div>
@endsection
