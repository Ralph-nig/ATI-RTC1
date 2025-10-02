<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Card - {{ $supply->name }}</title>
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
            
            <div class="supplies-container">
                <!-- Header Section -->
                <div class="form-header">
                    <a href="{{ route('client.stockcard.index') }}" class="back-button">
                        <i class="fas fa-arrow-left"></i>
                        Back to Stock Card
                    </a>
                    
                    <div class="supply-info-header">
                        <div class="supply-title">
                            <h2>#{{ str_pad($supply->id, 4, '0', STR_PAD_LEFT) }} - {{ $supply->name }}</h2>
                            @if($supply->description)
                                <p class="supply-description">{{ $supply->description }}</p>
                            @endif
                        </div>
                        <div class="current-stock">
                            <div class="stock-label">Current Stock</div>
                            <div class="stock-value {{ $supply->quantity <= $supply->minimum_stock ? 'low-stock' : 'normal-stock' }}">
                                {{ $supply->quantity }} {{ $supply->unit }}
                            </div>
                            @if($supply->quantity <= $supply->minimum_stock)
                                <div class="low-stock-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Low Stock Alert
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="supply-details">
                        <div class="detail-item">
                            <label>Category:</label>
                            <span>{{ $supply->category ?: 'Uncategorized' }}</span>
                        </div>
                        <div class="detail-item">
                            <label>Unit of Measurement:</label>
                            <span>{{ $supply->unit }}</span>
                        </div>
                        @if($supply->minimum_stock)
                        <div class="detail-item">
                            <label>Minimum Stock:</label>
                            <span>{{ $supply->minimum_stock }} {{ $supply->unit }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Stock Movements Table -->
                <div class="supplies-table-container">
                    @if($movements->count() > 0)
                        <table class="supplies-table stock-movements-table">
                            <thead>
                                <tr>
                                    <th style="width: 12%;">
                                        <i class="fas fa-calendar-alt" style="margin-right: 5px;"></i>
                                        Date
                                    </th>
                                    <th style="width: 15%;">
                                        <i class="fas fa-hashtag" style="margin-right: 5px;"></i>
                                        Reference
                                    </th>
                                    <th style="width: 12%; text-align: center;">
                                        <i class="fas fa-arrow-down" style="margin-right: 5px; color: #28a745;"></i>
                                        Stock In
                                    </th>
                                    <th style="width: 12%; text-align: center;">
                                        <i class="fas fa-arrow-up" style="margin-right: 5px; color: #dc3545;"></i>
                                        Stock Out
                                    </th>
                                    <th style="width: 35%;">
                                        <i class="fas fa-info-circle" style="margin-right: 5px;"></i>
                                        Description
                                    </th>
                                    <th style="width: 14%; text-align: center;">
                                        <i class="fas fa-balance-scale" style="margin-right: 5px;"></i>
                                        Balance
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($movements as $movement)
                                    <tr class="movement-row movement-{{ $movement->type }}">
                                        <td>
                                            <div class="date-display">
                                                <div class="date-main">{{ $movement->created_at->format('M d, Y') }}</div>
                                                <div class="date-time">{{ $movement->created_at->format('h:i A') }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="reference-display">
                                                <span class="reference-text">{{ $movement->reference }}</span>
                                            </div>
                                        </td>
                                        <td style="text-align: center;">
                                            @if($movement->type === 'in')
                                                <div class="quantity-display stock-in">
                                                    <i class="fas fa-plus"></i>
                                                    {{ $movement->quantity }} {{ $supply->unit }}
                                                </div>
                                            @else
                                                <span class="no-value">—</span>
                                            @endif
                                        </td>
                                        <td style="text-align: center;">
                                            @if($movement->type === 'out')
                                                <div class="quantity-display stock-out">
                                                    <i class="fas fa-minus"></i>
                                                    {{ $movement->quantity }} {{ $supply->unit }}
                                                </div>
                                            @else
                                                <span class="no-value">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="description-display">
                                                @if($movement->notes)
                                                    {{ $movement->notes }}
                                                @else
                                                    <span class="no-description">No description provided</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td style="text-align: center;">
                                            <div class="balance-display">
                                                <span class="balance-value">{{ $movement->balance_after }} {{ $supply->unit }}</span>
                                                <div class="balance-change">
                                                    @if($movement->type === 'in')
                                                        <span class="change-positive">+{{ $movement->quantity }}</span>
                                                    @else
                                                        <span class="change-negative">-{{ $movement->quantity }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        <!-- Pagination -->
                        @if($movements->hasPages())
                            <div class="pagination">
                                {{ $movements->links() }}
                            </div>
                        @endif
                    @else
                        <div class="empty-state">
                            <i class="fas fa-history"></i>
                            <h3>No stock movements found</h3>
                            <p>No stock movements have been recorded for this item yet.</p>
                            <div class="empty-actions">
                                <a href="{{ route('client.stockcard.stock-in') }}?supply_id={{ $supply->id }}" class="btn btn-success">
                                    <i class="fas fa-plus"></i>
                                    Add Stock
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>   
    </div>

    <style>
        .supplies-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .back-button {
            display: flex;
            align-items: center;
            gap: 8px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            margin-bottom: 15px;
            transition: color 0.3s ease;
        }

        .back-button:hover {
            color: white;
        }

        .supply-info-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border: 2px solid #dee2e6;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .supply-info-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            gap: 20px;
        }

        .supply-title h2 {
            margin: 0 0 8px 0;
            color: #dee2e6;
            font-size: 24px;
            font-weight: 700;
        }

        .supply-description {
            margin: 0;
            color: #6c757d;
            font-size: 16px;
            line-height: 1.5;
        }

        .current-stock {
            text-align: center;
            min-width: 180px;
        }

        .stock-label {
            font-size: 12px;
            color: #dee2e6;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .stock-value {
            font-size: 28px;
            font-weight: 800;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 8px;
        }

        .stock-value.normal-stock {
            color: #28a745;
            background: rgba(40, 167, 69, 0.1);
            border: 2px solid #28a745;
        }

        .stock-value.low-stock {
            color: #dc3545;
            background: rgba(220, 53, 69, 0.1);
            border: 2px solid #dc3545;
        }

        .low-stock-warning {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            color: #dc3545;
            font-size: 12px;
            font-weight: 600;
            animation: pulse 2s infinite;
        }

        .supply-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .detail-item label {
            font-size: 12px;
            color: #dee2e6;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-item span {
            color: #dee2e6;
            font-weight: 500;
            font-size: 16px;
        }

        .table-header {
            margin-bottom: 20px;
        }

        .table-header h3 {
            margin: 0;
            color: #333;
            font-size: 20px;
            font-weight: 600;
        }

        .stock-movements-table {
            font-size: 14px;
        }

        .movement-row.movement-in {
            background: rgba(40, 167, 69, 0.03);
        }

        .movement-row.movement-out {
            background: rgba(220, 53, 69, 0.03);
        }

        .date-display {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .date-main {
            font-weight: 600;
            color: #333;
        }

        .date-time {
            font-size: 12px;
            color: #6c757d;
        }

        .reference-display {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            color: #495057;
            background: rgba(108, 117, 125, 0.1);
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 13px;
        }

        .quantity-display {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 13px;
        }

        .quantity-display.stock-in {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }

        .quantity-display.stock-out {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border: 1px solid rgba(220, 53, 69, 0.3);
        }

        .no-value {
            color: #adb5bd;
            font-size: 18px;
        }

        .description-display {
            line-height: 1.4;
            color: #333;
        }

        .no-description {
            color: #6c757d;
            font-style: italic;
        }

        .balance-display {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
        }

        .balance-value {
            font-weight: 700;
            color: #333;
            font-size: 16px;
        }

        .balance-change {
            font-size: 11px;
            font-weight: 600;
        }

        .change-positive {
            color: #28a745;
        }

        .change-negative {
            color: #dc3545;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }

        @media (max-width: 768px) {
            .supplies-header {
                flex-direction: column;
                align-items: stretch;
            }

            .supply-info-header {
                flex-direction: column;
                text-align: center;
            }

            .current-stock {
                min-width: unset;
            }

            .supply-details {
                grid-template-columns: 1fr;
            }

            .stock-movements-table {
                font-size: 12px;
            }

            .quantity-display {
                font-size: 11px;
                padding: 4px 8px;
            }
        }
    </style>
</body>
</html>