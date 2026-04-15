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
                <option value="user"      {{ old('role', $user->role ?? '') == 'user'      ? 'selected' : '' }}>User</option>
                <option value="admin"     {{ old('role', $user->role ?? '') == 'admin'     ? 'selected' : '' }}>Admin</option>
                <option value="requestor" {{ old('role', $user->role ?? '') == 'requestor' ? 'selected' : '' }}>Requestor</option>
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
                <option value="active"   {{ old('status', $user->status ?? 'active') == 'active'   ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $user->status ?? '')       == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        @error('status')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>
    @endif

    <!-- ── Permissions for regular "user" role ── -->
    <div class="form-group full-width" id="user-permissions-section" style="display:none;">
        <div class="permissions-header">
            <h4><i class="fas fa-shield-alt"></i> User Permissions</h4>
            <p>Select which actions this user is allowed to perform.</p>
        </div>
        <div class="permissions-grid">
            <div class="permission-card">
                <input type="checkbox" id="can_create" name="can_create" value="1"
                       {{ old('can_create', $user->can_create ?? false) ? 'checked' : '' }}>
                <label for="can_create">
                    <strong>Create</strong>
                    <span>Add new records</span>
                </label>
            </div>

            <div class="permission-card">
                <input type="checkbox" id="can_read" name="can_read" value="1"
                       {{ old('can_read', $user->can_read ?? true) ? 'checked' : '' }}>
                <label for="can_read">
                    <strong>Read</strong>
                    <span>View records</span>
                </label>
            </div>

            <div class="permission-card">
                <input type="checkbox" id="can_update" name="can_update" value="1"
                       {{ old('can_update', $user->can_update ?? false) ? 'checked' : '' }}>
                <label for="can_update">
                    <strong>Update</strong>
                    <span>Modify records</span>
                </label>
            </div>

            <div class="permission-card">
                <input type="checkbox" id="can_delete" name="can_delete" value="1"
                       {{ old('can_delete', $user->can_delete ?? false) ? 'checked' : '' }}>
                <label for="can_delete">
                    <strong>Delete</strong>
                    <span>Remove records</span>
                </label>
            </div>

            <div class="permission-card">
                <input type="checkbox" id="can_stock_in" name="can_stock_in" value="1"
                       {{ old('can_stock_in', $user->can_stock_in ?? false) ? 'checked' : '' }}>
                <label for="can_stock_in">
                    <strong>Stock In</strong>
                    <span>Add inventory</span>
                </label>
            </div>

            <div class="permission-card">
                <input type="checkbox" id="can_stock_out" name="can_stock_out" value="1"
                       {{ old('can_stock_out', $user->can_stock_out ?? false) ? 'checked' : '' }}>
                <label for="can_stock_out">
                    <strong>Stock Out</strong>
                    <span>Remove inventory</span>
                </label>
            </div>
        </div>
    </div>

    <!-- ── Permission badge for "requestor" role (read-only info card) ── -->
    <div class="form-group full-width" id="requestor-permissions-section" style="display:none;">
        <div class="permissions-header">
            <h4><i class="fas fa-file-alt"></i> Requestor Permissions</h4>
            <p>Requestors automatically receive the <strong>Request</strong> permission only. All other permissions are disabled.</p>
        </div>
        <div class="permissions-grid">
            <div class="permission-card permission-card--active">
                {{-- Hidden input so the value is always submitted for requestors --}}
                <input type="checkbox" id="can_request" name="can_request" value="1" checked disabled>
                <label for="can_request">
                    <strong>Request</strong>
                    <span>Submit supply requests (RIS)</span>
                </label>
            </div>
            <div class="permission-card permission-card--locked">
                <input type="checkbox" disabled>
                <label><strong>Create</strong><span>Disabled for Requestors</span></label>
            </div>
            <div class="permission-card permission-card--locked">
                <input type="checkbox" disabled>
                <label><strong>Read</strong><span>Disabled for Requestors</span></label>
            </div>
            <div class="permission-card permission-card--locked">
                <input type="checkbox" disabled>
                <label><strong>Update</strong><span>Disabled for Requestors</span></label>
            </div>
            <div class="permission-card permission-card--locked">
                <input type="checkbox" disabled>
                <label><strong>Delete</strong><span>Disabled for Requestors</span></label>
            </div>
            <div class="permission-card permission-card--locked">
                <input type="checkbox" disabled>
                <label><strong>Stock In / Out</strong><span>Disabled for Requestors</span></label>
            </div>
        </div>
    </div>

    <!-- ── Info badge for "admin" role ── -->
    <div class="form-group full-width" id="admin-permissions-section" style="display:none;">
        <div class="permissions-header">
            <h4><i class="fas fa-crown"></i> Admin Permissions</h4>
            <p>Admins automatically receive <strong>all permissions</strong>. No manual assignment needed.</p>
        </div>
        <div class="permissions-grid">
            @foreach(['Create','Read','Update','Delete','Stock In','Stock Out'] as $perm)
            <div class="permission-card permission-card--active">
                <input type="checkbox" checked disabled>
                <label><strong>{{ $perm }}</strong><span>Granted automatically</span></label>
            </div>
            @endforeach
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
.full-width { grid-column: 1 / -1; }

.permissions-header {
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #e9ecef;
}
.permissions-header h4 {
    color: #296218;
    font-size: 18px;
    font-weight: 600;
    margin: 0 0 5px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}
.permissions-header p { color: #6c757d; font-size: 13px; margin: 0; }

.permissions-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
}

.permission-card {
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 15px;
    transition: all 0.3s ease;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    cursor: pointer;
}
.permission-card:hover { border-color: #296218; box-shadow: 0 4px 12px rgba(41,98,24,.1); transform: translateY(-2px); }

/* Active (auto-checked) card */
.permission-card--active {
    border-color: #296218 !important;
    background: #f0faf0 !important;
}
.permission-card--active label strong { color: #296218 !important; }

/* Locked (disabled) card */
.permission-card--locked {
    opacity: .45;
    pointer-events: none;
    background: #f8f9fa;
}

.permission-card input[type="checkbox"] {
    width: 18px; height: 18px;
    cursor: pointer; flex-shrink: 0;
    margin-top: 5px;
    accent-color: #296218;
}
.permission-card label {
    display: flex; flex-direction: column;
    cursor: pointer; width: 100%; margin: 0; gap: 8px;
}
.permission-card label strong { color: #333; font-size: 14px; font-weight: 600; }
.permission-card label span   { color: #6c757d; font-size: 12px; }

@media (max-width: 1200px) { .permissions-grid { grid-template-columns: repeat(2,1fr); } }
@media (max-width: 768px)  { .permissions-grid { grid-template-columns: 1fr; } }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const roleSelect          = document.getElementById('role');
    const userSection         = document.getElementById('user-permissions-section');
    const requestorSection    = document.getElementById('requestor-permissions-section');
    const adminSection        = document.getElementById('admin-permissions-section');

    function updateSections() {
        const role = roleSelect.value;
        userSection.style.display      = role === 'user'      ? 'block' : 'none';
        requestorSection.style.display = role === 'requestor' ? 'block' : 'none';
        adminSection.style.display     = role === 'admin'     ? 'block' : 'none';
    }

    updateSections();
    roleSelect.addEventListener('change', updateSections);
});
</script>