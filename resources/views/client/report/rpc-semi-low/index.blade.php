<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semi-Expendable Property Card (Low Value)</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body { font-family: Arial, sans-serif; font-size: 12px; background: #fff; color: #000; }

        .rpc-semi-content { padding: 24px 28px; background: white; }

        /* ── Controls ── */
        .back-button {
            display: inline-flex; align-items: center; gap: 8px;
            background-color: #296218; color: white; padding: 10px 20px;
            border: none; border-radius: 8px; text-decoration: none;
            font-weight: 500; transition: all 0.3s ease; margin-bottom: 20px;
        }
        .back-button:hover { background-color: #1e4612; color: white; }

        .filters-section {
            background: #f8f9fa; padding: 14px; border-radius: 8px;
            margin-bottom: 18px; border: 1px solid #ddd;
        }
        .filters-form {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px; align-items: end;
        }
        .filter-group label { display: block; font-size: 12px; font-weight: 600; color: #495057; margin-bottom: 5px; }
        .filter-group select,
        .filter-group input { width: 100%; padding: 6px 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 13px; }
        .filter-actions { display: flex; gap: 8px; align-items: flex-end; }
        .btn { padding: 7px 14px; border: none; border-radius: 4px; font-size: 13px; font-weight: 500; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-block; }
        .btn-primary  { background: #296218; color: white; }
        .btn-primary:hover  { background: #1e4612; }
        .btn-secondary { background: #6c757d; color: white; }

        /* ── Report Title ── */
        .report-title { text-align: center; font-size: 16px; font-weight: bold; letter-spacing: 0.5px; margin-bottom: 20px; }

        /* ══════════════════════════════════════════════
           PROPERTY CARD — green theme (screen only)
        ══════════════════════════════════════════════ */
        .property-card {
            margin-bottom: 30px;
            page-break-inside: avoid;
            width: 100%;
            border: 1.5px solid #296218;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(41,98,24,0.12);
        }

        /* Entity Name / Fund Cluster */
        .meta-table { width: 100%; border-collapse: collapse; margin-bottom: 6px; font-size: 12px; }
        .meta-table td { border: none; padding: 0; vertical-align: bottom; }
        .meta-left  { text-align: left; }
        .meta-right { text-align: right; }
        .underline-blank    { display: inline-block; border-bottom: 1px solid #000; min-width: 210px; height: 16px; vertical-align: bottom; }
        .underline-blank-sm { display: inline-block; border-bottom: 1px solid #000; min-width: 140px; height: 16px; vertical-align: bottom; }

        /* Main table */
        .property-table { width: 100%; border-collapse: collapse; border: 1.5px solid #296218; }

        .property-table th,
        .property-table td { border: 1px solid #a8d5a2; padding: 3px 5px; vertical-align: middle; font-size: 11px; color: #000; }

        /* ── Info rows: light green ── */
        .property-table .info-row td {
            font-size: 11px; padding: 4px 6px; height: 24px;
            background-color: #eaf4e8;
            border-color: #000000;
        }
        .lbl { font-weight: bold; }

        /* Property Number underline */
        .prop-num-cell { white-space: nowrap; }
        .prop-num-cell .prop-num-underline {
            display: inline-block; border-bottom: 1px solid #296218;
            width: calc(100% - 220px); min-width: 40px; vertical-align: bottom; margin-left: 2px;
        }

        /* ── Column header rows: solid dark green, white text ── */
        .property-table .header-row th {
            text-align: center; font-weight: bold; font-size: 11px;
            height: 26px; white-space: nowrap;
            background-color: #296218;
            color: #ffffff;
            border-color: #000000;
        }

        /* ── Data rows: white bg, light green border, hover effect ── */
        .property-table .data-row td {
            height: 22px; font-size: 11px;
            background-color: #ffffff;
            border-color: #000000;
        }
        .property-table .data-row:hover td { background-color: rgb(255, 250, 250); }

        .text-center { text-align: center; }
        .text-right  { text-align: right; }

        .empty-state { text-align: center; padding: 60px 20px; color: #6c757d; }
        .empty-state i { font-size: 64px; color: #dee2e6; margin-bottom: 20px; }

        /* ══════════════════════════════════════════════
           PRINT — pure black & white, no colors
        ══════════════════════════════════════════════ */
        @media print {
            @page { margin: 1cm; size: A4 portrait; }
            * { overflow: visible !important; box-sizing: border-box !important; }
            html, body { width: 100% !important; max-width: none !important; margin: 0 !important; padding: 0 !important; font-family: Arial, sans-serif !important; font-size: 9px !important; background: white !important; }
            .container  { width: 100% !important; max-width: 100% !important; margin: 0 !important; padding: 0 !important; }
            .details    { margin: 0 !important; padding: 0 !important; width: 100% !important; left: 0 !important; background: white !important; }
            .rpc-semi-content { padding: 0 !important; margin: 0 !important; width: 100% !important; }

            .back-button, .filters-section,
            .sidebar, .header, .logo, .system-title, .profile-pic, .user-profile, .brand-logo,
            .institute-name, .nav-icons, .export-fab, .fab, .navbar, .brand, .title-box,
            .header-container, .institute-title, .export-buttons, .dashboard-header,
            .header-left, .header-right, .navigation, .brand-container, .notifications,
            .user-avatar, .user-info, .sidebar *, .header *, .dashboard-header *,
            .fab *, .fab-print *, .fab-pdf *, .fab-excel * { display: none !important; }

            /* Strip ALL color */
            .property-card { border: 1px solid #000 !important; box-shadow: none !important; border-radius: 0 !important; margin-bottom: 16px !important; overflow: visible !important; }
            .property-table { border: 1px solid #000 !important; }
            .property-table th,
            .property-table td { border: 1px solid #000 !important; font-size: 8px !important; padding: 2px 3px !important; background-color: #ffffff !important; color: #000000 !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .property-table .info-row td   { height: 16px !important; font-size: 8px !important; background-color: #ffffff !important; border-color: #000 !important; }
            .property-table .header-row th { height: 16px !important; font-size: 8px !important; background-color: #ffffff !important; color: #000000 !important; border-color: #000 !important; }
            .property-table .data-row td   { height: 15px !important; background-color: #ffffff !important; border-color: #000 !important; }
            .prop-num-cell .prop-num-underline { border-bottom: 1px solid #000 !important; width: calc(100% - 160px) !important; }
            .report-title  { font-size: 13px !important; margin-bottom: 14px !important; }
            .meta-table    { font-size: 9px !important; margin-bottom: 4px !important; }
            .underline-blank    { min-width: 150px !important; border-bottom-color: #000 !important; }
            .underline-blank-sm { min-width: 100px !important; border-bottom-color: #000 !important; }
        }
    </style>
</head>
<body>
<div class="container">
    @include('layouts.core.sidebar')
    <div class="details">
        @include('layouts.core.header')
        <div class="rpc-semi-content">

            <a href="{{ route('client.reports.index') }}" class="back-button">
                <i class="fas fa-arrow-left"></i> Back to Reports
            </a>

            <div class="filters-section">
                <form method="GET" action="{{ route('client.report.rpc-semi-low') }}" class="filters-form">
                    <div class="filter-group">
                        <label>Date From</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}">
                    </div>
                    <div class="filter-group">
                        <label>Date To</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}">
                    </div>
                    <div class="filter-group">
                        <label>Article</label>
                        <select name="classification">
                            <option value="">All Articles</option>
                            @foreach($classifications as $class)
                                <option value="{{ $class }}" {{ request('classification') == $class ? 'selected' : '' }}>{{ $class }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Condition</label>
                        <select name="condition">
                            <option value="">All Conditions</option>
                            <option value="Serviceable"   {{ request('condition') == 'Serviceable'   ? 'selected' : '' }}>Serviceable</option>
                            <option value="Unserviceable" {{ request('condition') == 'Unserviceable' ? 'selected' : '' }}>Unserviceable</option>
                        </select>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Apply Filters</button>
                        <a href="{{ route('client.report.rpc-semi-low') }}" class="btn btn-secondary"><i class="fas fa-redo"></i> Reset</a>
                    </div>
                </form>
            </div>

            <div class="filters-section" style="margin-top:-8px;">
                <form method="GET" action="{{ route('client.report.rpc-semi-low') }}" class="filters-form">
                    <input type="hidden" name="classification" value="{{ request('classification') }}">
                    <input type="hidden" name="condition"      value="{{ request('condition') }}">
                    <input type="hidden" name="date_from"      value="{{ request('date_from') }}">
                    <input type="hidden" name="date_to"        value="{{ request('date_to') }}">
                    <div class="filter-group">
                        <label>Entity Name</label>
                        <input type="text" name="entity_name" value="{{ request('entity_name') }}">
                    </div>
                    <div class="filter-group">
                        <label>Fund Cluster</label>
                        <input type="text" name="fund_cluster" value="{{ request('fund_cluster') }}">
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">Apply Header</button>
                        <a href="{{ route('client.report.rpc-semi-low') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
            </div>

            <div class="report-title">SEMI-EXPENDABLE PROPERTY CARD</div>

            @if($groupedEquipment->count() > 0)
                @foreach($groupedEquipment as $classification => $equipmentItems)
                    @php $groupedByArticle = $equipmentItems->groupBy('article'); @endphp
                    @foreach($groupedByArticle as $article => $items)
                        @foreach($items as $equipment)

                            <div class="property-card">

                                <table class="meta-table">
                                    <tr>
                                        <td class="meta-left">
                                            <strong>Entity Name :</strong>&nbsp;
                                            @if(!empty(trim($header['entity_name'] ?? '')))
                                                {{ $header['entity_name'] }}
                                            @else
                                                <span class="underline-blank"></span>
                                            @endif
                                        </td>
                                        <td class="meta-right">
                                            <strong>Fund Cluster :</strong>&nbsp;
                                            @if(!empty(trim($header['fund_cluster'] ?? '')))
                                                {{ $header['fund_cluster'] }}
                                            @else
                                                <span class="underline-blank-sm"></span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>

                                <table class="property-table">
                                    <colgroup>
                                        <col style="width:9%"><col style="width:10%"><col style="width:7%">
                                        <col style="width:9%"><col style="width:8%"><col style="width:7%">
                                        <col style="width:7%"><col style="width:10%"><col style="width:8%">
                                        <col style="width:10%"><col style="width:15%">
                                    </colgroup>
                                    <thead>
                                        <tr class="info-row">
                                            <td colspan="8">
                                                <span class="lbl">Semi-expendable Property:</span>&nbsp;{{ $article ?: '' }}
                                            </td>
                                            <td colspan="3" class="prop-num-cell">
                                                <span class="lbl">Semi-expendable Property Number:</span>
                                                @if(!empty($equipment->property_number))
                                                    &nbsp;{{ $equipment->property_number }}
                                                @else
                                                    <span class="prop-num-underline"></span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr class="info-row">
                                            <td colspan="11">
                                                <span class="lbl">Description :</span>&nbsp;{{ $equipment->description ?: '' }}
                                            </td>
                                        </tr>
                                        <tr class="header-row">
                                            <th rowspan="2">Date</th>
                                            <th rowspan="2">Reference</th>
                                            <th colspan="3">Receipt</th>
                                            <th colspan="3">Issue/Transfer/ Disposal</th>
                                            <th>Balance</th>
                                            <th rowspan="2">Amount</th>
                                            <th rowspan="2">Remarks</th>
                                        </tr>
                                        <tr class="header-row">
                                            <th>Qty.</th>
                                            <th>Unit Cost</th>
                                            <th>Total Cost</th>
                                            <th>Item No.</th>
                                            <th>Qty.</th>
                                            <th>Office/Officer</th>
                                            <th>Qty.</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="data-row">
                                            <td class="text-center">{{ $equipment->acquisition_date ? $equipment->acquisition_date->format('M d, Y') : '' }}</td>
                                            <td></td>
                                            <td class="text-center">1</td>
                                            <td class="text-right">&#8369;{{ number_format($equipment->unit_value, 2) }}</td>
                                            <td class="text-right">&#8369;{{ number_format($equipment->unit_value, 2) }}</td>
                                            <td></td><td></td><td></td>
                                            <td class="text-center">1</td>
                                            <td class="text-right">&#8369;{{ number_format($equipment->unit_value, 2) }}</td>
                                            <td class="text-center">{{ $equipment->condition ?: 'Serviceable' }}</td>
                                        </tr>
                                        @for($i = 0; $i < 16; $i++)
                                        <tr class="data-row">
                                            <td></td><td></td><td></td><td></td><td></td>
                                            <td></td><td></td><td></td><td></td><td></td><td></td>
                                        </tr>
                                        @endfor
                                    </tbody>
                                </table>

                            </div>

                        @endforeach
                    @endforeach
                @endforeach

                @include('client.report._export_fab', [
                    'excelUrl' => route('client.report.rpc-semi-low.export.excel', request()->query()),
                    'pdfUrl'   => route('client.report.rpc-semi-low.export.pdf',   request()->query())
                ])
            @else
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <h3>No Equipment Found</h3>
                    <p>There are no semi-expendable properties with low value (below &#8369;50,000) to display.</p>
                </div>
            @endif

        </div>
    </div>
</div>
</body>
</html>