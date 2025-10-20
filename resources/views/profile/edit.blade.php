@extends('layouts.app')

@section('title', 'Edit Profile')
@section('page-title', 'Edit Profile')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person-gear me-2"></i>Edit Profile Information
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Avatar Section --}}
                        <div class="row mb-4">
                            <div class="col-12">
                                <label class="form-label fw-bold">Profile Picture</label>
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        @if($user->avatar)
                                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="Current Avatar" 
                                                 class="rounded-circle" width="80" height="80" style="object-fit: cover;">
                                        @else
                                            <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center" 
                                                 style="width: 80px; height: 80px;">
                                                <i class="bi bi-person-fill text-white" style="font-size: 2rem;"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <input type="file" class="form-control @error('avatar') is-invalid @enderror" 
                                               id="avatar" name="avatar" accept="image/*">
                                        <small class="form-text text-muted">
                                            Upload a new profile picture (JPEG, PNG, JPG, GIF, max 2MB)
                                        </small>
                                        @error('avatar')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Basic Information --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label fw-bold">Phone Number</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $user->phone) }}" 
                                       placeholder="+260 XXX XXX XXX">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label fw-bold">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" name="address" rows="3" 
                                      placeholder="Enter your full address">{{ old('address', $user->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="bio" class="form-label fw-bold">Bio</label>
                            <textarea class="form-control @error('bio') is-invalid @enderror" 
                                      id="bio" name="bio" rows="4" 
                                      placeholder="Tell us about yourself...">{{ old('bio', $user->bio) }}</textarea>
                            <small class="form-text text-muted">Maximum 1000 characters</small>
                            @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Action Buttons --}}
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('profile.show') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Preview avatar before upload
    const avatarInput = document.getElementById('avatar');
    const currentAvatar = document.querySelector('img[alt="Current Avatar"], .bg-secondary');
    
    avatarInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                if (currentAvatar.tagName === 'IMG') {
                    currentAvatar.src = e.target.result;
                } else {
                    // Replace the placeholder with an image
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = 'Avatar Preview';
                    img.className = 'rounded-circle';
                    img.style.width = '80px';
                    img.style.height = '80px';
                    img.style.objectFit = 'cover';
                    currentAvatar.parentNode.replaceChild(img, currentAvatar);
                }
            };
            reader.readAsDataURL(file);
        }
    });

    // Character counter for bio
    const bioTextarea = document.getElementById('bio');
    const maxLength = 1000;
    
    function updateCharCount() {
        const remaining = maxLength - bioTextarea.value.length;
        const helpText = bioTextarea.parentNode.querySelector('.form-text');
        helpText.textContent = `${remaining} characters remaining (max ${maxLength})`;
        
        if (remaining < 50) {
            helpText.classList.add('text-warning');
        } else {
            helpText.classList.remove('text-warning');
        }
        
        if (remaining < 0) {
            helpText.classList.add('text-danger');
            helpText.classList.remove('text-warning');
        } else {
            helpText.classList.remove('text-danger');
        }
    }
    
    bioTextarea.addEventListener('input', updateCharCount);
    updateCharCount(); // Initial count
});
</script>
@endsection
