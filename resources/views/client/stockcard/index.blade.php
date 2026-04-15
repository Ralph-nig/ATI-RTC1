<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Card</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/supplies.css') }}">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="container">
        @include('layouts.core.sidebar')
        <div class="details">
            @include('layouts.core.header')
            @include('layouts.core.footer')
            
            <div class="supplies-container">
                <!-- Header Section -->
                <div class="supplies-header">
                    <h1 class="supplies-title">
                        <i class="fas fa-clipboard-list"></i>
                        Supply
                    </h1>
                    
                    <!-- Controls Row -->
                    <div class="controls-row">
                        <div class="search-filter-group">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" placeholder="Search by items or categories" 
                                value="{{ request('search') }}" id="searchInput">
                            </div>                    
                        </div>
                        
                        <div class="action-buttons">
                            @if(!auth()->user()->hasPermission('stock_in') && !auth()->user()->hasPermission('stock_out'))
                                <div class="no-permission-message">
                                    <i class="fas fa-lock"></i>
                                    <span>View Only Access</span>
                                </div>
                            @endif
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

                @if(session('error'))
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                @endif
                
                <!-- Table Section -->
                <div class="supplies-table-container">
                    @if($supplies->count() > 0)
                        <table class="supplies-table">
                            <thead>
                                <tr>
                                    <th style="width: 5%; text-align: center;">
                                        <i class="fas fa-hashtag"></i>
                                    </th>
                                    <th style="width: 25%;">
                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'id', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}">
                                            <i class="fas fa-user" style="margin-right: 5px;"></i>ID
                                            @if(request('sort_by') == 'id')
                                                <i class="fas fa-sort-{{ request('sort_direction') == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th style="width: 55%;">
                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'name', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}">
                                            <i class="fas fa-info-circle" style="margin-right: 5px;"></i>Description
                                            @if(request('sort_by') == 'name')
                                                <i class="fas fa-sort-{{ request('sort_direction') == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th style="width: 15%; text-align: left;">
                                        <i class="fas fa-cog" style="margin-right: 5px;"></i>Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($supplies as $supply)
                                    <tr>
                                        <td style="text-align: center;">
                                            <div style="font-weight: 600; color: #6c757d; font-size: 14px;">
                                                {{ $loop->iteration }}
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600; color: #333; font-size: 16px;">
                                                #{{ str_pad($supply->id, 4, '0', STR_PAD_LEFT) }}
                                            </div>
                                            <div style="font-size: 12px; color: #6c757d; margin-top: 2px;">
                                                Item ID
                                            </div>
                                        </td>
                                        <td>
                                            <div style="margin-bottom: 8px;">
                                                <div style="font-weight: 600; font-size: 16px; color: #333; margin-bottom: 4px; line-height: 1.4;">
                                                    {{ $supply->name }}@if($supply->description), {{ $supply->description }}@endif, {{ $supply->quantity }} {{ $supply->unit }}
                                                </div>
                                            </div>
                                        </td>
                                        <td style="text-align: center;">
                                            <div class="action-buttons-cell">
                                                <a href="{{ route('client.stockcard.show', $supply->id) }}" class="btn btn-primary btn-sm" title="View Details">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        <!-- Pagination -->
                        @if($supplies->hasPages())
                            <div class="pagination-wrapper">
                                <div class="pagination-info">
                                    Showing {{ $supplies->firstItem() }} to {{ $supplies->lastItem() }} of {{ $supplies->total() }} results
                                </div>

                                <nav>
                                    <ul class="pagination">
                                        {{-- Previous Page Link --}}
                                        @if ($supplies->onFirstPage())
                                            <li class="disabled" aria-disabled="true">
                                                <span aria-hidden="true">
                                                    <i class="fas fa-chevron-left"></i> Previous
                                                </span>
                                            </li>
                                        @else
                                            <li>
                                                <a href="{{ $supplies->appends(request()->query())->previousPageUrl() }}" rel="prev">
                                                    <i class="fas fa-chevron-left"></i> Previous
                                                </a>
                                            </li>
                                        @endif

                                        {{-- Page Number Links --}}
                                        @foreach ($supplies->appends(request()->query())->getUrlRange(1, $supplies->lastPage()) as $page => $url)
                                            @if ($page == $supplies->currentPage())
                                                <li class="active" aria-current="page">
                                                    <span>{{ $page }}</span>
                                                </li>
                                            @else
                                                <li>
                                                    <a href="{{ $url }}">{{ $page }}</a>
                                                </li>
                                            @endif
                                        @endforeach

                                        {{-- Next Page Link --}}
                                        @if ($supplies->hasMorePages())
                                            <li>
                                                <a href="{{ $supplies->appends(request()->query())->nextPageUrl() }}" rel="next">
                                                    Next <i class="fas fa-chevron-right"></i>
                                                </a>
                                            </li>
                                        @else
                                            <li class="disabled" aria-disabled="true">
                                                <span aria-hidden="true">
                                                    Next <i class="fas fa-chevron-right"></i>
                                                </span>
                                            </li>
                                        @endif
                                    </ul>
                                </nav>

                                <div class="items-per-page">
                                    <label for="perPage">Show:</label>
                                    <select id="perPage" onchange="changePerPage(this.value)">
                                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                    </select>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="empty-state">
                            <i class="fas fa-clipboard-list"></i>
                            <h3>No stock items found</h3>
                            <p>No supplies available in the system.</p>
                            @if(auth()->user()->hasPermission('create'))
                                <a href="{{ route('supplies.create') }}" class="btn btn-success">
                                    <i class="fas fa-plus"></i>
                                    Add Supplies First
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>   
    </div>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

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
        });

        function changePerPage(perPage) {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', perPage);
            url.searchParams.delete('page');
            window.location.href = url.toString();
        }
    </script>

    <style>
        .no-permission-message {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: #f8f9fa;
            border: 2px solid #dee2e6;
            border-radius: 6px;
            color: #6c757d;
            font-size: 14px;
            font-weight: 600;
        }

        .no-permission-message i {
            font-size: 16px;
        }
    </style>

    @include('layouts.core.footer')
</body>
</html>