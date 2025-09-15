<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Supplies</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <!-- Ionicons CDN -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- jQuery for AJAX -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>

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
        
        .supplies-container {
            background-color: #296218;
            border-radius: 15px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }
        
        .supplies-header {
            background: rgba(255,255,255,0.15);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            backdrop-filter: blur(10px);
        }
        
        .supplies-title {
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
        
        .supplies-table-container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .supplies-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .supplies-table th {
            background: #f8f9fa;
            padding: 15px 12px;
            text-align: left;
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
        }
        
        .supplies-table th a {
            color: #495057;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .supplies-table th a:hover {
            color: #5a8a4e;
        }
        
        .supplies-table td {
            padding: 15px 12px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .supplies-table tr:hover {
            background: #f8f9fa;
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        
        .status-low-stock {
            background: #dc3545; 
            color: #ffffff;
            border: 1px solid #b02a37;
        }
        
        .status-in-stock {
            background: #d4edda;
            color: #155724;
            border: 1px solid #a3d9a5;
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
        
        .pagination {
            display: flex;
            justify-content: center;
            padding: 20px;
            gap: 8px;
        }
        
        .pagination a, .pagination span {
            padding: 10px 14px;
            text-decoration: none;
            border-radius: 8px;
            color: #495057;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .pagination a:hover {
            background: #5a8a4e;
            color: white;
            border-color: #5a8a4e;
        }
        
        .pagination .active span {
            background: #5a8a4e;
            color: white;
            border-color: #5a8a4e;
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
    </style>
</head>
<body>
    <div class="container">
        @include('layouts.core.sidebar')
        <div class="details">
            @include('layouts.core.header')
            
            <div class="supplies-container">
                <!-- Header Section -->
                <div class="supplies-header">
                    <h1 class="supplies-title">
                        <i class="fas fa-boxes"></i>
                        Manage Supplies
                    </h1>
                    
                    <!-- Controls Row -->
                    <div class="controls-row">
                        <div class="search-filter-group">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" placeholder="Search by items or categories" 
                                       value="{{ request('search') }}" id="searchInput">
                            </div>
                            
                            <div class="filter-dropdown">
                                <select id="categoryFilter">
                                    <option value="">All Categories</option>
                                    @foreach($categories ?? [] as $category)
                                        <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                            {{ $category }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="filter-dropdown">
                                <select id="stockFilter">
                                    <option value="">All Items</option>
                                    <option value="1" {{ request('low_stock') == '1' ? 'selected' : '' }}>Low Stock Only</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="action-buttons">
                            <a href="{{ route('supplies.create') }}" class="btn btn-success">
                                <i class="fas fa-plus"></i>
                                Add Items
                            </a>
                            <a href="{{ route('supplies.export') }}" class="btn btn-primary">
                                <i class="fas fa-download"></i>
                                Export
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
                <div class="supplies-table-container">
                    @if($supplies->count() > 0)
                        <table class="supplies-table">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="selectAll">
                                    </th>
                                    <th>
                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'name', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}">
                                            Items
                                            @if(request('sort_by') == 'name')
                                                <i class="fas fa-sort-{{ request('sort_direction') == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'quantity', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}">
                                            Quantity
                                            @if(request('sort_by') == 'quantity')
                                                <i class="fas fa-sort-{{ request('sort_direction') == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'purchase_date', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}">
                                            Date
                                            @if(request('sort_by') == 'purchase_date')
                                                <i class="fas fa-sort-{{ request('sort_direction') == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'category', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}">
                                            Categories
                                            @if(request('sort_by') == 'category')
                                                <i class="fas fa-sort-{{ request('sort_direction') == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($supplies as $supply)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="supply-checkbox" value="{{ $supply->id }}">
                                        </td>
                                        <td>
                                            <div style="font-weight: 600; margin-bottom: 4px;">{{ $supply->name }}</div>
                                            @if($supply->description)
                                                <div style="font-size: 12px; color: #6c757d;">{{ Str::limit($supply->description, 50) }}</div>
                                            @endif
                                            @if($supply->supplier)
                                                <div style="font-size: 11px; color: #007bff;">{{ $supply->supplier }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            <div style="font-weight: 600;">{{ number_format($supply->quantity) }} {{ $supply->unit }}</div>
                                            <div style="font-size: 12px; color: #6c757d;">₱{{ number_format($supply->unit_price, 2) }} each</div>
                                            @if($supply->isLowStock())
                                                <span class="status-badge status-low-stock">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                    Low Stock
                                                </span>
                                            @else
                                                <span class="status-badge status-in-stock">
                                                    <i class="fas fa-check"></i>
                                                    In Stock
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $supply->formatted_purchase_date }}
                                        </td>
                                        <td>
                                            @if($supply->category)
                                                <span class="status-badge" style="background: #e3f2fd; color: #1565c0;">
                                                    {{ $supply->category }}
                                                </span>
                                            @else
                                                <span style="color: #6c757d;">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="action-buttons-cell">
                                                <a href="{{ route('supplies.show', $supply) }}" class="btn btn-primary btn-sm" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('supplies.edit', $supply) }}" class="btn btn-warning btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" action="{{ route('supplies.destroy', $supply) }}" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Delete" 
                                                            onclick="return confirm('Are you sure you want to delete this item?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        <!-- Pagination -->
                        @if($supplies->hasPages())
                            <div class="pagination">
                                {{ $supplies->appends(request()->query())->links() }}
                            </div>
                        @endif
                    @else
                        <div class="empty-state">
                            <i class="fas fa-boxes"></i>
                            <h3>No supplies found</h3>
                            <p>Get started by adding your first supply item.</p>
                            <a href="{{ route('supplies.create') }}" class="btn btn-success">
                                <i class="fas fa-plus"></i>
                                Add Your First Item
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
            $('#searchInput').on('input', function() {
                clearTimeout(searchTimeout);
                const searchTerm = $(this).val();
                
                searchTimeout = setTimeout(() => {
                    const url = new URL(window.location.href);
                    if (searchTerm) {
                        url.searchParams.set('search', searchTerm);
                    } else {
                        url.searchParams.delete('search');
                    }
                    window.location.href = url.toString();
                }, 500);
            });

            // Category filter
            $('#categoryFilter').on('change', function() {
                const category = $(this).val();
                const url = new URL(window.location.href);
                if (category) {
                    url.searchParams.set('category', category);
                } else {
                    url.searchParams.delete('category');
                }
                window.location.href = url.toString();
            });

            // Stock filter
            $('#stockFilter').on('change', function() {
                const lowStock = $(this).val();
                const url = new URL(window.location.href);
                if (lowStock) {
                    url.searchParams.set('low_stock', lowStock);
                } else {
                    url.searchParams.delete('low_stock');
                }
                window.location.href = url.toString();
            });

            // Select all functionality
            $('#selectAll').on('change', function() {
                $('.supply-checkbox').prop('checked', $(this).prop('checked'));
            });

            // Individual checkbox handling
            $('.supply-checkbox').on('change', function() {
                const totalCheckboxes = $('.supply-checkbox').length;
                const checkedCheckboxes = $('.supply-checkbox:checked').length;
                
                $('#selectAll').prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes);
                $('#selectAll').prop('checked', checkedCheckboxes === totalCheckboxes);
            });
        });
    </script>
</body>
</html> 