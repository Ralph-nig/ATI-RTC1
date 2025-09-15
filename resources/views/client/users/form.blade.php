<div class="form-grid">
    <!-- Full Name -->
    <div class="form-group">
        <label for="name" class="form-label required">Full Name</label>
        <div class="input-group">
            <i class="fas fa-user"></i>
            <input type="text" id="name" name="name" class="form-input" 
                   value="{{ old('name', $user->name ?? '') }}" required placeholder="Enter user's full name">
        </div>
        @error('name')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>
    
    <!-- Email Address -->
    <div class="form-group">
        <label for="email" class="form-label required">Email Address</label>
        <div class="input-group">
            <i class="fas fa-envelope"></i>
            <input type="email" id="email" name="email" class="form-input" 
                   value="{{ old('email', $user->email ?? '') }}" required placeholder="Enter email address">
        </div>
        @error('email')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>
    
    <!-- Password -->
    <div class="form-group">
        <label for="password" class="form-label {{ isset($user) ? '' : 'required' }}">Password</label>
        <div class="input-group">
            <i class="fas fa-lock"></i>
            <input type="password" id="password" name="password" class="form-input" 
                   {{ isset($user) ? '' : 'required' }} 
                   placeholder="{{ isset($user) ? 'Current password' : 'Enter password' }}">
        </div>
        <div class="password-info">
            <i class="fas fa-info-circle"></i>
            {{ isset($user) ? 'Leave blank to keep current password' : 'Password must be at least 8 characters long' }}
        </div>
        @error('password')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>
    
    <!-- Confirm Password -->
    <div class="form-group">
        <label for="password_confirmation" class="form-label {{ isset($user) ? '' : 'required' }}">Confirm Password</label>
        <div class="input-group">
            <i class="fas fa-lock"></i>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" 
                   placeholder="{{ isset($user) ? 'Confirm new password' : 'Confirm password' }}">
        </div>
        @error('password_confirmation')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>
    
    <!-- User Role -->
    <div class="form-group">
        <label for="role" class="form-label required">User Role</label>
        <div class="input-group">
            <i class="fas fa-user-tag"></i>
            <select id="role" name="role" class="form-select" required>
                <option value="">Select Role</option>
                <option value="user" {{ old('role', $user->role ?? '') == 'user' ? 'selected' : '' }}>User</option>
                <option value="admin" {{ old('role', $user->role ?? '') == 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
        </div>
        @error('role')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>
    
    <!-- Status (Only show for edit) -->
    @if(isset($user))
    <div class="form-group">
        <label for="status" class="form-label required">Status</label>
        <div class="input-group">
            <i class="fas fa-toggle-on"></i>
            <select id="status" name="status" class="form-select" required>
                <option value="">Select Status</option>
                <option value="active" {{ old('status', $user->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $user->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        @error('status')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>
    @endif
</div>

<div class="form-actions">
    <a href="{{ route('users.index') }}" class="btn btn-secondary">
        <i class="fas fa-{{ isset($user) ? 'arrow-left' : 'times' }}"></i>
        {{ isset($user) ? 'Back to Users' : 'Cancel' }}
    </a>
    <button type="submit" class="btn btn-success">
        <i class="fas fa-save"></i>
        {{ isset($user) ? 'Update User' : 'Create User' }}
    </button>
</div>
