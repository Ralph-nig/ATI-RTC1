<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipment Details</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            background: #f5f6fa;
            font-family: 'Poppins', sans-serif;
        }
        .container {
            display: flex;
            min-height: 100vh;
        }
        .details {
            flex: 1;
            padding: 20px;
            max-width: 100%;
            overflow-x: hidden;
            box-sizing: border-box;
        }
        
        /* Content wrapper to match other pages */
        .content-wrapper {
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }
        .card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 25px;
            margin-bottom: 20px;
            width: 100%;
            box-sizing: border-box;
        }
        .card h2 {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 15px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            table-layout: fixed;
        }
        .table th, .table td {
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
            word-wrap: break-word;
        }
        .table th {
            width: 200px;
            background: #fafafa;
            color: #333;
            text-align: left;
        }
        .table td {
            width: calc(100% - 200px);
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 500;
        }
        .disposal-highlight {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
            padding: 4px 12px;
            border-radius: 4px;
            font-weight: 500;
        }
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            padding: 8px 14px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        .btn-back {
            background: #6c757d;
        }
        .btn-edit {
            background: #007bff;
        }
        .btn-ai {
            background: #8b5cf6;
        }
        .btn:hover {
            opacity: 0.9;
        }
        
        /* AI Prediction Section */
        .ai-prediction-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            width: 100%;
            box-sizing: border-box;
        }
        .ai-prediction-card h3 {
            margin: 0 0 15px 0;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        /* Supplies container for consistent styling */
        .supplies-container {
            background-color: #296218;
            border-radius: 15px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }
        .prediction-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        .prediction-item {
            background: rgba(255,255,255,0.15);
            padding: 12px;
            border-radius: 8px;
            min-width: 0;
        }
        .prediction-item label {
            display: block;
            font-size: 12px;
            opacity: 0.9;
            margin-bottom: 5px;
        }
        .prediction-item value {
            display: block;
            font-size: 16px;
            font-weight: 600;
            word-break: break-word;
        }
        .confidence-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .confidence-high {
            background: #10b981;
        }
        .confidence-medium {
            background: #f59e0b;
        }
        .confidence-low {
            background: #ef4444;
        }
        .ai-reasoning-box {
            margin-top: 15px;
            padding: 12px;
            background: rgba(255,255,255,0.1);
            border-radius: 8px;
            width: 100%;
            box-sizing: border-box;
        }
        .ai-reasoning-box label {
            display: block;
            font-size: 12px;
            opacity: 0.9;
            margin-bottom: 5px;
        }
        .ai-reasoning-box p {
            margin: 0;
            font-size: 14px;
            line-height: 1.6;
            word-wrap: break-word;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .details {
                padding: 15px;
            }
            .card {
                padding: 15px;
            }
            .table th {
                width: 140px;
                font-size: 13px;
            }
            .table td {
                width: calc(100% - 140px);
                font-size: 13px;
            }
            .prediction-grid {
                grid-template-columns: 1fr;
            }
            .btn-group {
                flex-direction: column;
            }
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        @include('layouts.core.sidebar')
        <div class="details">
            @include('layouts.core.header')
            @include('layouts.core.footer')
            
            <!-- AI Prediction Card -->
            @if($equipment->maintenance_prediction_days)
            <div class="supplies-container">
                <div class="ai-prediction-card">
                    <h3>
                        <i class="fas fa-robot"></i>
                        AI Maintenance Prediction
                    </h3>
                    <div class="prediction-grid">
                        <div class="prediction-item">
                            <label>Next Check In</label>
                            <value>{{ $equipment->maintenance_prediction_days }} days</value>
                        </div>
                        <div class="prediction-item">
                            <label>Due Date</label>
                            <value>{{ $equipment->maintenance_schedule_end ? $equipment->maintenance_schedule_end->format('M d, Y') : 'N/A' }}</value>
                        </div>
                        <div class="prediction-item">
                            <label>AI Confidence</label>
                            <value>
                                <span class="confidence-badge confidence-{{ $equipment->maintenance_prediction_confidence }}">
                                    {{ ucfirst($equipment->maintenance_prediction_confidence) }}
                                </span>
                            </value>
                        </div>
                    </div>
                    @if($equipment->maintenance_prediction_reasoning)
                    <div class="ai-reasoning-box">
                        <label>AI Reasoning:</label>
                        <p>{{ $equipment->maintenance_prediction_reasoning }}</p>
                    </div>
                    @endif
                </div>
                @endif

                <div class="card mt-4">
                    <h2><i class="fa-solid fa-box"></i> Equipment Details</h2>

                    @if(isset($equipment))
                    <table class="table">
                        <tr><th>Property Number</th><td>{{ $equipment->property_number }}</td></tr>
                        <tr><th>Article</th><td>{{ $equipment->article }}</td></tr>
                        <tr><th>Classification</th><td>{{ $equipment->classification ?? 'N/A' }}</td></tr>
                        <tr><th>Description</th><td>{{ $equipment->description ?? 'N/A' }}</td></tr>
                        <tr><th>Unit of Measurement</th><td>{{ $equipment->unit_of_measurement }}</td></tr>
                        <tr><th>Unit Value</th><td>₱{{ number_format($equipment->unit_value, 2) }}</td></tr>
                        <tr>
                            <th>Condition</th>
                            <td>
                                @if($equipment->condition === 'Serviceable')
                                    <span class="status-badge" style="background: #d4edda; color: #155724; border: 1px solid #a3d9a5;">
                                        <i class="fas fa-check-circle"></i> Serviceable
                                    </span>
                                @else
                                    <span class="status-badge" style="background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;">
                                        <i class="fas fa-times-circle"></i> Unserviceable
                                    </span>
                                @endif
                            </td>
                        </tr>
                        
                        @if($equipment->condition === 'Unserviceable' && $equipment->disposal_method)
                            <tr>
                                <th>Disposal Method</th>
                                <td>
                                    <span class="disposal-highlight">
                                        <i class="fas fa-recycle"></i> {{ $equipment->formatted_disposal_method }}
                                    </span>
                                </td>
                            </tr>
                        @endif
                        
                        <tr><th>Acquisition Date</th>
                            <td>{{ $equipment->acquisition_date ? \Carbon\Carbon::parse($equipment->acquisition_date)->format('F d, Y') : 'N/A' }}</td>
                        </tr>
                        <tr><th>Location</th><td>{{ $equipment->location ?? 'N/A' }}</td></tr>
                        <tr><th>Responsible Person</th><td>{{ $equipment->responsible_person ?? 'N/A' }}</td></tr>
                        <tr><th>Remarks</th><td>{{ $equipment->remarks ?? 'None' }}</td></tr>
                        
                        @if($equipment->last_maintenance_check)
                        <tr>
                            <th>Last Maintenance</th>
                            <td>{{ \Carbon\Carbon::parse($equipment->last_maintenance_check)->format('F d, Y h:i A') }}</td>
                        </tr>
                        @endif
                    </table>

                    <div class="btn-group">
                        <a href="{{ route('client.equipment.index') }}" class="btn btn-back">
                            <ion-icon name="arrow-back-outline"></ion-icon> Back
                        </a>
                        @if(auth()->user()->hasPermission('update'))
                            <a href="{{ route('equipment.edit', $equipment->id) }}" class="btn btn-edit">
                                <ion-icon name="create-outline"></ion-icon> Edit
                            </a>
                            <button onclick="repredictMaintenance()" class="btn btn-ai">
                                <i class="fas fa-robot"></i> Re-predict with AI
                            </button>
                        @endif
                    </div>
                    @else
                        <p>No equipment data found.</p>
                    @endif
                </div>
            </div>
            </div>
        </div>   
    </div>

    <script>
        function repredictMaintenance() {
            if (!confirm('Trigger AI to re-predict the maintenance schedule?')) {
                return;
            }

            $.ajax({
                url: '/client/equipment/maintenance/repredict/{{ $equipment->id }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    alert('✅ AI prediction updated successfully!');
                    location.reload();
                },
                error: function(xhr) {
                    alert('❌ Error: ' + (xhr.responseJSON?.message || 'Failed to re-predict'));
                }
            });
        }
    </script>
</body>
</html>