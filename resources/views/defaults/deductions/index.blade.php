@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Default Deductions Management</h4>
        <div class="btn-group">
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addDeductionModal">
                <i class="bi bi-plus-circle me-1"></i> Add Default Deduction
            </button>
            <a href="{{ route('payslips.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Back to Payslips
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Sort Order</th>
                            <th>Name</th>
                            <th>Amount</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Statutory</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($defaultDeductions as $deduction)
                            <tr>
                                <td>{{ $deduction->sort_order }}</td>
                                <td>
                                    <strong>{{ $deduction->name }}</strong>
                                    @if(in_array($deduction->name, ['PAYE', 'NAPSA', 'NHIS', 'Personal Levy']))
                                        <span class="badge bg-warning ms-1">Statutory</span>
                                    @endif
                                </td>
                                <td>
                                    @if($deduction->type === 'percentage')
                                        {{ $deduction->amount }}%
                                    @else
                                        K{{ number_format($deduction->amount, 2) }}
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $deduction->type === 'percentage' ? 'warning' : 'success' }}">
                                        {{ ucfirst($deduction->type) }}
                                    </span>
                                </td>
                                <td>{{ $deduction->description ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $deduction->is_active ? 'success' : 'danger' }}">
                                        {{ $deduction->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $deduction->is_statutory ? 'warning' : 'secondary' }}">
                                        {{ $deduction->is_statutory ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-outline-primary" 
                                                onclick="editDeduction({{ $deduction->id }})">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        @if(!in_array($deduction->name, ['PAYE', 'NAPSA', 'NHIS', 'Personal Levy']))
                                            <form action="{{ route('default-deductions.toggle-status', $deduction->id) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-{{ $deduction->is_active ? 'warning' : 'success' }}">
                                                    <i class="bi bi-{{ $deduction->is_active ? 'pause' : 'play' }}"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('default-deductions.destroy', $deduction->id) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        onclick="return confirm('Are you sure you want to delete this default deduction?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">No default deductions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Deduction Modal -->
<div class="modal fade" id="addDeductionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Default Deduction</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="deductionForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="deduction_id" id="deductionId">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" id="deductionName" class="form-control" required>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Amount</label>
                            <input type="number" step="0.01" name="amount" id="deductionAmount" class="form-control" required min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type</label>
                            <select name="type" id="deductionType" class="form-select" required>
                                <option value="fixed">Fixed Amount</option>
                                <option value="percentage">Percentage</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="deductionDescription" class="form-control" rows="2"></textarea>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Sort Order</label>
                            <input type="number" name="sort_order" id="deductionSortOrder" class="form-control" min="0" value="0">
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mt-4">
                                <input type="checkbox" name="is_statutory" id="deductionIsStatutory" class="form-check-input">
                                <label class="form-check-label" for="deductionIsStatutory">Statutory Deduction</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-check">
                        <input type="checkbox" name="is_active" id="deductionIsActive" class="form-check-input" checked>
                        <label class="form-check-label" for="deductionIsActive">Active</label>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="submitBtn">Add Deduction</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function editDeduction(deductionId) {
    // This would typically fetch the deduction data via AJAX
    // For now, we'll just show the modal with the form
    document.getElementById('modalTitle').textContent = 'Edit Default Deduction';
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('deductionForm').action = `/default-deductions/${deductionId}`;
    document.getElementById('deductionId').value = deductionId;
    document.getElementById('submitBtn').textContent = 'Update Deduction';
    
    // Show modal
    new bootstrap.Modal(document.getElementById('addDeductionModal')).show();
}

// Reset form when modal is hidden
document.getElementById('addDeductionModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('modalTitle').textContent = 'Add Default Deduction';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('deductionForm').action = '{{ route("default-deductions.store") }}';
    document.getElementById('deductionId').value = '';
    document.getElementById('submitBtn').textContent = 'Add Deduction';
    document.getElementById('deductionForm').reset();
});
</script>
@endsection
