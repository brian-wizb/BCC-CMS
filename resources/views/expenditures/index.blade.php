
@extends('layouts.app')
@section('content')
<div class="container mx-auto py-8">
	<div class="flex justify-between items-center mb-6">
		<h1>Expenditures</h1>
		<a href="{{ route('expenditures.create') }}" class="btn btn-primary">Add Expenditure</a>
	</div>
	@if(session('success'))
		<div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
	@endif
	<div class="overflow-x-auto">
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>Date</th>
					<th>Description</th>
					<th>Amount</th>
					<th>Category</th>
					<th>Attachment</th>
					<th>Notes</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				@forelse($expenditures as $expenditure)
				<tr>
					<td>{{ $expenditure->date }}</td>
					<td>{{ $expenditure->description }}</td>
					<td>{{ number_format($expenditure->amount, 2) }}</td>
					<td>{{ $expenditure->category }}</td>
					<td>
						@if($expenditure->attachment)
							<a href="{{ $expenditure->attachment }}" target="_blank">View</a>
						@endif
					</td>
					<td>{{ $expenditure->notes }}</td>
					<td>
						<a href="{{ route('expenditures.edit', $expenditure) }}" class="btn btn-sm btn-warning">Edit</a>
						<form action="{{ route('expenditures.destroy', $expenditure) }}" method="POST" class="inline">
							@csrf
							@method('DELETE')
							<button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this expenditure?')">Delete</button>
						</form>
					</td>
				</tr>
				@empty
				<tr>
					<td colspan="7" class="text-center">No expenditures found.</td>
				</tr>
				@endforelse
			</tbody>
		</table>
	</div>
	<div class="mt-4">{{ $expenditures->links() }}</div>
</div>
@endsection

