<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Logs</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/supplies.css') }}">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .logs-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .logs-title {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 28px;
            color: #333;
            margin: 0;
        }

        .logs-title i {
            color: #296218;
        }

        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .filter-item {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-label {
            font-size: 14px;
            font-weight: 600;
            color: #495057;
        }

        .logs-table {
            width: 100%;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .logs-table thead {
            background: linear-gradient(135deg, #296218 0%, #3a7d23 100%);
            color: white;
        }

        .logs-table thead th {
            padding: 16px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .logs-table tbody tr {
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.2s ease;
        }

        .logs-table tbody tr:hover {
            background: #f8f9fa;
            transform: translateX(2px);
        }

        .logs-table tbody td {
            padding: 16px;
            font-size: 14px;
            color: #495057;
        }

        .log-date {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .log-date-main {
            font-weight: 600;
            color: #333;
        }

        .log-date-time {
            font-size: 12px;
            color: #6c757d;
        }

        .equipment-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .equipment-name {
            font-weight: 600;
            color: #333;
        }

        .equipment-number {
            font-size: 12px;
            color: #6c757d;
        }

        .action-type-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .action-type-maintenance {
            background: #cce5ff;
            color: #004085;
        }

        .action-type-status {
            background: #fff3cd;
            color: #856404;
        }

        .action-type-warning {
            background: #d4edda;
            color: #155724;
        }

        .condition-change {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .condition-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }

        .condition-serviceable {
            background: #d4edda;
            color: #155724;
        }

        .condition-unserviceable {
            background: #f8d7da;
            color: #721c24;
        }

        .action-text {
            max-width: 300px;
            line-height: 1.5;
        }

        .performed-by {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .performer-name {
            font-weight: 600;
            color: #333;
        }

        .performer-role {
            font-size: 12px;
            color: #6c757d;
        }

        .view-details-btn {
            padding: 8px 16px;
            background: #296218;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .view-details-btn:hover {
            background: #1f4912;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            background-color: #fefefe;
            margin: 3% auto;
            padding: 0;
            border-radius: 12px;
            width: 90%;
            max-width: 800px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            background: linear-gradient(135deg, #296218 0%, #3a7d23 100%);
            color: white;
            padding: 25px 30px;
            border-radius: 12px 12px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-body {
            padding: 30px;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .detail-item.full-width {
            grid-column: 1 / -1;
        }

        .detail-label {
            font-size: 13px;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-value {
            font-size: 15px;
            color: #333;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 3px solid #296218;
        }

        .close {
            color: white;
            font-size: 32px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.2s ease;
            line-height: 1;
        }

        .close:hover {
            transform: scale(1.1);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .empty-state i {
            font-size: 64px;
            color: #dee2e6;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            font-size: 24px;
            color: #495057;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #6c757d;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="container">
        @include('layouts.core.sidebar')
        <div class="details">
            @include('layouts.core.header')
            @include('layouts.core.footer')
            
            <div class="supplies-container">
                <!-- Header -->
                <div class="logs-header">
                    <h1 class="logs-title">
                        <i class="fas fa-history"></i>
                        Maintenance Logs
                    </h1>
                    
                    <div class="action-buttons">
                        <a href="{{ route('client.equipment.maintenance.warnings') }}" class="btn btn-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            View Warnings
                        </a>
                        @if(auth()->user()->hasPermission('read'))
                            <a href="#" class="btn btn-primary">
                                <i class="fas fa-download"></i>
                                Export Logs
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Filters -->
                <div class="filter-section">
                    <div class="filter-grid">
                        <div class="filter-item">
                            <label class="filter-label">Equipment</label>
                            <select id="equipmentFilter" class="form-select">
                                <option value="">All Equipment</option>
                                @foreach($equipment as $item)
                                    <option value="{{ $item->id }}" {{ request('equipment_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->article }} ({{ $item->property_number }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="filter-item">
                            <label class="filter-label">Action Type</label>
                            <select id="actionTypeFilter" class="form-select">
                                <option value="">All Action Types</option>
                                <option value="maintenance_check" {{ request('action_type') == 'maintenance_check' ? 'selected' : '' }}>
                                    Maintenance Check
                                </option>
                                <option value="status_update" {{ request('action_type') == 'status_update' ? 'selected' : '' }}>
                                    Status Update
                                </option>
                                <option value="warning_acknowledged" {{ request('action_type') == 'warning_acknowledged' ? 'selected' : '' }}>
                                    Warning Acknowledged
                                </option>
                            </select>
                        </div>

                        <div class="filter-item">
                            <label class="filter-label">From Date</label>
                            <input type="date" id="dateFrom" class="form-input" value="{{ request('date_from') }}">
                        </div>

                        <div class="filter-item">
                            <label class="filter-label">To Date</label>
                            <input type="date" id="dateTo" class="form-input" value="{{ request('date_to') }}">
                        </div>
                    </div>

                    <button class="btn btn-primary" onclick="applyDateFilter()">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                </div>

                <!-- Logs Table -->
                <div class="supplies-table-container">
                    @if($logs->count() > 0)
                        <table class="logs-table">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Equipment</th>
                                    <th>Action Type</th>
                                    <th>Condition Change</th>
                                    <th>Action Taken</th>
                                    <th>Performed By</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($logs as $log)
                                    <tr>
                                        <td>
                                            <div class="log-date">
                                                <span class="log-date-main">
                                                    {{ $log->maintenance_date->format('M d, Y') }}
                                                </span>
                                                <span class="log-date-time">
                                                    {{ $log->created_at->format('h:i A') }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="equipment-info">
                                                <span class="equipment-name">{{ $log->equipment->article }}</span>
                                                <span class="equipment-number">#{{ $log->equipment->property_number }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if($log->action_type === 'maintenance_check')
                                                <span class="action-type-badge action-type-maintenance">
                                                    <i class="fas fa-wrench"></i>
                                                    Maintenance Check
                                                </span>
                                            @elseif($log->action_type === 'status_update')
                                                <span class="action-type-badge action-type-status">
                                                    <i class="fas fa-edit"></i>
                                                    Status Update
                                                </span>
                                            @else
                                                <span class="action-type-badge action-type-warning">
                                                    <i class="fas fa-check"></i>
                                                    Warning Ack
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($log->condition_before && $log->condition_after)
                                                <div class="condition-change">
                                                    <span class="condition-badge {{ $log->condition_before === 'Serviceable' ? 'condition-serviceable' : 'condition-unserviceable' }}">
                                                        {{ $log->condition_before }}
                                                    </span>
                                                    <i class="fas fa-arrow-right" style="color: #6c757d;"></i>
                                                    <span class="condition-badge {{ $log->condition_after === 'Serviceable' ? 'condition-serviceable' : 'condition-unserviceable' }}">
                                                        {{ $log->condition_after }}
                                                    </span>
                                                </div>
                                            @else
                                                <span style="color: #6c757d; font-size: 12px;">No change</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="action-text">
                                                {{ Str::limit($log->action_taken, 80) }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="performed-by">
                                                <span class="performer-name">{{ $log->user->name }}</span>
                                                <span class="performer-role">{{ $log->user->role }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <button class="view-details-btn" onclick="showLogDetails({{ $log->id }})">
                                                <i class="fas fa-eye"></i>
                                                View
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        <!-- Pagination -->
                        @if($logs->hasPages())
                            <div class="pagination">
                                {{ $logs->appends(request()->query())->links() }}
                            </div>
                        @endif
                    @else
                        <div class="empty-state">
                            <i class="fas fa-history"></i>
                            <h3>No maintenance logs found</h3>
                            <p>No maintenance actions have been recorded yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Log Details Modal -->
    <div id="logDetailsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-file-alt"></i> Maintenance Log Details</h2>
                <span class="close" onclick="closeLogModal()">&times;</span>
            </div>
            <div class="modal-body" id="logDetailsContent">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#equipmentFilter, #actionTypeFilter').on('change', function() {
                applyFilters();
            });
        });

        function applyFilters() {
            const url = new URL(window.location.href);
            
            const equipment = $('#equipmentFilter').val();
            const actionType = $('#actionTypeFilter').val();
            
            if (equipment) {
                url.searchParams.set('equipment_id', equipment);
            } else {
                url.searchParams.delete('equipment_id');
            }
            
            if (actionType) {
                url.searchParams.set('action_type', actionType);
            } else {
                url.searchParams.delete('action_type');
            }
            
            window.location.href = url.toString();
        }

        function applyDateFilter() {
            const url = new URL(window.location.href);
            
            const dateFrom = $('#dateFrom').val();
            const dateTo = $('#dateTo').val();
            
            if (dateFrom) {
                url.searchParams.set('date_from', dateFrom);
            } else {
                url.searchParams.delete('date_from');
            }
            
            if (dateTo) {
                url.searchParams.set('date_to', dateTo);
            } else {
                url.searchParams.delete('date_to');
            }
            
            window.location.href = url.toString();
        }

        function showLogDetails(logId) {
            $.ajax({
                url: `/client/equipment/maintenance/logs/${logId}`,
                method: 'GET',
                success: function(response) {
                    $('#logDetailsContent').html(response);
                    $('#logDetailsModal').css('display', 'block');
                },
                error: function() {
                    alert('Error loading log details');
                }
            });
        }

        function closeLogModal() {
            $('#logDetailsModal').css('display', 'none');
        }

        window.onclick = function(event) {
            const modal = document.getElementById('logDetailsModal');
            if (event.target == modal) {
                closeLogModal();
            }
        }
    </script>
</body>
</html>