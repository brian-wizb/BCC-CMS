@extends('layouts.app')
@section('title', 'Department Income Records')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0"><i class="bi bi-building text-info me-2"></i>Department Income Records</h3>
        <a href="{{ route('department-income.create') }}" class="btn btn-info text-white">
            <i class="bi bi-plus-circle me-1"></i>Add Department Income
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('department-income.index') }}" class="row g-2 align-items-end">
                <div class="col-sm-3">
                    <label class="form-label small fw-semibold mb-1"><i class="bi bi-calendar-range me-1"></i>From</label>
                    <input type="date" name="from" value="{{ request('from') }}" class="form-control form-control-sm">
                </div>
                <div class="col-sm-3">
                    <label class="form-label small fw-semibold mb-1"><i class="bi bi-calendar-range me-1"></i>To</label>
                    <input type="date" name="to" value="{{ request('to') }}" class="form-control form-control-sm">
                </div>
                <div class="col-sm-3">
                    <label class="form-label small fw-semibold mb-1"><i class="bi bi-building me-1"></i>Department</label>
                    <select name="department" class="form-select form-select-sm">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept }}" {{ request('department')==$dept?'selected':'' }}>{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-auto">
                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('department-income.index') }}" class="btn btn-sm btn-outline-secondary ms-1">
                        <i class="bi bi-x-circle me-1"></i>Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Total banner --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="card border-0 bg-info bg-opacity-10">
                <div class="card-body py-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-info bg-opacity-25 p-3">
                        <i class="bi bi-cash-coin fs-4 text-info"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Income Amount</div>
                        <div class="fw-bold fs-5 text-info">Tsh. {{ number_format($total ?? 0) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" width="50">#</th>
                        <th>Department</th>
                        <th>Income Type</th>
                        <th>Amount (Tsh.)</th>
                        <th>Received Date</th>
                        <th>Attachment</th>
                        <th class="text-center" width="120">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                    <tr>
                        <td class="ps-4 text-muted">{{ $loop->iteration }}</td>
                        <td>
                            <span class="badge bg-info-subtle text-info px-2 py-1">
                                <i class="bi bi-building me-1"></i>{{ $record->department }}
                            </span>
                        </td>
                        <td>{{ $record->income_type }}</td>
                        <td class="fw-semibold text-info">{{ number_format($record->amount) }}</td>
                        <td>{{ \Carbon\Carbon::parse($record->received_date)->format('d M Y') }}</td>
                        <td>
                            @if($record->attachment_url)
                                <a href="{{ $record->attachment_url }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-paperclip"></i>
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('department-income.edit', $record) }}" class="btn btn-sm btn-outline-primary me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('department-income.destroy', $record) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Delete this record?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>No income records found.
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
