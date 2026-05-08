@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Payroll Categories</h1>
    <a href="{{ route('payroll-categories.create') }}" class="btn btn-primary mb-3">Add Category</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Type</th>
                <th>Charge In</th>
                <th>Charge</th>
                <th>Deduct After PAYE</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $category)
            <tr>
                <td>{{ $category->id }}</td>
                <td>{{ $category->name }}</td>
                <td>{{ $category->type }}</td>
                <td>{{ $category->charge_in }}</td>
                <td>{{ $category->charge }}</td>
                <td>{{ $category->deduct_after_paye ? 'Yes' : 'No' }}</td>
                <td>
                    <a href="{{ route('payroll-categories.edit', $category) }}" class="btn btn-sm btn-warning">Edit</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
