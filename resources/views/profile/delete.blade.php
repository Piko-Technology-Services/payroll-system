@extends('layouts.app')

@section('title', 'Delete Account')
@section('page-title', 'Delete Account')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>Delete Account
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger" role="alert">
                        <h6 class="alert-heading">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>Warning!
                        </h6>
                        <p class="mb-0">
                            This action is <strong>irreversible</strong>. Once you delete your account, 
                            all of your data will be permanently removed from our servers.
                        </p>
                    </div>

                    <div class="mb-4">
                        <h6>What will be deleted:</h6>
                        <ul class="list-unstyled">
                            <li><i class="bi bi-check text-danger me-2"></i>Your profile information</li>
                            <li><i class="bi bi-check text-danger me-2"></i>Your account settings</li>
                            <li><i class="bi bi-check text-danger me-2"></i>Your profile picture</li>
                            <li><i class="bi bi-check text-danger me-2"></i>All associated data</li>
                        </ul>
                    </div>

                    <form action="{{ route('profile.destroy') }}" method="POST" id="deleteAccountForm">
                        @csrf
                        @method('DELETE')
                        
                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold">
                                Confirm your password to continue <span class="text-danger">*</span>
                            </label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" required 
                                   placeholder="Enter your current password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="confirmDelete" required>
                                <label class="form-check-label" for="confirmDelete">
                                    I understand that this action cannot be undone and I want to permanently delete my account.
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('profile.show') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-danger" id="deleteButton" disabled>
                                <i class="bi bi-trash me-2"></i>Delete My Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Safety Information --}}
            <div class="card mt-4 border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>Need Help?
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        If you're having issues with your account or just need a break, 
                        consider these alternatives:
                    </p>
                    <ul class="list-unstyled mb-0">
                        <li><i class="bi bi-arrow-right me-2"></i>Contact support for assistance</li>
                        <li><i class="bi bi-arrow-right me-2"></i>Update your profile settings instead</li>
                        <li><i class="bi bi-arrow-right me-2"></i>Change your password if security is a concern</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const confirmCheckbox = document.getElementById('confirmDelete');
    const deleteButton = document.getElementById('deleteButton');
    const deleteForm = document.getElementById('deleteAccountForm');
    
    // Enable/disable delete button based on checkbox
    confirmCheckbox.addEventListener('change', function() {
        deleteButton.disabled = !this.checked;
    });
    
    // Additional confirmation before form submission
    deleteForm.addEventListener('submit', function(e) {
        const confirmed = confirm(
            'Are you absolutely sure you want to delete your account? ' +
            'This action cannot be undone and all your data will be permanently lost.'
        );
        
        if (!confirmed) {
            e.preventDefault();
        }
    });
    
    // Auto-focus password field
    document.getElementById('password').focus();
});
</script>
@endsection
