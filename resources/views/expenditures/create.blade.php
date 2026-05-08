@extends('layouts.app')
@section('content')
<div class="container mx-auto py-8 max-w-lg">
    <h1 class="text-2xl font-bold mb-6">Add Expenditure</h1>
    <form action="{{ route('expenditures.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 bg-white p-6 rounded shadow">
        @csrf
        <div>
            <label class="block mb-1 font-medium">Date</label>
            <input type="date" name="date" class="form-control w-full" required value="{{ old('date') }}">
        </div>
        <div>
            <label class="block mb-1 font-medium">Description</label>
            <input type="text" name="description" class="form-control w-full" required value="{{ old('description') }}">
        </div>
        <div>
            <label class="block mb-1 font-medium">Amount</label>
            <input type="number" step="0.01" name="amount" class="form-control w-full" required value="{{ old('amount') }}">
        </div>
        <div>
            <label class="block mb-1 font-medium">Category</label>
            <input type="text" name="category" class="form-control w-full" value="{{ old('category') }}">
        </div>
        <div>
            <label class="block mb-1 font-medium">Attachment</label>
            <input type="file" name="attachment" class="form-control w-full">
        </div>
        <div>
            <label class="block mb-1 font-medium">Notes</label>
            <textarea name="notes" class="form-control w-full">{{ old('notes') }}</textarea>
        </div>
        <div class="flex justify-end">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save</button>
        </div>
    </form>
</div>
@endsection
