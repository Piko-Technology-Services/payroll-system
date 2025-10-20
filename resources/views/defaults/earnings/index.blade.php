@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Default Earnings Management</h4>
        <div class="btn-group">
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addEarningModal">
                <i class="bi bi-plus-circle me-1"></i> Add Default Earning
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
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($defaultEarnings as $earning)
                            <tr>
                                <td>{{ $earning->sort_order }}</td>
                                <td>
                                    <strong>{{ $earning->name }}</strong>
                                    @if($earning->name === 'Basic Pay')
                                        <span class="badge bg-info ms-1">System</span>
                                    @endif
                                </td>
                                <td>
                                    @if($earning->type === 'percentage')
                                        {{ $earning->amount }}%
                                    @else
                                        K{{ number_format($earning->amount, 2) }}
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $earning->type === 'percentage' ? 'warning' : 'success' }}">
                                        {{ ucfirst($earning->type) }}
                                    </span>
                                </td>
                                <td>{{ $earning->description ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $earning->is_active ? 'success' : 'danger' }}">
                                        {{ $earning->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-outline-primary" 
                                                onclick="editEarning({{ $earning->id }})">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        @if($earning->name !== 'Basic Pay')
                                            <form action="{{ route('default-earnings.toggle-status', $earning->id) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-{{ $earning->is_active ? 'warning' : 'success' }}">
                                                    <i class="bi bi-{{ $earning->is_active ? 'pause' : 'play' }}"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('default-earnings.destroy', $earning->id) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        onclick="return confirm('Are you sure you want to delete this default earning?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No default earnings found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Earning Modal -->
<div class="modal fade" id="addEarningModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Default Earning</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="earningForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="earning_id" id="earningId">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" id="earningName" class="form-control" required>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Amount</label>
                            <input type="number" step="0.01" name="amount" id="earningAmount" class="form-control" required min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type</label>
                            <select name="type" id="earningType" class="form-select" required>
                                <option value="fixed">Fixed Amount</option>
                                <option value="percentage">Percentage</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="earningDescription" class="form-control" rows="2"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" id="earningSortOrder" class="form-control" min="0" value="0">
                    </div>
                    
                    <div class="form-check">
                        <input type="checkbox" name="is_active" id="earningIsActive" class="form-check-input" checked>
                        <label class="form-check-label" for="earningIsActive">Active</label>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="submitBtn">Add Earning</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", () => {
    // Make editEarning globally accessible
    window.editEarning = async function (earningId) {
        try {
            // Fetch earning details via AJAX
            const response = await fetch(`/default-earnings/${earningId}/edit`);
            if (!response.ok) throw new Error('Failed to fetch earning details');
            
            const data = await response.json();

            // Update modal title and form attributes
            document.getElementById('modalTitle').textContent = 'Edit Default Earning';
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('earningForm').action = `/default-earnings/${earningId}`;
            document.getElementById('earningId').value = earningId;
            document.getElementById('submitBtn').textContent = 'Update Earning';

            // Populate form fields
            document.getElementById('earningName').value = data.name || '';
            document.getElementById('earningAmount').value = data.amount || '';
            document.getElementById('earningType').value = data.type || 'fixed';
            document.getElementById('earningDescription').value = data.description || '';
            document.getElementById('earningSortOrder').value = data.sort_order || 0;
            document.getElementById('earningIsActive').checked = !!data.is_active;

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('addEarningModal'));
            modal.show();

        } catch (error) {
            console.error(error);
            alert('Error loading earning details. Please try again.');
        }
    };

    // Reset modal when hidden
    const earningModal = document.getElementById('addEarningModal');
    earningModal.addEventListener('hidden.bs.modal', () => {
        document.getElementById('modalTitle').textContent = 'Add Default Earning';
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('earningForm').action = '{{ route("default-earnings.store") }}';
        document.getElementById('earningId').value = '';
        document.getElementById('submitBtn').textContent = 'Add Earning';
        document.getElementById('earningForm').reset();
    });
});
</script>
@endsection

