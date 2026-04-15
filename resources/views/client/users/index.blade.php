<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ATI-RTC1 - Manage Users</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/users.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        @include('layouts.core.sidebar')
        <div class="details">
            @include('layouts.core.header')

            <div class="users-container">
                <!-- Header Section -->
                <div class="users-header">
                    <h1 class="users-title">
                        <i class="fas fa-users"></i>
                        Manage Users
                    </h1>

                    <!-- Controls Row -->
                    <div class="controls-row">
                        <div class="search-filter-group">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" placeholder="Search by name or email" id="userSearch">
                            </div>

                            <div class="filter-dropdown">
                                <select id="roleFilter">
                                    <option value="">All Roles</option>
                                    <option value="admin">Admin</option>
                                    <option value="user">User</option>
                                    <option value="requestor">Requestor</option>
                                </select>
                            </div>
                        </div>

                        <div class="action-buttons">
                            <a href="{{ route('users.orgchart') }}" class="btn btn-secondary">
                                <i class="fas fa-sitemap"></i>
                                Org Chart
                            </a>
                            <a href="{{ url('client/users/create') }}" class="btn btn-success">
                                <i class="fas fa-plus"></i>
                                Add User
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Alerts -->
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        {{ session('warning') }}
                    </div>
                @endif

                <!-- Table Section -->
                <div class="users-table-container">
                    @if(count($user ?? []) > 0)
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 5%; text-align: center;">
                                        <i class="fas fa-hashtag"></i>
                                    </th>
                                    <th style="width: 20%;">
                                        <i class="fas fa-user" style="margin-right: 5px;"></i>User
                                    </th>
                                    <th style="width: 20%;">
                                        <i class="fas fa-envelope" style="margin-right: 5px;"></i>Email
                                    </th>
                                    <th style="width: 10%; text-align: center;">
                                        <i class="fas fa-user-tag" style="margin-right: 5px;"></i>Role
                                    </th>
                                    <th style="width: 30%; text-align: center;">
                                        <i class="fas fa-shield-alt" style="margin-right: 5px;"></i>Permissions
                                    </th>
                                    <th style="width: 15%; text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user as $key => $userItem)
                                    <tr class="user-row" data-role="{{ strtolower($userItem->role ?? 'user') }}">
                                        <td style="text-align: center; font-weight: 600; color: #495057;">
                                            {{ $key + 1 }}
                                        </td>
                                        <td>
                                            <div style="display: flex; align-items: center;">
                                                <div style="width: 32px; height: 32px; background: #296218; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 10px; flex-shrink: 0;">
                                                    <i class="fas fa-user" style="color: white; font-size: 14px;"></i>
                                                </div>
                                                <div style="min-width: 0; flex: 1;">
                                                    <div class="user-name" style="font-weight: 600; color: #333; font-size: 14px; margin-bottom: 2px;">{{ $userItem->name }}</div>
                                                    <div style="font-size: 11px; color: #6c757d;">
                                                        <i class="fas fa-calendar-alt" style="margin-right: 4px;"></i>
                                                        {{ $userItem->created_date }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="user-email" style="color: #495057;">
                                            <div style="display: flex; align-items: center;">
                                                <i class="fas fa-envelope" style="color: #666; margin-right: 8px;"></i>
                                                {{ $userItem->email }}
                                            </div>
                                        </td>

                                        {{-- ── Role Badge ── --}}
                                        <td style="text-align: center;">
                                            @php $role = $userItem->role ?? 'user'; @endphp
                                            @if($role === 'admin')
                                                <span class="role-badge role-admin">
                                                    <i class="fas fa-user-shield"></i> Admin
                                                </span>
                                            @elseif($role === 'requestor')
                                                <span class="role-badge role-requestor">
                                                    <i class="fas fa-file-alt"></i> Requestor
                                                </span>
                                            @else
                                                <span class="role-badge role-user">
                                                    <i class="fas fa-user"></i> User
                                                </span>
                                            @endif
                                        </td>

                                        {{-- ── Permissions Column ── --}}
                                        <td style="text-align: center;">
                                            @if($role === 'admin')
                                                <span class="permission-badge permission-full">
                                                    <i class="fas fa-crown"></i>
                                                    Full Access
                                                </span>
                                            @elseif($role === 'requestor')
                                                <span class="permission-badge permission-requestor">
                                                    <i class="fas fa-paper-plane"></i>
                                                    Request Only
                                                </span>
                                            @else
                                                <div class="permissions-display" style="justify-content: center;">
                                                    <span class="perm-badge {{ $userItem->can_create  ? 'perm-create'  : 'perm-disabled' }}"
                                                          title="{{ $userItem->can_create  ? 'Can Create'   : 'Cannot Create' }}">C</span>
                                                    <span class="perm-badge {{ $userItem->can_read    ? 'perm-read'    : 'perm-disabled' }}"
                                                          title="{{ $userItem->can_read    ? 'Can Read'     : 'Cannot Read' }}">R</span>
                                                    <span class="perm-badge {{ $userItem->can_update  ? 'perm-update'  : 'perm-disabled' }}"
                                                          title="{{ $userItem->can_update  ? 'Can Update'   : 'Cannot Update' }}">U</span>
                                                    <span class="perm-badge {{ $userItem->can_delete  ? 'perm-delete'  : 'perm-disabled' }}"
                                                          title="{{ $userItem->can_delete  ? 'Can Delete'   : 'Cannot Delete' }}">D</span>
                                                </div>
                                            @endif
                                        </td>

                                        <td style="text-align: center;">
                                            <div class="action-buttons-cell">
                                                <button onclick="viewUserEquipment({{ $userItem->id }}, '{{ addslashes($userItem->name) }}')"
                                                        class="btn btn-info btn-sm" title="View Equipment">
                                                    <i class="fas fa-tools"></i>
                                                </button>
                                                <a href="{{ url('client/users', $userItem->id) }}/edit"
                                                   class="btn btn-warning btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($userItem->id !== auth()->user()->id)
                                                    <button onclick="removeUser({{ $userItem->id }})"
                                                            class="btn btn-danger btn-sm" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @else
                                                    <span class="btn btn-sm"
                                                          style="background: #6c757d; color: white; cursor: not-allowed;"
                                                          title="Cannot delete your own account">
                                                        <i class="fas fa-lock"></i>
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-users-slash"></i>
                            <h3>No users found</h3>
                            <p>Get started by adding your first user to the system.</p>
                            <a href="{{ url('client/users/create') }}" class="btn btn-success">
                                <i class="fas fa-plus"></i>
                                Add Your First User
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Role-badge extra styles --}}
    <style>
        /* Requestor role badge */
        /* .role-requestor {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffc107;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        } */
