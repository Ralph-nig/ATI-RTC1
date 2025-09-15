<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AGRISUPPLY - Manage Users</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
        .users-container {
            background-color: #296218;
            border-radius: 15px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            max-width: 100%; 
            box-sizing: border-box; 
        }
        
        .details {
            position: relative;
            width: calc(100% - 300px) !important;
            left: 300px !important;
            min-height: 100vh;
            background: #f5f5f5;
            transition: 0.5s;
            padding: 20px;
            font-family: 'Inter', sans-serif;
            box-sizing: border-box;
        }
                
        .users-header {
            background: rgba(255,255,255,0.15);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            backdrop-filter: blur(10px);
        }
        
        .users-title {
            color: white;
            font-size: 24px;
            font-weight: bold;
            margin: 0 0 15px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .controls-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .search-filter-group {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: center;
        }
        
        .search-box {
            position: relative;
        }
        
        .search-box input {
            padding: 10px 15px 10px 40px;
            border: none;
            border-radius: 25px;
            background: rgba(255,255,255,0.95);
            width: 250px;
            font-size: 14px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }
        
        .filter-dropdown select {
            padding: 10px 15px;
            border: none;
            border-radius: 8px;
            background: rgba(255,255,255,0.95);
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            min-width: 140px;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        
        .btn-primary {
            background: #1e88e5;
            color: white;
        }
        
        .btn-success {
            background: #43a047;
            color: white;
        }
        
        .btn-warning {
            background: #fb8c00;
            color: white;
        }
        
        .btn-danger {
            background: #e53935;
            color: white;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.2);
        }
        
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: rgba(255,255,255,0.2);
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
        }
        
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: white;
            margin-bottom: 8px;
        }
        
        .stat-label {
            color: rgba(255,255,255,0.9);
            font-size: 14px;
            font-weight: 500;
        }
        
        .users-table-container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .users-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .users-table th {
            background: #f8f9fa;
            padding: 15px 12px;
            text-align: left;
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
        }
        
        .users-table th a {
            color: #495057;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .users-table th a:hover {
            color: #5a8a4e;
        }
        
        .users-table td {
            padding: 15px 12px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .users-table tr:hover {
            background: #f8f9fa;
        }
        
        .user-details {
            min-width: 0;
            flex: 1;
        }
        
        .user-name {
            font-weight: 600;
            color: #333;
            font-size: 15px;
            margin-bottom: 4px;
        }
        
        .user-date {
            font-size: 12px;
            color: #6c757d;
        }
        
        .role-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            gap: 4px;
        }
        
        .role-admin {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .role-user {
            background: #d4edda;
            color: #155724;
            border: 1px solid #a3d9a5;
        }
        
        .permission-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            gap: 4px;
        }
        
        .permission-full {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .permission-limited {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .action-buttons-cell {
            display: flex;
            gap: 8px;
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .alert {
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #a3d9a5;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.4;
            color: #5a8a4e;
        }
        
        .empty-state h3 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #495057;
        }
        
        .empty-state p {
            font-size: 16px;
            margin-bottom: 25px;
        }
        
        @media (max-width: 768px) {
            .controls-row {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-box input {
                width: 100%;
            }
            
            .action-buttons {
                justify-content: center;
            }
            
            .users-table th,
            .users-table td {
                padding: 10px 8px;
                font-size: 13px;
            }
            
            .user-name {
                font-size: 14px;
            }
            
            .action-buttons-cell {
                flex-direction: column;
                gap: 4px;
            }
        }
    </style>
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
                                <input type="text" placeholder="Search by name or email" 
                                       id="userSearch">
                            </div>
                            
                            <div class="filter-dropdown">
                                <select id="roleFilter">
                                    <option value="">All Roles</option>
                                    <option value="admin">Admin</option>
                                    <option value="user">Employees</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="action-buttons">
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
                
                <!-- Table Section -->
                <div class="users-table-container">
                    @if(count($user ?? []) > 0)
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 5%; text-align: center;">
                                        <i class="fas fa-hashtag"></i>
                                    </th>
                                    <th style="width: 25%;">
                                        <i class="fas fa-user" style="margin-right: 5px;"></i>User
                                    </th>
                                    <th style="width: 25%;">
                                        <i class="fas fa-envelope" style="margin-right: 5px;"></i>Email
                                    </th>
                                    <th style="width: 15%; text-align: center;">
                                        <i class="fas fa-user-tag" style="margin-right: 5px;"></i>Role
                                    </th>
                                    <th style="width: 15%; text-align: center;">
                                        <i class="fas fa-shield-alt" style="margin-right: 5px;"></i>Permissions
                                    </th>
                                    <th style="width: 15%; text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user as $key => $userItem)
                                    <tr class="user-row">
                                        <td style="text-align: center; font-weight: 600; color: #495057;">
                                            {{ $key + 1 }}
                                        </td>
                                        <td>
                                            <div style="display: flex; align-items: center;">
                                                <div style="width: 32px; height: 32px; background: #296218; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 10px; flex-shrink: 0;">
                                                    <i class="fas fa-user" style="color: white; font-size: 14px;"></i>
                                                </div>
                                                <div style="min-width: 0; flex: 1;">
                                                    <div style="font-weight: 600; color: #333; font-size: 14px; margin-bottom: 2px;">{{ $userItem->name }}</div>
                                                    <div style="font-size: 11px; color: #6c757d;">
                                                        <i class="fas fa-calendar-alt" style="margin-right: 4px;"></i>
                                                        {{ $userItem->created_date }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="color: #495057;">
                                            <div style="display: flex; align-items: center;">
                                                <i class="fas fa-envelope" style="color: #666; margin-right: 8px;"></i>
                                                {{ $userItem->email }}
                                            </div>
                                        </td>
                                        <td style="text-align: center;">
                                            <span class="role-badge {{ ($userItem->role ?? 'user') == 'admin' ? 'role-admin' : 'role-user' }}">
                                                <i class="fas fa-{{ ($userItem->role ?? 'user') == 'admin' ? 'user-shield' : 'user' }}"></i>
                                                {{ ucfirst($userItem->role ?? 'employee') }}
                                            </span>
                                        </td>
                                        <td style="text-align: center;">
                                            @if(($userItem->role ?? 'user') == 'admin')
                                                <span class="permission-badge permission-full">
                                                    <i class="fas fa-crown"></i>
                                                    Full Access
                                                </span>
                                            @else
                                                <span class="permission-badge permission-limited">
                                                    <i class="fas fa-lock"></i>
                                                    Limited
                                                </span>
                                            @endif
                                        </td>
                                        <td style="text-align: center;">
                                            <div class="action-buttons-cell">
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

    <script>
        $(document).ready(function() {
            // Setup CSRF token for AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Search functionality
            let searchTimeout;
            $('#userSearch').on('input', function() {
                clearTimeout(searchTimeout);
                const searchTerm = $(this).val().toLowerCase();
                
                searchTimeout = setTimeout(() => {
                    filterTable();
                }, 300);
            });

            // Role filter
            $('#roleFilter').on('change', function() {
                filterTable();
            });

            // Permission filter
            $('#permissionFilter').on('change', function() {
                filterTable();
            });

            function filterTable() {
                const searchTerm = $('#userSearch').val().toLowerCase();
                const roleFilter = $('#roleFilter').val().toLowerCase();
                const permissionFilter = $('#permissionFilter').val().toLowerCase();
                
                $('.user-row').each(function() {
                    const row = $(this);
                    const name = row.find('.user-name').text().toLowerCase();
                    const email = row.find('td:eq(2)').text().toLowerCase();
                    const role = row.find('.role-badge').text().toLowerCase();
                    const permission = row.find('.permission-badge').text().toLowerCase();
                    
                    let showRow = true;
                    
                    // Search filter
                    if (searchTerm && !name.includes(searchTerm) && !email.includes(searchTerm)) {
                        showRow = false;
                    }
                    
                    // Role filter
                    if (roleFilter && !role.includes(roleFilter)) {
                        showRow = false;
                    }
                    
                    // Permission filter
                    if (permissionFilter && !permission.includes(permissionFilter)) {
                        showRow = false;
                    }
                    
                    row.toggle(showRow);
                });
            }
        });

        function removeUser(id) {
            if(confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                $.ajax({
                    type: "DELETE",
                    url: "{{ url('client/users') }}/" + id,
                    dataType: "json",
                    beforeSend: function() {
                        $('body').append('<div class="loading-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 9999; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;"><i class="fas fa-spinner fa-spin" style="margin-right: 10px;"></i> Deleting...</div>');
                    },
                    success: function(response) {
                        $('.loading-overlay').remove();
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        $('.loading-overlay').remove();
                        alert('Error deleting user. Please try again.');
                        console.error('Error:', error);
                    }
                });
            }
        }
    </script>
</body>
</html>