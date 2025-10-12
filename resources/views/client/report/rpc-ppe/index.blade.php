<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RPC-PPE Report</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .rpc-ppe-content {
            padding: 20px;
            background: white;
        }

        .report-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #000;
        }

        .report-header h1 {
            color: #000;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: #296218;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .back-button:hover {
            background-color: #1e4612;
            color: white;
        }

        .filters-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }

        .filters-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
            align-items: end;
        }

        .filter-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
        }

        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: 6px 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 13px;
        }

        .filter-actions {
            display: flex;
            gap: 8px;
        }

        .btn {
            padding: 6px 14px;
            border: none;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #296218;
            color: white;
        }

        .btn-primary:hover {
            background: #1e4612;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .classification-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }

        .equipment-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            border: 2px solid #000;
        }

        .equipment-table th {
            padding: 8px 6px;
            text-align: center;
            font-weight: bold;
            color: #000;
            border: 1px solid #000;
            background: #e8f5e9;
            font-size: 10px;
            text-transform: uppercase;
            vertical-align: middle;
        }

        .equipment-table td {
            padding: 8px 6px;
            border: 1px solid #000;
            vertical-align: top;
            font-size: 11px;
        }

        .classification-header-row {
            background: #b8daba !important;
        }

        .classification-header-row td {
            font-weight: bold;
            font-size: 12px;
            padding: 10px;
            text-align: center;
            color: #000;
        }

        .article-group-row {
            background: #d4edda;
        }

        .article-group-row td {
            font-weight: bold;
            padding: 8px;
            color: #000;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .shortage-overage {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4px;
        }

        .shortage-overage div {
            font-size: 10px;
        }

        .remarks-cell {
            font-size: 10px;
            line-height: 1.4;
        }

        .remarks-cell div {
            margin-bottom: 3px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 64px;
            color: #dee2e6;
            margin-bottom: 20px;
        }

        .print-button {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #296218;
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            cursor: pointer;
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .print-button:hover {
            background: #1e4612;
            transform: scale(1.05);
        }

        @media print {
            body {
                background: white;
            }
            
            .back-button, .filters-section, .print-button {
                display: none !important;
            }

            .rpc-ppe-content {
                padding: 0;
            }

            .equipment-table {
                font-size: 9px;
            }

            .equipment-table th {
                font-size: 8px;
                padding: 6px 4px;
            }

            .equipment-table td {
                font-size: 9px;
                padding: 6px 4px;
            }

            .classification-section {
                page-break-inside: avoid;
            }

            @page {
                margin: 0.5cm;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        @include('layouts.core.sidebar')
        <div class="details">
            @include('layouts.core.header')
            
            <div class="rpc-ppe-content">
                <a href="{{ route('client.reports.index') }}" class="back-button">
                    <i class="fas fa-arrow-left"></i>
                    Back to Reports
                </a>

                <div class="report-header">
                    <h1>REPORT ON THE PHYSICAL COUNT OF PROPERTY, PLANT AND EQUIPMENT</h1>
                    <p style="font-size: 12px; margin-top: 5px;">As of {{ date('F d, Y') }}</p>
                </div>

                <!-- Filters -->
                <div class="filters-section">
                    <form method="GET" action="{{ route('client.report.rpc-ppe') }}" class="filters-form">
                        <div class="filter-group">
                            <label>Classification</label>
                            <select name="classification">
                                <option value="">All Classifications</option>
                                @foreach($classifications as $class)
                                    <option value="{{ $class }}" {{ request('classification') == $class ? 'selected' : '' }}>
                                        {{ $class }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="filter-group">
                            <label>Condition</label>
                            <select name="condition">
                                <option value="">All Conditions</option>
                                <option value="Serviceable" {{ request('condition') == 'Serviceable' ? 'selected' : '' }}>Serviceable</option>
                                <option value="Unserviceable" {{ request('condition') == 'Unserviceable' ? 'selected' : '' }}>Unserviceable</option>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label>Date From</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}">
                        </div>

                        <div class="filter-group">
                            <label>Date To</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}">
                        </div>

                        <div class="filter-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <a href="{{ route('client.report.rpc-ppe') }}" class="btn btn-secondary">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Equipment Report Table -->
                @if($groupedEquipment->count() > 0)
                    <div class="classification-section">
                        <table class="equipment-table">
                            <thead>
                                <tr>
                                    <th rowspan="2" style="width: 12%;">ARTICLE/ITEM</th>
                                    <th rowspan="2" style="width: 15%;">DESCRIPTION</th>
                                    <th rowspan="2" style="width: 10%;">PROPERTY NUMBER</th>
                                    <th rowspan="2" style="width: 8%;">UNIT OF MEASURE</th>
                                    <th rowspan="2" style="width: 8%;">UNIT VALUE</th>
                                    <th rowspan="2" style="width: 8%;">Acquisition<br>Date</th>
                                    <th rowspan="2" style="width: 6%;">QUANTITY per<br>PROPERTY CARD</th>
                                    <th rowspan="2" style="width: 6%;">QUANTITY per<br>PHYSICAL COUNT</th>
                                    <th colspan="2" style="width: 10%;">SHORTAGE/OVERAGE</th>
                                    <th colspan="3" style="width: 17%;">REMARKS</th>
                                </tr>
                                <tr>
                                    <th style="width: 5%;">Quantity</th>
                                    <th style="width: 5%;">Value</th>
                                    <th style="width: 6%;">Person<br>Responsible</th>
                                    <th style="width: 6%;">Responsibility<br>Center</th>
                                    <th style="width: 5%;">Condition of<br>Properties</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($groupedEquipment as $classification => $equipmentItems)
                                    <!-- Classification Header -->
                                    <tr class="classification-header-row">
                                        <td colspan="13">
                                            {{ strtoupper($classification ?: 'UNCLASSIFIED EQUIPMENT') }}
                                        </td>
                                    </tr>

                                    @php
                                        $groupedByArticle = $equipmentItems->groupBy('article');
                                    @endphp

                                    @foreach($groupedByArticle as $article => $items)
                                        @foreach($items as $index => $equipment)
                                            <tr>
                                                @if($index === 0)
                                                    <!-- Show article name only for first item -->
                                                    <td rowspan="{{ $items->count() }}" style="vertical-align: middle; font-weight: bold;">
                                                        {{ $article }}
                                                    </td>
                                                @endif
                                                
                                                <td>{{ $equipment->description ?: '-' }}</td>
                                                <td class="text-center">{{ $equipment->property_number }}</td>
                                                <td class="text-center">{{ $equipment->unit_of_measurement }}</td>
                                                <td class="text-right">{{ number_format($equipment->unit_value, 2) }}</td>
                                                <td class="text-center">
                                                    {{ $equipment->acquisition_date ? $equipment->acquisition_date->format('M-d') : '-' }}
                                                </td>
                                                <td class="text-center">1</td>
                                                <td class="text-center">1</td>
                                                <td class="text-center">-</td>
                                                <td class="text-center">-</td>
                                                <td class="text-center remarks-cell">
                                                    {{ $equipment->responsible_person ?: 'Unknown / Book of the Accountant' }}
                                                </td>
                                                <td class="text-center remarks-cell">
                                                    {{ $equipment->location ?: '-' }}
                                                </td>
                                                <td class="text-center">{{ $equipment->condition }}</td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-box-open"></i>
                        <h3>No Equipment Found</h3>
                        <p>There are no equipment records to display. Try adjusting your filters or add equipment first.</p>
                    </div>
                @endif

                <!-- Print Button -->
                <button class="print-button" onclick="window.print()" title="Print Report">
                    <i class="fas fa-print"></i>
                </button>
            </div>
        </div>
    </div>
</body>
</html>