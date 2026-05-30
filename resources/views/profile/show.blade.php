<x-layouts.app>
<div class="container py-4" style="max-width:640px">

    <h1 class="h5 fw-semibold mb-4" style="font-family:var(--font-display)">Profile</h1>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2 small mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Account details --}}
    <div class="card border-0 mb-4" style="background:var(--bg-raised);border:1px solid var(--border-faint)!important">
        <div class="card-header" style="padding:14px 20px">
            <span style="font-family:var(--font-mono);font-size:10px;font-weight:500;text-transform:uppercase;letter-spacing:.1em;color:var(--text-3)">Account Details</span>
        </div>
        <div class="card-body p-4">
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf @method('PATCH')
                <div class="mb-3">
                    <label class="form-label small fw-medium">Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $user->name) }}" required />
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-medium">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $user->email) }}" required />
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <button type="submit" class="wt-btn wt-btn-accent" style="padding:7px 20px">Save Changes</button>
            </form>
        </div>
    </div>

    {{-- Change password --}}
    <div class="card border-0" style="background:var(--bg-raised);border:1px solid var(--border-faint)!important">
        <div class="card-header" style="padding:14px 20px">
            <span style="font-family:var(--font-mono);font-size:10px;font-weight:500;text-transform:uppercase;letter-spacing:.1em;color:var(--text-3)">Change Password</span>
        </div>
        <div class="card-body p-4">
            <form method="POST" action="{{ route('profile.password') }}">
                @csrf @method('PATCH')
                <div class="mb-3">
                    <label class="form-label small fw-medium">Current Password</label>
                    <input type="password" name="current_password"
                           class="form-control @error('current_password') is-invalid @enderror" />
                    @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-medium">New Password</label>
                    <input type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror" />
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-medium">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="form-control" />
                </div>
                <button type="submit" class="wt-btn wt-btn-accent" style="padding:7px 20px">Update Password</button>
            </form>
        </div>
    </div>

</div>
</x-layouts.app>
