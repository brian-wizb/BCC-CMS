@extends('layouts.app')
@section('title', 'Edit Income')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0"><i class="bi bi-pencil-square text-warning me-2"></i>Edit Income Record</h3>
        <a href="{{ route('income.index') }}" class="btn btn-outline-secondary">
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
                    <form method="POST" action="{{ route('income.update', $income) }}" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label class="form-label fw-semibold"><i class="bi bi-bookmark me-1"></i>Income Type</label>
                            <select name="income_type_id" class="form-select @error('income_type_id') is-invalid @enderror" required>
                                <option value="">— Select type —</option>
                                @foreach($incomeTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('income_type_id', $income->income_type_id)==$type->id?'selected':'' }}>{{ $type->type }}</option>
                                @endforeach
                            </select>
                            @error('income_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold"><i class="bi bi-currency-exchange me-1"></i>Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">Tsh.</span>
                                    <input type="number" step="1" min="0" name="amount" class="form-control"
                                        value="{{ old('amount', $income->amount) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold"><i class="bi bi-calendar me-1"></i>Received Date</label>
                                <input type="date" name="received_date" class="form-control"
                                    value="{{ old('received_date', $income->received_date) }}" required>
                            </div>
                        </div>

                        <div class="card border bg-light mb-3">
                            <div class="card-header bg-transparent py-2">
                                <h6 class="mb-0 fw-semibold"><i class="bi bi-person-circle me-2 text-success"></i>Contributor Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">Existing Member (optional)</label>
                                    <select name="member_id" class="form-select form-select-sm">
                                        <option value="">— Search existing member —</option>
                                        @foreach($members as $member)
                                            <option value="{{ $member->id }}" {{ old('member_id', $income->member_id)==$member->id?'selected':'' }}>
                                                {{ $member->full_name ?? $member->name ?? $member->first_name.' '.$member->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label fw-semibold small">Contributor Name</label>
                                        <input type="text" name="contributor_name" class="form-control form-control-sm"
                                            value="{{ old('contributor_name', $income->contributor_name) }}">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label fw-semibold small">Contacts</label>
                                        <input type="text" name="contributor_contacts" class="form-control form-control-sm"
                                            value="{{ old('contributor_contacts', $income->contributor_contacts) }}">
                                    </div>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label fw-semibold small">Address</label>
                                    <input type="text" name="contributor_address" class="form-control form-control-sm"
                                        value="{{ old('contributor_address', $income->contributor_address) }}">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold"><i class="bi bi-paperclip me-1"></i>Attachment</label>
                            @if($income->attachment_url)
                                <div class="mb-1">
                                    <a href="{{ $income->attachment_url }}" target="_blank" class="text-primary small">
                                        <i class="bi bi-file-earmark me-1"></i>View current attachment
                                    </a>
                                </div>
                            @endif
                            <input type="file" name="attachment" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold"><i class="bi bi-chat-left-text me-1"></i>Comment</label>
                            <textarea name="comment" class="form-control" rows="3">{{ old('comment', $income->comment) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                <option value="Received" {{ old('status', $income->status)=='Received'?'selected':'' }}>Received</option>
                                <option value="Pending" {{ old('status', $income->status)=='Pending'?'selected':'' }}>Pending</option>
                            </select>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning px-4">
                                <i class="bi bi-save me-1"></i>Update
                            </button>
                            <a href="{{ route('income.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
