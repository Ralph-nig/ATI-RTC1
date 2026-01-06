?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deleted Equipment History</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/supplies.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="container">
        @include('layouts.core.sidebar')
        <div class="details">
            @include('layouts.core.header')
            
            <div class="supplies-container">
                <div class="supplies-header">
                    <h1 class="supplies-title">
                        <i class="fas fa-trash-restore"></i>
                        Deleted Equipment History
                    </h1>
                    
                    <div class="controls-row">
                        <div class="search-filter-group">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" placeholder="Search deleted equipment" 
                                       value="{{ request('search') }}" id="searchInput">
                            </div>
                            
                            <div class="filter-dropdown">
                                <select id="conditionFilter">
                                    <option value="">All Conditions</option>
                                    @foreach($conditions ?? [] as $condition)
                                        <option value="{{ $condition }}" {{ request('condition') == $condition ? 'selected' : '' }}>
                                            {{ $condition }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <input type="date" id="dateFrom" class="form-control" 
                                   value="{{ request('date_from') }}" placeholder="From">
                            
                            <input type="date" id="dateTo" class="form-control" 
                                   value="{{ request('date_to') }}" placeholder="To">
                        </div>
                        
                        <div class="action-buttons">
                            <a href="{{ route('client.equipment.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i>
                                Back to Equipment
                            </a>
                        </div>
                    </div>
                </div>
                
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
                
                <div class="supplies-table-container">
                    @if($deletedItems->count() > 0)
                        <table class="supplies-table">
                            <thead>
                                <tr>
                                    <th>Property #</th>
                                    <th>Equipment Details</th>
                                    <th>Value</th>
                                    <th>Condition</th>
                                    <th>Deleted By</th>
                                    <th>Deleted On</th>
                                    <th style="text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($deletedItems as $item)
                                    <tr>
                                        <td>
                                            <div style="font-weight: 600; color: #6c757d;">
                                                {{ $item->property_number }}
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600; margin-bottom: 4px;">{{ $item->article }}</div>
                                            @if($item->description)
                                                <div style="font-size: 12px; color: #6c757d;">
                                                    {{ Str::limit($item->description, 40) }}
                                                </div>
                                            @endif
                                            @if($item->classification)
                                                <span class="status-badge" style="background: #e3f2fd; color: #1565c0; margin-top: 4px;">
                                                    {{ $item->classification }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div style="font-weight: 600; color: #28a745;">
                                                ₱{{ number_format($item->unit_value, 2) }}
                                            </div>
                                            <div style="font-size: 12px; color: #6c757d;">
                                                {{ $item->unit_of_measurement }}
                                            </div>
                                        </td>
                                        <td>
                                            @if($item->condition === 'Serviceable')
                                                <span class="status-badge" style="background: #d4edda; color: #155724;">
                                                    <i class="fas fa-check-circle"></i>
                                                    Serviceable
                                                </span>
                                            @else
                                                <span class="status-badge" style="background: #f8d7da; color: #721c24;">
                                                    <i class="fas fa-times-circle"></i>
                                                    Unserviceable
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div style="font-weight: 600;">{{ $item->user->name }}</div>
                                            <div style="font-size: 12px; color: #6c757d;">
                                                {{ $item->user->email }}
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600;">
                                                {{ $item->created_at->format('M d, Y') }}
                                            </div>
                                            <div style="font-size: 12px; color: #6c757d;">
                                                {{ $item->created_at->format('h:i A') }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="action-buttons-cell" style="justify-content: center;">
                                                @if(auth()->user()->hasPermission('create'))
                                                    <form method="POST" action="{{ route('deleted-equipment.restore', $item) }}" 
                                                          style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm" 
                                                                title="Restore Equipment"
                                                                onclick="return confirm('Are you sure you want to restore this equipment?')">
                                                            <i class="fas fa-undo"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                
                                                @if(auth()->user()->hasPermission('delete'))
                                                    <form method="POST" action="{{ route('deleted-equipment.permanent-delete', $item) }}" 
                                                          style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" 
                                                                title="Permanently Delete"
                                                                onclick="return confirm('⚠️ WARNING: This will PERMANENTLY delete this equipment. This action CANNOT be undone! Are you absolutely sure?')">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        @if($deletedItems->hasPages())
                            <div class="pagination">
                                {{ $deletedItems->appends(request()->query())->links() }}
                            </div>
                        @endif
                    @else
                        <div class="empty-state">
                            <i class="fas fa-trash-restore"></i>
                            <h3>No deleted equipment found</h3>
                            <p>There are no deleted equipment in the history.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>   
    </div>

    <script>
        $(document).ready(function() {
            function updateURL() {
                const url = new URL(window.location.href);
                const params = {
                    search: $('#searchInput').val(),
                    condition: $('#conditionFilter').val(),
                    date_from: $('#dateFrom').val(),
                    date_to: $('#dateTo').val()
                };
                
                Object.keys(params).forEach(key => {
                    params[key] ? url.searchParams.set(key, params[key]) : url.searchParams.delete(key);
                });
                
                window.location.href = url.toString();
            }

            let searchTimeout;
            $('#searchInput').on('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => updateURL(), 500);
            });

            $('#conditionFilter, #dateFrom, #dateTo').on('change', updateURL);
        });
    </script>

    <style>
        .form-control {
            padding: 8px 12px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 6px;
            font-size: 13px;
            background: white;
            flex: 1;
            min-width: 120px;
        }

        .form-control:focus {
            outline: none;
            border-color: white;
        }
    </style>

    @include('layouts.core.footer')
</body>
</html> 