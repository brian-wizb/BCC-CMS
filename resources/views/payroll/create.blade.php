@extends('layouts.app')
@section('title', 'Add Payroll')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0"><i class="bi bi-people text-primary me-2"></i>Add Payroll</h3>
        <a href="{{ route('payroll.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Payroll Records
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('payroll.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold"><i class="bi bi-person me-1"></i>Employee</label>
                                <select name="employee_id" class="form-select" required>
                                    <option value="">— Select employee —</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ old('employee_id')==$employee->id?'selected':'' }}>
                                            {{ $employee->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold"><i class="bi bi-calendar me-1"></i>Payment Date</label>
                                <input type="date" name="payment_date" class="form-control"
                                    value="{{ old('payment_date', date('Y-m-d')) }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold"><i class="bi bi-credit-card me-1"></i>Payment Method</label>
                                <select name="method" class="form-select">
                                    @foreach(['Cash','Mobile','Credit','Cheque','Bank'] as $m)
                                        <option value="{{ $m }}" {{ old('method')==$m?'selected':'' }}>{{ $m }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold"><i class="bi bi-person-badge me-1"></i>Account Name</label>
                                <input type="text" name="account_name" class="form-control" value="{{ old('account_name') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold"><i class="bi bi-bank me-1"></i>Account Number</label>
                                <input type="text" name="account_number" class="form-control" value="{{ old('account_number') }}">
                            </div>
                        </div>

                        <hr class="my-3">
                        <h6 class="fw-semibold text-muted mb-3"><i class="bi bi-calculator me-1"></i>Salary Breakdown</h6>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Gross Salary</label>
                                <div class="input-group">
                                    <span class="input-group-text">Tsh.</span>
                                    <input type="number" step="1" min="0" name="salary" id="salary" class="form-control"
                                        value="{{ old('salary') }}">
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Tax %</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" max="100" name="tax_percent" id="tax_percent" class="form-control"
                                        value="{{ old('tax_percent', 0) }}">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Church Addition</label>
                                <div class="input-group">
                                    <span class="input-group-text">Tsh.</span>
                                    <input type="number" step="1" min="0" name="church_staffs_addition" id="addition" class="form-control"
                                        value="{{ old('church_staffs_addition', 0) }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">PAYE</label>
                                <div class="input-group">
                                    <span class="input-group-text">Tsh.</span>
                                    <input type="number" step="1" name="paye" id="paye" class="form-control"
                                        value="{{ old('paye', 0) }}">
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Net Salary</label>
                                <div class="input-group">
                                    <span class="input-group-text">Tsh.</span>
                                    <input type="number" step="1" name="other_amount" id="net_salary" class="form-control"
                                        value="{{ old('other_amount') }}" readonly>
                                </div>
                            </div>
                        </div>

                        <hr class="my-3">
                        <div class="mb-3">
                            <label class="form-label fw-semibold"><i class="bi bi-chat-left-text me-1"></i>Details</label>
                            <textarea name="details" class="form-control" rows="3">{{ old('details') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold"><i class="bi bi-paperclip me-1"></i>Attachment</label>
                            <input type="file" name="attachment" class="form-control">
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-1"></i>Save Payroll
                            </button>
                            <a href="{{ route('payroll.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function calcNet() {
    const salary = parseFloat(document.getElementById('salary').value) || 0;
    const tax = parseFloat(document.getElementById('tax_percent').value) || 0;
    const addition = parseFloat(document.getElementById('addition').value) || 0;
    const paye = parseFloat(document.getElementById('paye').value) || 0;
    const net = salary + addition - (salary * tax / 100) - paye;
    document.getElementById('net_salary').value = Math.max(0, Math.round(net));
}
['salary','tax_percent','addition','paye'].forEach(id => {
    document.getElementById(id).addEventListener('input', calcNet);
});
</script>
@endsection
