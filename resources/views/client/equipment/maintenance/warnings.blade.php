<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Warnings</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/supplies.css') }}">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .warning-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-left: 4px solid;
        }
        
        .warning-card.overdue {
            border-left-color: #dc3545;
            background: #fff5f5;
        }
        
        .warning-card.critical {
            border-left-color: #721c24;
            background: #f8d7da;
        }
        
        .warning-card.due_soon {
            border-left-color: #ffc107;
            background: #fff9e6;
        }
        
        .warning-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .warning-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .warning-badge.overdue {
            background: #dc3545;
            color: white;
        }
        
        .warning-badge.critical {
            background: #721c24;
            color: white;
        }
        
        .warning-badge.due_soon {
            background: #ffc107;
            color: #000;
        }
        
        .warning-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .detail-item {
            display: flex;
            flex-direction: column;
        }
        
        .detail-label {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 5px;
        }
        
        .detail-value {
            font-size: 14px;
            font-weight: 600;
            color: #333;
        }
        
        .warning-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
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
            background-color: rgba(0,0,0,0.4);
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 30px;
            border: 1px solid #888;
            border-radius: 10px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover,
        .close:focus {
            color: #000;
        }
        
        .filter-row {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .filter-item {
            flex: 1;
            min-width: 200px;
        }
        
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 14px;
            color: #6c757d;
        }
        
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 12px 15px;
            border-radius: 6px;
            border-left: 4px solid #17a2b8;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .alert-info i {
            font-size: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        @include('layouts.core.sidebar')
        <div class="details">
            @include('layouts.core.header')
            @include('layouts.core.footer')

            <div class="form-container">
                <div class="form-header">
                    <a href="{{ route('client.equipment.index') }}" class="back-button">
                        <i class="fas fa-arrow-left"></i>
                        Back to Equipment List
                    </a>
                    <h1 class="form-title">
                        <i class="fas fa-exclamation-triangle"></i>
                        Maintenance Warnings
                    </h1>
                    <div class="action-buttons">
                        <a href="{{ route('client.equipment.maintenance.logs') }}" class="btn btn-primary">
                            <i class="fas fa-history"></i>
                            View Logs
                        </a>
                    </div>
                </div>

                <!-- Stats Row -->
                <div class="stats-row">
                    <div class="stat-card">
                        <div class="stat-number" style="color: #ffc107;">
                            {{ $warnings->where('warning_type', 'due_soon')->where('status', 'active')->count() }}
                        </div>
                        <div class="stat-label">Due Soon</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" style="color: #dc3545;">
                            {{ $warnings->where('warning_type', 'overdue')->where('status', 'active')->count() }}
                        </div>
                        <div class="stat-label">Overdue</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" style="color: #721c24;">
                            {{ $warnings->where('warning_type', 'critical')->where('status', 'active')->count() }}
                        </div>
                        <div class="stat-label">Critical</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" style="color: #28a745;">
                            {{ $warnings->where('status', 'resolved')->count() }}
                        </div>
                        <div class="stat-label">Resolved</div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="filter-row">
                    <div class="filter-item">
                        <select id="statusFilter" class="form-select">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="acknowledged" {{ request('status') == 'acknowledged' ? 'selected' : '' }}>Acknowledged</option>
                            <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        </select>
                    </div>
                    <div class="filter-item">
                        <select id="typeFilter" class="form-select">
                            <option value="">All Types</option>
                            <option value="due_soon" {{ request('type') == 'due_soon' ? 'selected' : '' }}>Due Soon</option>
                            <option value="overdue" {{ request('type') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                            <option value="critical" {{ request('type') == 'critical' ? 'selected' : '' }}>Critical</option>
                        </select>
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

                <!-- Warnings List -->
                @if($warnings->count() > 0)
                    @foreach($warnings as $warning)
                        <div class="warning-card {{ $warning->warning_type }}">
                            <div class="warning-header">
                                <div>
                                    <h3 style="margin: 0 0 5px 0;">
                                        <i class="fas fa-tools"></i>
                                        {{ $warning->equipment->article }}
                                    </h3>
                                    <p style="margin: 0; color: #6c757d; font-size: 14px;">
                                        Property #{{ $warning->equipment->property_number }}
                                    </p>
                                </div>
                                <span class="warning-badge {{ $warning->warning_type }}">
                                    {{ $warning->formatted_warning_type }}
                                </span>
                            </div>

                            <div class="warning-details">
                                <div class="detail-item">
                                    <span class="detail-label">Warning Date</span>
                                    <span class="detail-value">
                                        {{ $warning->warning_date ? $warning->warning_date->format('M d, Y') : 'N/A' }}
                                    </span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Maintenance Due</span>
                                    <span class="detail-value">
                                        {{ $warning->equipment->maintenance_schedule_end ? $warning->equipment->maintenance_schedule_end->format('M d, Y') : 'N/A' }}
                                    </span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Location</span>
                                    <span class="detail-value">{{ $warning->equipment->location ?: 'N/A' }}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Responsible Person</span>
                                    <span class="detail-value">{{ $warning->equipment->responsible_person ?: 'N/A' }}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Current Condition</span>
                                    <span class="detail-value">
                                        @if($warning->equipment->condition === 'Serviceable')
                                            <span style="color: #28a745;">
                                                <i class="fas fa-check-circle"></i> Serviceable
                                            </span>
                                        @else
                                            <span style="color: #dc3545;">
                                                <i class="fas fa-times-circle"></i> Unserviceable
                                            </span>
                                        @endif
                                    </span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Status</span>
                                    <span class="detail-value">{{ ucfirst($warning->status) }}</span>
                                </div>
                            </div>

                            @if($warning->status === 'active')
                                <div class="warning-actions">
                                    <button class="btn btn-primary btn-sm" onclick="openMaintenanceModal({{ $warning->id }}, '{{ $warning->equipment->article }}', {{ $warning->equipment->id }})">
                                        <i class="fas fa-wrench"></i>
                                        Take Action
                                    </button>
                                </div>
                            @elseif($warning->status === 'resolved')
                                <div style="background: #d4edda; padding: 10px; border-radius: 5px; margin-top: 10px;">
                                    <small style="color: #155724;">
                                        <i class="fas fa-check-circle"></i>
                                        <strong>Resolved by:</strong> {{ $warning->acknowledgedBy->name ?? 'Unknown' }} 
                                        on {{ $warning->acknowledged_at ? $warning->acknowledged_at->format('M d, Y h:i A') : 'N/A' }}
                                    </small>
                                    @if($warning->acknowledgment_note)
                                        <p style="margin: 5px 0 0 0; color: #155724; font-size: 13px;">
                                            <strong>Action:</strong> {{ $warning->acknowledgment_note }}
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach

                    <!-- Pagination -->
                    @if($warnings->hasPages())
                        <div class="pagination">
                            {{ $warnings->appends(request()->query())->links() }}
                        </div>
                    @endif
                @else
                    <div class="empty-state">
                        <i class="fas fa-check-circle"></i>
                        <h3>No maintenance warnings</h3>
                        <p>All equipment maintenance is up to date!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Maintenance Action Modal - UPDATED for Auto 30-Day Schedule -->
    <div id="maintenanceModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeMaintenanceModal()">&times;</span>
            <h2><i class="fas fa-wrench"></i> Record Maintenance Action</h2>
            <p id="equipmentName" style="color: #6c757d; margin-bottom: 20px;"></p>
            
            <!-- {{-- Info banner about automatic 30-day reschedule --}}
            <div class="alert-info">
                <i class="fas fa-calendar-check"></i>
                <div>
                    <strong>Automatic Schedule:</strong> After submitting this maintenance action, the equipment will be automatically scheduled for maintenance again in 30 days.
                </div>
            </div> -->
            
            <form id="maintenanceForm" method="POST">
                @csrf
                <input type="hidden" id="warningId" name="warning_id">
                <input type="hidden" id="equipmentId" name="equipment_id">
                
                <div class="form-group">
                    <label for="action_taken" class="form-label required">Action Taken</label>
                    <textarea id="action_taken" name="action_taken" class="form-input form-textarea" 
                              rows="4" required 
                              placeholder="Describe what maintenance actions were performed..."></textarea>
                </div>

                <div class="form-group">
                    <label for="condition_after" class="form-label required">Equipment Condition After Maintenance</label>
                    <select id="condition_after" name="condition_after" class="form-select" required>
                        <option value="">Select Condition</option>
                        <option value="Serviceable">Serviceable</option>
                        <option value="Unserviceable">Unserviceable</option>
                    </select>
                </div>

                <div class="form-actions" style="margin-top: 20px;">
                    <button type="button" class="btn btn-secondary" onclick="closeMaintenanceModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Submit & Schedule Next Maintenance
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Filter handlers
            $('#statusFilter, #typeFilter').on('change', function() {
                const url = new URL(window.location.href);
                
                const status = $('#statusFilter').val();
                const type = $('#typeFilter').val();
                
                if (status) {
                    url.searchParams.set('status', status);
                } else {
                    url.searchParams.delete('status');
                }
                
                if (type) {
                    url.searchParams.set('type', type);
                } else {
                    url.searchParams.delete('type');
                }
                
                window.location.href = url.toString();
            });
        });

        function openMaintenanceModal(warningId, equipmentName, equipmentId) {
            document.getElementById('maintenanceModal').style.display = 'block';
            document.getElementById('warningId').value = warningId;
            document.getElementById('equipmentId').value = equipmentId;
            document.getElementById('equipmentName').textContent = 'Equipment: ' + equipmentName;
            document.getElementById('maintenanceForm').action = `/client/equipment/maintenance/process/${warningId}`;
        }

        function closeMaintenanceModal() {
            document.getElementById('maintenanceModal').style.display = 'none';
            document.getElementById('maintenanceForm').reset();
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('maintenanceModal');
            if (event.target == modal) {
                closeMaintenanceModal();
            }
        }
    </script>
</body>
</html>