/* 
        /* "Request Only" permission badge */
        /* .permission-requestor {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffc107;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        } */ */

        .btn-info { background: #0d6efd; color: #fff; }
        .btn-info:hover { background: #0b5ed7; }

        /* ── Equipment Modal ── */
        .eq-modal-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.55);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }
        .eq-modal-card {
            background: #fff;
            border-radius: 16px;
            width: 100%;
            max-width: 780px;
            max-height: 85vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 20px 60px rgba(0,0,0,0.25);
            animation: eqModalIn 0.2s ease;
            overflow: hidden;
        }
        @keyframes eqModalIn {
            from { transform: translateY(20px); opacity: 0; }
            to   { transform: translateY(0);    opacity: 1; }
        }
        .eq-modal-header {
            background: #296218;
            color: #fff;
            padding: 18px 22px;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-shrink: 0;
        }
        .eq-modal-header h3 { margin: 0; font-size: 17px; font-weight: 700; display: flex; align-items: center; gap: 8px; }
        .eq-modal-close {
            background: transparent; border: none;
            color: rgba(255,255,255,0.8); font-size: 24px;
            cursor: pointer; line-height: 1; padding: 0; margin-left: 16px;
        }
        .eq-modal-close:hover { color: #fff; }
        .eq-modal-body { padding: 20px; overflow-y: auto; flex: 1; }
        .eq-loading { text-align: center; padding: 40px; color: #6c757d; font-size: 15px; }
        .eq-empty { text-align: center; padding: 40px; color: #6c757d; }
        .eq-empty i { font-size: 48px; opacity: 0.3; margin-bottom: 12px; display: block; }
        .eq-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .eq-table thead th {
            background: #f8f9fa; padding: 10px 12px;
            text-align: left; font-weight: 700; color: #495057;
            border-bottom: 2px solid #dee2e6; font-size: 12px;
            text-transform: uppercase; letter-spacing: 0.4px;
        }
        .eq-table tbody td { padding: 10px 12px; border-bottom: 1px solid #f0f0f0; color: #333; vertical-align: middle; }
        .eq-table tbody tr:hover { background: #f8f9fa; }
        .eq-badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; }
        .eq-badge-serviceable   { background: #d4edda; color: #155724; }
        .eq-badge-unserviceable { background: #f8d7da; color: #721c24; }
        .eq-summary-bar { display: flex; gap: 12px; margin-bottom: 16px; flex-wrap: wrap; }
        .eq-summary-chip {
            background: #f8f9fa; border: 1px solid #dee2e6;
            border-radius: 8px; padding: 8px 14px;
            font-size: 13px; display: flex; align-items: center; gap: 7px;
        }
        .eq-summary-chip strong { color: #296218; font-size: 18px; font-weight: 700; }
    </style>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            let searchTimeout;
            $('#userSearch').on('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => { filterTable(); }, 300);
            });

            $('#roleFilter').on('change', function() { filterTable(); });

            function filterTable() {
                const searchTerm = $('#userSearch').val().toLowerCase().trim();
                const roleFilter = $('#roleFilter').val().toLowerCase().trim();

                $('.user-row').each(function() {
                    const row      = $(this);
                    const name     = row.find('.user-name').text().toLowerCase().trim();
                    const email    = row.find('.user-email').text().toLowerCase().trim();
                    const userRole = row.data('role');

                    let showRow = true;

                    if (searchTerm && !name.includes(searchTerm) && !email.includes(searchTerm)) {
                        showRow = false;
                    }
                    if (roleFilter && userRole !== roleFilter) {
                        showRow = false;
                    }

                    showRow ? row.show() : row.hide();
                });

                updateNoResultsMessage();
            }

            function updateNoResultsMessage() {
                const visibleRows     = $('.user-row:visible').length;
                const tableContainer  = $('.users-table-container');

                $('.no-results-message').remove();

                if (visibleRows === 0 && $('.user-row').length > 0) {
                    tableContainer.append(`
                        <div class="no-results-message" style="text-align:center;padding:40px;color:#6c757d;">
                            <i class="fas fa-search" style="font-size:48px;margin-bottom:16px;opacity:0.5;"></i>
                            <h3>No users match your search</h3>
                            <p>Try adjusting your search terms or filters.</p>
                        </div>`);
                    $('.users-table').hide();
                } else {
                    $('.users-table').show();
                }
            }
        });

        function removeUser(id) {
            if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                $.ajax({
                    type: "DELETE",
                    url: "{{ url('client/users') }}/" + id,
                    dataType: "json",
                    beforeSend: function() {
                        $('body').append('<div class="loading-overlay" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.7);z-index:9999;display:flex;align-items:center;justify-content:center;color:white;font-size:18px;"><i class="fas fa-spinner fa-spin" style="margin-right:10px;"></i> Deleting...</div>');
                    },
                    success: function() { $('.loading-overlay').remove(); location.reload(); },
                    error:   function() { $('.loading-overlay').remove(); alert('Error deleting user. Please try again.'); }
                });
            }
        }
    </script>

    @include('layouts.core.footer')

    {{-- ── User Equipment Modal ── --}}
    <div id="equipmentModal" class="eq-modal-overlay" style="display:none;">
        <div class="eq-modal-card">
            <div class="eq-modal-header">
                <div>
                    <h3 id="equipmentModalTitle"><i class="fas fa-tools"></i> Equipment</h3>
                    <p id="equipmentModalSubtitle" style="margin:4px 0 0;font-size:13px;opacity:0.85;"></p>
                </div>
                <button class="eq-modal-close" id="equipmentModalClose">&times;</button>
            </div>
            <div class="eq-modal-body" id="equipmentModalBody">
                <div class="eq-loading"><i class="fas fa-spinner fa-spin"></i> Loading equipment...</div>
            </div>
        </div>
    </div>

    <script>
        function viewUserEquipment(userId, userName) {
            $('#equipmentModalTitle').html('<i class="fas fa-tools"></i> Assigned Equipment');
            $('#equipmentModalSubtitle').text('Responsible person: ' + userName);
            $('#equipmentModalBody').html('<div class="eq-loading"><i class="fas fa-spinner fa-spin"></i> Loading equipment...</div>');
            $('#equipmentModal').fadeIn(200);

            $.ajax({
                url: '{{ url("client/users") }}/' + userId + '/equipment',
                method: 'GET',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(data) {
                    if (!data.equipment || data.equipment.length === 0) {
                        $('#equipmentModalBody').html(`
                            <div class="eq-empty">
                                <i class="fas fa-box-open"></i>
                                <p style="font-size:15px;font-weight:600;margin:0 0 6px;">${userName} has no assigned equipment.</p>
                            </div>`);
                        return;
                    }

                    const total       = data.equipment.length;
                    const serviceable = data.equipment.filter(e => e.condition === 'Serviceable').length;
                    const unserv      = total - serviceable;

                    let html = `
                        <div class="eq-summary-bar">
                            <div class="eq-summary-chip"><strong>${total}</strong> Total</div>
                            <div class="eq-summary-chip"><strong style="color:#28a745;">${serviceable}</strong> Serviceable</div>
                            <div class="eq-summary-chip"><strong style="color:#dc3545;">${unserv}</strong> Unserviceable</div>
                        </div>
                        <table class="eq-table">
                            <thead>
                                <tr>
                                    <th>#</th><th>Property No.</th><th>Article</th>
                                    <th>Classification</th><th>Condition</th>
                                    <th>Location</th><th>Unit Value</th>
                                </tr>
                            </thead><tbody>`;

                    data.equipment.forEach((eq, i) => {
                        const cond = eq.condition === 'Serviceable'
                            ? '<span class="eq-badge eq-badge-serviceable">Serviceable</span>'
                            : '<span class="eq-badge eq-badge-unserviceable">Unserviceable</span>';
                        html += `<tr>
                            <td>${i + 1}</td>
                            <td><strong>${eq.property_number}</strong></td>
                            <td>${eq.article}</td>
                            <td>${eq.classification || '—'}</td>
                            <td>${cond}</td>
                            <td>${eq.responsibility_center || '—'}</td>
                            <td>₱${parseFloat(eq.unit_value).toLocaleString('en-PH', {minimumFractionDigits:2})}</td>
                        </tr>`;
                    });

                    html += '</tbody></table>';
                    $('#equipmentModalBody').html(html);
                },
                error: function() {
                    $('#equipmentModalBody').html('<div class="eq-empty"><i class="fas fa-exclamation-triangle"></i><p>Failed to load equipment.</p></div>');
                }
            });
        }

        $('#equipmentModalClose').on('click', function() { $('#equipmentModal').fadeOut(200); });
        $(document).on('click', '.eq-modal-overlay', function(e) {
            if ($(e.target).hasClass('eq-modal-overlay')) $('#equipmentModal').fadeOut(200);
        });
    </script>
</body>
</html>