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

    <!-- Permissions Grid -->
    <div class="form-group full-width">
        <div class="permissions-grid">
            <!-- Create Permission -->
            <div class="permission-card">
                <input type="checkbox" id="can_create" name="can_create" value="1" 
                       {{ old('can_create', $user->can_create ?? false) ? 'checked' : '' }}>
                <label for="can_create">
                    <strong>Create</strong>
                    <span>Add records</span>
                </label>
            </div>
            
            <!-- Read Permission -->
            <div class="permission-card">
                <input type="checkbox" id="can_read" name="can_read" value="1" 
                       {{ old('can_read', $user->can_read ?? true) ? 'checked' : '' }}>
                <label for="can_read">
                    <strong>Read</strong>
                    <span>View records</span>
                </label>
            </div>
            
            <!-- Update Permission -->
            <div class="permission-card">
                <input type="checkbox" id="can_update" name="can_update" value="1" 
                       {{ old('can_update', $user->can_update ?? false) ? 'checked' : '' }}>
                <label for="can_update">
                    <strong>Update</strong>
                    <span>Modify records</span>
                </label>
            </div>
            
            <!-- Delete Permission -->
            <div class="permission-card">
                <input type="checkbox" id="can_delete" name="can_delete" value="1" 
                       {{ old('can_delete', $user->can_delete ?? false) ? 'checked' : '' }}>
                <label for="can_delete">
                    <strong>Delete</strong>
                    <span>Remove records</span>
                </label>
            </div>
        </div>
    </div>
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

<style>
.full-width {
    grid-column: 1 / -1;
}

.permissions-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
}

.permission-card {
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 6px;
    padding: 10px;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
    min-height: 60px;
}

.permission-card:hover {
    border-color: #296218;
    box-shadow: 0 2px 8px rgba(41, 98, 24, 0.1);
}

.permission-card input[type="checkbox"] {
    width: 16px;
    height: 16px;
    cursor: pointer;
    flex-shrink: 0;
}

.permission-card label {
    display: flex;
    flex-direction: column;
    cursor: pointer;
    width: 100%;
    margin: 0;
    gap: 2px;
}

.permission-card label strong {
    color: #333;
    font-size: 13px;
    font-weight: 600;
}

.permission-card label span {
    color: #6c757d;
    font-size: 11px;
}

.permission-card input[type="checkbox"]:checked + label strong {
    color: #296218;
}

.permission-card.disabled {
    opacity: 0.5;
    pointer-events: none;
}

@media (max-width: 1200px) {
    .permissions-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .permissions-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const permissionsSection = document.querySelector('.permissions-grid');
    const permissionCheckboxes = [
        document.getElementById('can_create'),
        document.getElementById('can_read'),
        document.getElementById('can_update'),
        document.getElementById('can_delete')
    ];
    
    function updatePermissionsVisibility() {
        const selectedRole = roleSelect.value;
        
        if (selectedRole === 'admin') {
            permissionsSection.parentElement.style.display = 'none';
            // Ensure all permissions are checked for admin
            permissionCheckboxes.forEach(cb => {
                if (cb) cb.checked = true;
            });
        } else if (selectedRole === 'user') {
            permissionsSection.parentElement.style.display = 'block';
        } else {
            permissionsSection.parentElement.style.display = 'none';
        }
    }
    
    // Initial check
    updatePermissionsVisibility();
    
    // Listen for role changes
    roleSelect.addEventListener('change', updatePermissionsVisibility);
});
</script>