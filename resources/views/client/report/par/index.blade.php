<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAR - Property Acknowledgment Receipt</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/supplies.css') }}">
    <link rel="stylesheet" href="{{ asset('css/announcement.css') }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        .supplies-container {
            background-color: #296218 !important;
            border-radius: 15px !important;
            padding: 20px !important;
            margin: 20px 0 !important;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1) !important;
        }
        .supplies-header {
            background: rgba(255,255,255,0.15) !important;
            border-radius: 12px !important;
            padding: 20px !important;
            margin-bottom: 20px !important;
            backdrop-filter: blur(10px) !important;
        }
        .supplies-title {
            color: white !important;
            font-size: 24px !important;
            font-weight: bold !important;
            margin: 0 !important;
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
        }
        .par-badge {
            background: rgba(255,255,255,0.2);
            color: white;
            font-size: 12px;
            padding: 3px 10px;
            border-radius: 20px;
            font-weight: 500;
            margin-left: 8px;
            vertical-align: middle;
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
            padding: 13px 12px;
            text-align: left;
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
            white-space: nowrap;
        }
        .supplies-table td {
            padding: 13px 12px;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
            font-size: 14px;
        }
        .supplies-table tr:hover { background: #f8f9fa; }

        .condition-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .condition-serviceable   { background: #d4edda; color: #155724; border: 1px solid #a3d9a5; }
        .condition-unserviceable { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        .doc-badge {
            background: #e8f5e9;
            color: #296218;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 4px;
            border: 1px solid #a5d6a7;
        }

        .btn-print {
            background: #296218;
            color: white;
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
        }
        .btn-print:hover {
            background: #1f4f13;
            color: white;
            transform: translateY(-1px);
        }

        .search-box {
            position: relative;
            flex: 1;
            min-width: 200px;
            max-width: 300px;
        }
        .search-box input {
            width: 100%;
            padding: 10px 15px 10px 40px;
            border: none;
            border-radius: 25px;
            background: rgba(255,255,255,0.95);
            font-size: 14px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .search-box i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }

        .controls-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 0;
        }

        .stat-info {
            color: rgba(255,255,255,0.9);
            font-size: 13px;
            margin-top: 8px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        .empty-state i {
            font-size: 60px;
            margin-bottom: 18px;
            opacity: 0.4;
            color: #296218;
            display: block;
        }
        .empty-state h3 { font-size: 22px; margin-bottom: 10px; color: #495057; }
        .empty-state p  { font-size: 15px; }

        .pagination-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 12px;
            padding: 16px;
            border-top: 1px solid #dee2e6;
        }
        .pagination { list-style: none; padding: 0; margin: 0; display: flex; gap: 4px; flex-wrap: wrap; }
        .pagination li { list-style: none; }
        .pagination a, .pagination span {
            padding: 7px 13px; text-decoration: none; border-radius: 7px;
            color: #495057; background: #f8f9fa; border: 1px solid #dee2e6;
            font-weight: 500; font-size: 14px; transition: all 0.2s ease;
            display: inline-flex; align-items: center;
        }
        .pagination a:hover      { background: #296218; color: white; border-color: #296218; }
        .pagination .active span { background: #296218; color: white; border-color: #296218; }
        .pagination .disabled span { color: #adb5bd; cursor: not-allowed; }

        @media (max-width: 768px) {
            .supplies-table th:nth-child(4),
            .supplies-table td:nth-child(4),
            .supplies-table th:nth-child(6),
            .supplies-table td:nth-child(6) { display: none; }
        }
    </style>
</head>
<body>
    <div class="container">
        @include('layouts.core.sidebar')
        <div class="details">
            @include('layouts.core.header')

            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            <div class="supplies-container">
                <div class="supplies-header">
                    <a href="{{ route('client.reports.index') }}" class="back-button">
                        <i class="fas fa-arrow-left"></i>
                        Back to Reports
                    </a>
                    <h1 class="supplies-title">
                        <i class="fas fa-file-signature"></i>
                        Property Acknowledgment Receipt
                        <span class="par-badge">PAR</span>
                    </h1>
                    <div class="controls-row" style="margin-top: 12px;">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchInput" placeholder="Search equipment..." />
                        </div>
                    </div>
                </div>

                <div class="supplies-table-container">
                    @if($equipment->count() > 0)
                        <table class="supplies-table" id="parTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>PAR No.</th>
                                    <th>Property No.</th>
                                    <th>Article / Description</th>
                                    <th>Unit</th>
                                    <th>Qty</th>
                                    <th>Unit Value</th>
                                    <th>Date Acquired</th>
                                    <th>Responsible Person</th>
                                    <th>Condition</th>
                                    <th style="text-align:center;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($equipment as $index => $item)
                                <tr>
                                    <td>{{ $equipment->firstItem() + $index }}</td>
                                    <td><span class="doc-badge">{{ $item->document_number }}</span></td>
                                    <td>{{ $item->property_number }}</td>
                                    <td>
                                        <strong>{{ $item->article }}</strong>
                                        @if($item->description)
                                            <br><small style="color:#6c757d;">{{ Str::limit($item->description, 60) }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $item->unit_of_measurement }}</td>
                                    <td>{{ $item->quantity ?? 1 }}</td>
                                    <td><strong>₱{{ number_format($item->unit_value, 2) }}</strong></td>
                                    <td>{{ $item->acquisition_date ? \Carbon\Carbon::parse($item->acquisition_date)->format('M d, Y') : 'N/A' }}</td>
                                    <td>{{ $item->responsible_person ?? 'N/A' }}</td>
                                    <td>
                                        <span class="condition-badge {{ $item->condition === 'Serviceable' ? 'condition-serviceable' : 'condition-unserviceable' }}">
                                            <i class="fas {{ $item->condition === 'Serviceable' ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                            {{ $item->condition }}
                                        </span>
                                    </td>
                                    <td style="text-align:center;">
                                        <a href="{{ route('client.report.par.print', $item->id) }}" 
                                           target="_blank" class="btn-print">
                                            <i class="fas fa-print"></i> Print PAR
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="pagination-wrapper">
                            <span style="color:#6c757d;font-size:14px;">
                                Showing {{ $equipment->firstItem() }}–{{ $equipment->lastItem() }} of {{ $equipment->total() }} records
                            </span>
                            {{ $equipment->links() }}
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-file-signature"></i>
                            <h3>No PAR Records Found</h3>
                            <p>No equipment with unit value of ₱50,000 and above found.</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#searchInput').on('keyup', function () {
                const val = $(this).val().toLowerCase();
                $('#parTable tbody tr').filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(val) > -1);
                });
            });
        });
    </script>

    @include('layouts.core.footer')
</body>
</html>