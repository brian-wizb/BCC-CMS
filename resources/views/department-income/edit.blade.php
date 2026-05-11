@extends('layouts.app')
@section('title', 'Edit Department Income')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0"><i class="bi bi-pencil-square text-warning me-2"></i>Edit Department Income</h3>
        <a href="{{ route('department-income.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Income Records
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <div class="row">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('department-income.update', $record) }}" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label class="form-label fw-semibold"><i class="bi bi-building me-1"></i>Department</label>
                            <select name="department" class="form-select" required>
                                <option value="">— Select Department —</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept }}" {{ old('department', $record->department)==$dept?'selected':'' }}>{{ $dept }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold"><i class="bi bi-bookmark me-1"></i>Income Type</label>
                            <input type="text" name="income_type" class="form-control"
                                value="{{ old('income_type', $record->income_type) }}" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold"><i class="bi bi-currency-exchange me-1"></i>Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">Tsh.</span>
                                    <input type="number" step="1" min="0" name="amount" class="form-control"
                                        value="{{ old('amount', $record->amount) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold"><i class="bi bi-calendar me-1"></i>Received Date</label>
                                <input type="date" name="received_date" class="form-control"
                                    value="{{ old('received_date', $record->received_date) }}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold"><i class="bi bi-paperclip me-1"></i>Attachment</label>
                            @if($record->attachment_url)
                                <div class="mb-1">
                                    <a href="{{ $record->attachment_url }}" target="_blank" class="text-primary small">
                                        <i class="bi bi-file-earmark me-1"></i>View current attachment
                                    </a>
                                </div>
                            @endif
                            <input type="file" name="attachment" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold"><i class="bi bi-chat-left-text me-1"></i>Comment</label>
                            <textarea name="comment" class="form-control" rows="3">{{ old('comment', $record->comment) }}</textarea>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning px-4">
                                <i class="bi bi-save me-1"></i>Update
                            </button>
                            <a href="{{ route('department-income.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
