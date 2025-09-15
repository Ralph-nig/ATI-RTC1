<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RSMI - Report of Supplies and Materials Issued</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
        .rsmi-content {
            padding: 30px;
            background-color: #f8f9fa;
            min-height: calc(100vh - 80px);
        }

        .rsmi-header {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .rsmi-title {
            color: #2c3e50;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        /* .rsmi-subtitle {
            color: #7f8c8d;
            font-size: 16px;
        } */

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
            background-color: #296218;
            transform: translateY(-1px);
            color: white;
        }

        .report-filters {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .filter-row {
            display: flex;
            gap: 20px;
            align-items: end;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
            min-width: 200px;
        }

        .filter-group label {
            font-weight: 600;
            color: #2c3e50;
            font-size: 14px;
        }

        .filter-group input,
        .filter-group select {
            padding: 10px;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .filter-group input:focus,
        .filter-group select:focus {
            outline: none;
            border-color: #27ae60;
        }

        .generate-btn {
            background-color: #3498db;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .generate-btn:hover {
            background-color: #2980b9;
            transform: translateY(-1px);
        }

        .report-table-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
        }

        .report-table th {
            background-color: #34495e;
            color: white;
            padding: 15px 12px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
        }

        .report-table td {
            padding: 12px;
            border-bottom: 1px solid #e1e8ed;
            font-size: 14px;
        }

        .report-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
            font-style: italic;
        }

        .export-buttons {
            display: flex;
            gap: 10px;
            margin: 20px 0;
        }

        .export-btn {
            background-color: #e74c3c;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .export-btn.excel {
            background-color: #27ae60;
        }

        .export-btn:hover {
            transform: translateY(-1px);
            opacity: 0.9;
        }

        @media (max-width: 768px) {
            .rsmi-content {
                padding: 15px;
            }
            
            .filter-row {
                flex-direction: column;
                align-items: stretch;
            }
            
            .filter-group {
                min-width: auto;
            }
            
            .export-buttons {
                flex-direction: column;
            }
            
            .report-table-container {
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        @include('layouts.core.sidebar')
        <div class="details">
            @include('layouts.core.header')
            
            <div class="rsmi-content">
                <a href="{{ route('client.reports.index') }}" class="back-button">
                    <i class="fas fa-arrow-left"></i>
                    Back to Reports
                </a>

                <div class="rsmi-header">
                    <h1 class="rsmi-title">RSMI - Report of Supplies and Materials Issued</h1>
                    <!-- <p class="rsmi-subtitle">Generate and view reports of supplies and materials issued</p> -->
                </div>

                <div class="report-filters">
                    <form id="rsmi-filter-form" method="GET">
                        <div class="filter-row">
                            <div class="filter-group">
                                <label for="date_from">Date From</label>
                                <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}">
                            </div>
                            
                            <div class="filter-group">
                                <label for="date_to">Date To</label>
                                <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}">
                            </div>
                            
                            <div class="filter-group">
                                <label for="department">Department</label>
                                <select id="department" name="department">
                                    <option value="">All Departments</option>
                                    <option value="admin" {{ request('department') == 'admin' ? 'selected' : '' }}>Administration</option>
                                    <option value="finance" {{ request('department') == 'finance' ? 'selected' : '' }}>Finance</option>
                                    <option value="hr" {{ request('department') == 'hr' ? 'selected' : '' }}>Human Resources</option>
                                    <option value="it" {{ request('department') == 'it' ? 'selected' : '' }}>IT Department</option>
                                </select>
                            </div>
                            
                            <div class="filter-group">
                                <label for="status">Status</label>
                                <select id="status" name="status">
                                    <option value="">All Status</option>
                                    <option value="issued" {{ request('status') == 'issued' ? 'selected' : '' }}>Issued</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            
                            <button type="submit" class="generate-btn">
                                <i class="fas fa-search"></i>
                                Generate Report
                            </button>
                        </div>
                    </form>
                </div>

                <div class="export-buttons">
                    <button class="export-btn" onclick="exportToPDF()">
                        <i class="fas fa-file-pdf"></i>
                        Export PDF
                    </button>
                    <button class="export-btn excel" onclick="exportToExcel()">
                        <i class="fas fa-file-excel"></i>
                        Export Excel
                    </button>
                </div>

                <div class="report-table-container">
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Issue No.</th>
                                <th>Date Issued</th>
                                <th>Item Description</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Department</th>
                                <th>Requested By</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Sample data - replace with your actual data --}}
                            @forelse($rsmiData ?? [] as $item)
                            <tr>
                                <td>{{ $item->issue_no ?? 'RSMI-2024-001' }}</td>
                                <td>{{ $item->date_issued ?? '2024-01-15' }}</td>
                                <td>{{ $item->item_description ?? 'Office Supplies - Paper A4' }}</td>
                                <td>{{ $item->quantity ?? '100' }}</td>
                                <td>{{ $item->unit ?? 'reams' }}</td>
                                <td>{{ $item->department ?? 'Administration' }}</td>
                                <td>{{ $item->requested_by ?? 'John Doe' }}</td>
                                <td>
                                    <span class="status-badge status-{{ strtolower($item->status ?? 'issued') }}">
                                        {{ $item->status ?? 'Issued' }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="no-data">
                                    No data available.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>   
    </div>

    <script>
        function exportToPDF() {
            // Add PDF export functionality
            alert('PDF export');
        }

        function exportToExcel() {
            // Add Excel export functionality
            alert('Excel export');
        }

        // Form submission with loading state
        $('#rsmi-filter-form').on('submit', function() {
            $('.generate-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generating...');
        });
    </script>
</body>
</html>