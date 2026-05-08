@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
	<div class="flex justify-between items-center mb-4">
		<h1>Income Records</h1>
		<a href="{{ route('income.create') }}" class="btn btn-primary">Add Income</a>
	</div>
	<form method="GET" action="{{ route('income.index') }}" class="mb-4 flex gap-2">
		<input type="text" name="search" value="{{ $search }}" placeholder="Search by type..." class="form-control" />
		<button type="submit" class="btn btn-secondary">Search</button>
	</form>
	@if(session('success'))
		<div class="bg-green-100 text-green-800 p-2 rounded mb-2">{{ session('success') }}</div>
	@endif
	<div class="overflow-x-auto">
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>Type</th>
					<th>Amount</th>
					<th>Received Date</th>
					<th>Comment</th>
				</tr>
			</thead>
			<tbody>
				@forelse($records as $income)
					<tr>
						<td>{{ $income->incomeType->type ?? '-' }}</td>
						<td>{{ number_format($income->amount, 2) }}</td>
						<td>{{ $income->received_date }}</td>
						<td>{{ $income->comment }}</td>
					</tr>
				@empty
					<tr>
						<td colspan="4" class="text-center">No income records found.</td>
					</tr>
				@endforelse
			</tbody>
		</table>
	</div>
</div>
@endsection
