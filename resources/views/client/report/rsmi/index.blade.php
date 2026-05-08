<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REPORT OF SUPPLIES AND MATERIALS ISSUED</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* ── Sidebar/header icon fixes ── */
        .sidebar ion-icon { display:inline-block!important;visibility:visible!important;opacity:1!important;pointer-events:none; }
        .sidebar svg,.sidebar img:not([src=""]),.sidebar i { display:inline-flex!important;visibility:visible!important;opacity:1!important; }
        .header ion-icon,.header svg,.notification-count { display:inline-flex!important;visibility:visible!important;opacity:1!important; }
        .header img,.user-profile img { display:inline-block!important;visibility:visible!important;opacity:1!important; }
        ion-icon { --ionicon-stroke-width:32px; }

        /* ── Base ── */
        .iirup-content { padding: 20px; background: white; }
        .print-only { display: none !important; }

        /* ── Back button ── */
        .back-button {
            display: inline-flex; align-items: center; gap: 8px;
            background: #296218; color: white; padding: 10px 20px;
            border: none; border-radius: 8px; text-decoration: none;
            font-weight: 500; transition: all .3s; margin-bottom: 20px;
        }
        .back-button:hover { background: #1e4612; color: white; }

        /* ── Panel cards ── */
        .panel {
            background: #f8f9fa; padding: 14px 16px; border-radius: 8px;
            margin-bottom: 14px; border: 1px solid #ddd;
        }
        .panel-title { font-size: 13px; font-weight: 700; color: #296218; margin: 0 0 10px; display: flex; align-items: center; gap: 6px; }
        .panel-grid { display: grid; gap: 10px; align-items: end; }
        .fg label { display: block; font-size: 12px; font-weight: 600; color: #495057; margin-bottom: 4px; }
        .fg select, .fg input { width: 100%; padding: 6px 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 13px; box-sizing: border-box; }
        .fg-actions { display: flex; gap: 8px; align-items: flex-end; }

        /* ── Footer signatory grid ── */
        .footer-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .footer-col-label { font-size: 11px; font-weight: 700; color: #296218; margin-bottom: 6px; display: block; }
        .footer-sub-label { font-size: 10px; font-weight: 600; color: #555; margin: 6px 0 3px; }
        .footer-grid input { width: 100%; padding: 5px 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 12px; box-sizing: border-box; margin-bottom: 4px; }

        /* ── Buttons ── */
        .btn { padding: 6px 14px; border: none; border-radius: 4px; font-size: 13px; font-weight: 500; cursor: pointer; transition: all .3s; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
        .btn-primary   { background: #296218; color: white; }
        .btn-primary:hover { background: #1e4612; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-secondary:hover { background: #545b62; color: white; }
        .btn-info { background: #0d6efd; color: white; }
        .btn-info:hover { background: #0b5ed7; }

        /* ── Table toolbar ── */
        .table-toggle-bar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; }
        .record-count { font-size: 13px; color: #555; }

        /* ── RSMI table (screen only) ── */
        .tbl-wrap { overflow-x: visible; width: 100%; }
        table.rsmi-tbl {
            width: 100%; border-collapse: collapse;
            font-size: 9.5px; table-layout: fixed;
        }
        table.rsmi-tbl th, table.rsmi-tbl td {
            border: 1px solid #000; padding: 3px 3px;
            text-align: center; vertical-align: middle;
            word-wrap: break-word; overflow-wrap: break-word;
            white-space: normal; line-height: 1.25;
        }
        table.rsmi-tbl th {
            font-weight: bold; background: #d9d9d9;
            -webkit-print-color-adjust: exact; print-color-adjust: exact;
            overflow-wrap: normal; word-break: keep-all; hyphens: none;
        }
        table.rsmi-tbl td.tdl { text-align: left; }
        table.rsmi-tbl td.tdr { text-align: right; }
        table.rsmi-tbl td.total-label { font-weight: bold; text-align: right; }
        table.rsmi-tbl td.total-val   { font-weight: bold; text-align: right; }
        table.rsmi-tbl col.cA { width: 16%; }
        table.rsmi-tbl col.cB { width: 14%; }
        table.rsmi-tbl col.cC { width:  8%; }
        table.rsmi-tbl col.cD { width: 26%; }
        table.rsmi-tbl col.cE { width:  7%; }
        table.rsmi-tbl col.cF { width:  9%; }
        table.rsmi-tbl col.cG { width: 11%; }
        table.rsmi-tbl col.cH { width:  9%; }

        /* ── Sig Block Preview (screen) ── */
        .sig-preview { margin-top: 20px; padding: 16px; border: 1px solid #ddd; border-radius: 8px; background: #fafafa; }
        .sig-preview-title { font-weight: 700; color: #296218; margin-bottom: 12px; font-size: 13px; }
        .sig-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .sig-col { display: flex; flex-direction: column; gap: 2px; }
        .sig-col .sig-label { font-size: 11px; color: #666; margin-bottom: 4px; font-style: italic; }
        .sig-col .sig-name  { font-weight: bold; font-size: 12px; }
        .sig-col .sig-role  { font-size: 11px; color: #444; }

        /* ── Empty state ── */
        .empty-state { text-align: center; padding: 60px 20px; color: #6c757d; }
        .empty-state i { font-size: 64px; color: #dee2e6; margin-bottom: 20px; display: block; }

        /* ═══════════════════════════════════════════════════
           PRINT STYLES — match Appendix 64 reference exactly
           ═══════════════════════════════════════════════════ */
        @media print {
            @page { size: A4 portrait; margin: 12mm 14mm 12mm 14mm; }

            * { box-sizing: border-box !important; }
            html, body {
                width: 100% !important; margin: 0 !important; padding: 0 !important;
                background: white !important;
                font-family: Arial, sans-serif !important;
                font-size: 8pt !important; color: #000 !important;
            }

            /* Hide all screen-only chrome */
            .back-button, .panel, .table-toggle-bar, .export-fab, .fab,
            .sig-preview, .sidebar, .header, .navbar, .navigation,
            .dashboard-header, .header-left, .header-right,
            .notifications, .user-profile, .user-avatar,
            .screen-only, .btn, #table-wrapper { display: none !important; }

            .container, .details { margin: 0 !important; padding: 0 !important; width: 100% !important; left: 0 !important; }
            .iirup-content { padding: 0 !important; width: 100% !important; }

            /* Show print-only elements */
            .print-only { display: block !important; }

            /* ── Appendix 64 ── */
            .p-appendix {
                text-align: right !important;
                font-size: 8pt !important;
                font-style: italic !important;
                margin-bottom: 2pt !important;
            }

            /* ── Title ── */
            .p-title {
                text-align: center !important;
                font-size: 11pt !important;
                font-weight: bold !important;
                text-transform: uppercase !important;
                letter-spacing: 0.3pt !important;
                margin-bottom: 8pt !important;
                line-height: 1.2 !important;
            }

            /* ── Header (no border box) ── */
            .p-header {
                width: 100% !important;
                border-collapse: collapse !important;
                font-size: 8pt !important;
                margin-bottom: 4pt !important;
            }
            .p-header td {
                padding: 1pt 0 !important;
                vertical-align: bottom !important;
                border: none !important;
            }
            .p-header .hl { width: 55% !important; }
            .p-header .hr { width: 45% !important; text-align: right !important; }
            .p-hval {
                display: inline-block !important;
                border-bottom: 0.75pt solid #000 !important;
                min-width: 130pt !important;
                padding-bottom: 0.5pt !important;
            }
            .p-hval-sm {
                display: inline-block !important;
                border-bottom: 0.75pt solid #000 !important;
                min-width: 90pt !important;
                padding-bottom: 0.5pt !important;
            }

            /* ── Outer border box ── */
            .p-outer-box {
                width: 100% !important;
                border-collapse: collapse !important;
                border: 0.75pt solid #000 !important;
            }
            .p-outer-box > tbody > tr > td {
                padding: 0 !important; border: none !important;
            }

            /* ── Banner ── */
            .p-banner {
                width: 100% !important;
                border-collapse: collapse !important;
                font-size: 7.5pt !important;
                font-style: italic !important;
                border-bottom: 0.75pt solid #000 !important;
            }
            .p-banner td { padding: 2.5pt 4pt !important; vertical-align: middle !important; }
            .p-banner .bl { width: 55% !important; border-right: 0.75pt solid #000 !important; }
            .p-banner .br { width: 45% !important; text-align: right !important; }

            /* ── Data table ── */
            .p-data-tbl {
                width: 100% !important;
                border-collapse: collapse !important;
                font-size: 7.5pt !important;
                table-layout: fixed !important;
                border-bottom: 0.75pt solid #000 !important;
            }
            .p-data-tbl col.cA { width: 16% !important; }
            .p-data-tbl col.cB { width: 14% !important; }
            .p-data-tbl col.cC { width:  8% !important; }
            .p-data-tbl col.cD { width: 26% !important; }
            .p-data-tbl col.cE { width:  7% !important; }
            .p-data-tbl col.cF { width:  9% !important; }
            .p-data-tbl col.cG { width: 11% !important; }
            .p-data-tbl col.cH { width:  9% !important; }
            .p-data-tbl th {
                border: 0.75pt solid #000 !important;
                border-top: none !important;
                padding: 3pt 2pt !important;
                text-align: center !important;
                vertical-align: middle !important;
                background: #d9d9d9 !important;
                font-weight: bold !important;
                font-size: 7.5pt !important;
                line-height: 1.3 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .p-data-tbl th:first-child { border-left: none !important; }
            .p-data-tbl th:last-child  { border-right: none !important; }
            .p-data-tbl td {
                border: 0.75pt solid #000 !important;
                border-top: none !important;
                padding: 2.5pt 3pt !important;
                text-align: center !important;
                vertical-align: middle !important;
                font-size: 7.5pt !important;
                word-wrap: break-word !important;
                line-height: 1.3 !important;
            }
            .p-data-tbl td:first-child { border-left: none !important; }
            .p-data-tbl td:last-child  { border-right: none !important; }
            .p-data-tbl td.tdl { text-align: left !important; }
            .p-data-tbl td.tdr { text-align: right !important; }
            .p-data-tbl tr.total-row td { font-weight: bold !important; }
            .p-data-tbl tr.total-row td.total-label { text-align: right !important; padding-right: 4pt !important; }
            .p-data-tbl tr.total-row td.total-val   { text-align: right !important; }

            /* ── Recap section ── */
            .p-recap-row {
                width: 100% !important;
                border-collapse: collapse !important;
                border-bottom: 0.75pt solid #000 !important;
            }
            .p-recap-row td { padding: 0 !important; vertical-align: top !important; border: none !important; }
            .p-recap-row .rl { width: 55% !important; border-right: 0.75pt solid #000 !important; }
            .p-recap-row .rr { width: 45% !important; }
            .p-recap-inner { padding: 4pt 6pt !important; }
            .p-recap-title {
                font-weight: bold !important;
                font-size: 8pt !important;
                text-align: center !important;
                display: block !important;
                margin-bottom: 3pt !important;
            }
            .p-recap-tbl {
                width: 100% !important;
                border-collapse: collapse !important;
                font-size: 7.5pt !important;
                table-layout: fixed !important;
            }
            .p-recap-tbl th {
                border: 0.75pt solid #000 !important;
                padding: 2.5pt 2pt !important;
                background: #d9d9d9 !important;
                font-weight: bold !important;
                text-align: center !important;
                vertical-align: middle !important;
                font-size: 7.5pt !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .p-recap-tbl td {
                border: 0.75pt solid #000 !important;
                padding: 2pt 3pt !important;
                text-align: center !important;
                vertical-align: middle !important;
                font-size: 7.5pt !important;
                height: 12pt !important;
            }
            .p-recap-tbl td.tdr { text-align: right !important; }

            /* ── Sig cert row ── */
            .p-sig-cert {
                width: 100% !important;
                border-collapse: collapse !important;
                border-bottom: 0.75pt solid #000 !important;
            }
            .p-sig-cert td {
                padding: 4pt 6pt !important;
                font-size: 8pt !important;
                vertical-align: middle !important;
                border: none !important;
            }
            .p-sig-cert .cl { width: 55% !important; border-right: 0.75pt solid #000 !important; }
            .p-sig-cert .cr { width: 45% !important; }

            /* ── Sig name boxes ── */
            .p-sig-names {
                width: 100% !important;
                border-collapse: collapse !important;
            }
            .p-sig-names td { padding: 0 !important; border: none !important; }
            .p-sig-box {
                width: 100% !important;
                border-collapse: collapse !important;
            }
            .p-sig-box td.nc {
                border-right: 0.75pt solid #000 !important;
                border-bottom: none !important;
                padding: 8pt 4pt 0pt 4pt !important;
                text-align: center !important;
                font-weight: bold !important;
                font-size: 8pt !important;
                vertical-align: bottom !important;
                min-height: 20pt !important;
            }
            .p-sig-box td.nc:last-child { border-right: none !important; }
            .p-sig-box td.lc {
                border-right: 0.75pt solid #000 !important;
                border-top: 0.75pt solid #000 !important;
                padding: 2pt 4pt 5pt 4pt !important;
                text-align: center !important;
                font-size: 7pt !important;
                font-style: italic !important;
                vertical-align: top !important;
            }
            .p-sig-box td.lc:last-child { border-right: none !important; }
        }

        /* Hide all print-only elements on screen */
        .print-only,
        .p-appendix, .p-title, .p-header,
        .p-outer-box { display: none; }
    </style>
</head>
<body>
<div class="container">
    @include('layouts.core.sidebar')
    <div class="details">
        @include('layouts.core.header')
        <div class="iirup-content">

            <a href="{{ route('client.reports.index') }}" class="back-button screen-only">
                <i class="fas fa-arrow-left"></i> Back to Reports
            </a>

            {{-- ══════════════════════════════════════════════════════
                 PRINT-ONLY BLOCK — mirrors Appendix 64 exactly
                 ══════════════════════════════════════════════════════ --}}

            {{-- Appendix 64 --}}
            <div class="print-only p-appendix">Appendix 64</div>

            {{-- Title --}}
            <div class="print-only p-title">Report of Supplies and Materials Issued</div>

            {{-- Header: no border box, just label + underlined value --}}
            <table class="print-only p-header">
                <tr>
                    <td class="hl">
                        <strong>Entity Name:</strong>&nbsp;&nbsp;&nbsp;<span class="p-hval">{{ $header['entity_name'] ?: '&nbsp;' }}</span>
                    </td>
                    <td class="hr">
                        <strong>Serial No. :</strong>&nbsp;<span class="p-hval-sm">{{ $header['serial_no'] ?: '&nbsp;' }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="hl">
                        <strong>Fund Cluster:</strong>&nbsp;<span class="p-hval">{{ $header['fund_cluster'] ?: '&nbsp;' }}</span>
                    </td>
                    <td class="hr">
                        <strong>Date :</strong>&nbsp;<span class="p-hval-sm">{{ $header['date'] ?: '&nbsp;' }}</span>
                    </td>
                </tr>
            </table>

            {{-- Outer border box: contains banner + table + recap + signature --}}
            <table class="print-only p-outer-box">
            <tbody>

            {{-- Banner --}}
            <tr><td>
                <table class="p-banner">
                    <tr>
                        <td class="bl"><em>To be filled up by the Supply and/or Property Division/Unit</em></td>
                        <td class="br"><em>To be filled up by the Accounting Division/Unit</em></td>
                    </tr>
                </table>
            </td></tr>

            {{-- Main data table --}}
            <tr><td>
                <table class="p-data-tbl">
                    <colgroup>
                        <col class="cA"><col class="cB"><col class="cC"><col class="cD">
                        <col class="cE"><col class="cF"><col class="cG"><col class="cH">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>RIS No.</th>
                            <th>Responsibility<br>Center Code</th>
                            <th>Stock<br>No.</th>
                            <th>Item</th>
                            <th>Unit</th>
                            <th>Quantity<br>Issued</th>
                            <th>Unit Cost</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $grandTotalQty = 0; $grandTotalAmt = 0; @endphp
                        @foreach($grouped as $group)
                            @php
                                $rows = $group['rows'];
                                $rowCount = count($rows);
                                $grandTotalQty += $group['total_qty'];
                                $grandTotalAmt += $group['total_amount'];
                            @endphp
                            @foreach($rows as $i => $item)
                                @php
                                    $isFirst = ($i === 0);
                                    $isLast  = ($i === $rowCount - 1);
                                @endphp
                                <tr>
                                    <td class="tdl">{{ $item->issue_no }}</td>
                                    @if($isFirst)
                                        <td rowspan="{{ $rowCount }}">{{ $item->responsibility_center }}</td>
                                        <td rowspan="{{ $rowCount }}">{{ $item->stock_no }}</td>
                                        <td class="tdl" rowspan="{{ $rowCount }}">{{ $item->item }}</td>
                                        <td rowspan="{{ $rowCount }}">{{ $item->unit }}</td>
                                    @endif
                                    <td class="tdr">{{ $item->quantity_issued > 0 ? number_format($item->quantity_issued) : '' }}</td>
                                    <td class="tdr">{{ $isLast && $group['unit_cost'] > 0 ? number_format($group['unit_cost'], 2) : '' }}</td>
                                    <td class="tdr">{{ $isLast && $group['total_amount'] > 0 ? number_format($group['total_amount'], 2) : '' }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                        <tr class="total-row">
                            <td colspan="5" class="total-label">Total</td>
                            <td class="total-val">{{ number_format($grandTotalQty) }}</td>
                            <td class="total-val"></td>
                            <td class="total-val">{{ $grandTotalAmt > 0 ? number_format($grandTotalAmt, 2) : '' }}</td>
                        </tr>
                    </tbody>
                </table>
            </td></tr>

            {{-- Recapitulation --}}
            <tr><td>
                <table class="p-recap-row">
                    <tr>
                        <td class="rl">
                            <div class="p-recap-inner">
                                <span class="p-recap-title">Recapitulation:</span>
                                <table class="p-recap-tbl">
                                    <thead><tr>
                                        <th style="width:50%;">Stock No.</th>
                                        <th style="width:50%;">Quantity</th>
                                    </tr></thead>
                                    <tbody>
                                        @forelse($recapLeft as $row)
                                            <tr>
                                                <td>{{ $row['stock_no'] }}</td>
                                                <td class="tdr">{{ number_format($row['quantity']) }}</td>
                                            </tr>
                                        @empty
                                            <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </td>
                        <td class="rr">
                            <div class="p-recap-inner">
                                <span class="p-recap-title">Recapitulation:</span>
                                <table class="p-recap-tbl">
                                    <thead><tr>
                                        <th style="width:28%;">Unit Cost</th>
                                        <th style="width:36%;">Total Cost</th>
                                        <th style="width:36%;">UACS Object Code</th>
                                    </tr></thead>
                                    <tbody>
                                        @forelse($recapRight as $row)
                                            <tr>
                                                <td class="tdr">{{ $row['unit_cost']  > 0 ? number_format($row['unit_cost'],  2) : '' }}</td>
                                                <td class="tdr">{{ $row['total_cost'] > 0 ? number_format($row['total_cost'], 2) : '' }}</td>
                                                <td></td>
                                            </tr>
                                        @empty
                                            <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                </table>
            </td></tr>

            {{-- I hereby certify / Posted by --}}
            <tr><td>
                <table class="p-sig-cert">
                    <tr>
                        <td class="cl">I hereby certify to the correctness of the above information.</td>
                        <td class="cr">Posted by:</td>
                    </tr>
                </table>
            </td></tr>

            {{-- Name boxes --}}
            <tr><td>
                <table class="p-sig-names">
                    <tr><td>
                        <table class="p-sig-box">
                            <tr>
                                <td class="nc" style="width:38%;">{{ strtoupper(request('f_supply_name', '')) ?: '&nbsp;' }}</td>
                                <td class="nc" style="width:38%;">{{ strtoupper(request('f_acctg_name', '')) ?: '&nbsp;' }}</td>
                                <td class="nc" style="width:24%;">@if(request('f_acctg_date')){{ \Carbon\Carbon::parse(request('f_acctg_date'))->format('F d, Y') }}@endif</td>
                            </tr>
                            <tr>
                                <td class="lc" style="width:38%;">Signature over Printed Name of Supply and/or<br>Property Custodian</td>
                                <td class="lc" style="width:38%;">Signature over Printed Name of<br>Designated Accounting Staff</td>
                                <td class="lc" style="width:24%;">Date</td>
                            </tr>
                        </table>
                    </td></tr>
                </table>
            </td></tr>

            </tbody>
            </table>
            {{-- END PRINT-ONLY BLOCK --}}

            {{-- ══ Screen: Report title ══ --}}
            <div style="text-align:center; margin-bottom:14px;" class="screen-only">
                <div style="font-size:15px; font-weight:bold; text-transform:uppercase; margin-bottom:4px;">Report of Supplies and Materials Issued</div>
            </div>

            {{-- ══ 1. Apply Header ══ --}}
            <div class="panel screen-only">
                <div class="panel-title"><i class="fas fa-heading"></i> Apply Header</div>
                <form method="GET" action="{{ route('client.report.rsmi') }}">
                    <input type="hidden" name="date_from"      value="{{ request('date_from') }}">
                    <input type="hidden" name="date_to"        value="{{ request('date_to') }}">
                    <input type="hidden" name="description"    value="{{ request('description') }}">
                    <input type="hidden" name="status"         value="{{ request('status') }}">
                    <input type="hidden" name="f_supply_name"  value="{{ request('f_supply_name') }}">
                    <input type="hidden" name="f_supply_role"  value="{{ request('f_supply_role') }}">
                    <input type="hidden" name="f_acctg_name"   value="{{ request('f_acctg_name') }}">
                    <input type="hidden" name="f_acctg_date"   value="{{ request('f_acctg_date') }}">

                    <div class="panel-grid" style="grid-template-columns:150px 1fr 120px 160px 160px 160px auto;">
                        <div class="fg"><label>Month (As of)</label><input type="month" name="as_of" value="{{ request('as_of') }}"></div>
                        <div class="fg"><label>Entity Name</label><input type="text" name="entity_name" placeholder="e.g. ATI-RTC I" value="{{ request('entity_name') }}"></div>
                        <div class="fg"><label>Fund Cluster</label><input type="text" name="fund_cluster" placeholder="e.g. 01" value="{{ request('fund_cluster') }}"></div>
                        <div class="fg"><label>Serial No.</label><input type="text" name="serial_no" placeholder="e.g. 2026-01-01" value="{{ request('serial_no') }}"></div>
                        <div class="fg"><label>Date</label><input type="date" name="date" value="{{ request('date') }}"></div>
                        <div class="fg"><label>Accountable Person</label><input type="text" name="accountable_person" placeholder="Name" value="{{ request('accountable_person') }}"></div>
                        <div class="fg-actions">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Apply</button>
                            <a href="{{ route('client.report.rsmi') }}" class="btn btn-secondary"><i class="fas fa-redo"></i></a>
                        </div>
                    </div>
                    <div class="panel-grid" style="grid-template-columns:1fr 1fr 1fr; margin-top:8px;">
                        <div class="fg"><label>Position/Designation</label><input type="text" name="position" placeholder="e.g. Supply Officer" value="{{ request('position') }}"></div>
                        <div class="fg"><label>Office/Station</label><input type="text" name="office" placeholder="e.g. ATI-RTC I" value="{{ request('office') }}"></div>
                        <div class="fg"><label>Assumption Date</label><input type="date" name="assumption_date" value="{{ request('assumption_date') }}"></div>
                    </div>
                </form>
            </div>

            {{-- ══ 2. Apply Filters ══ --}}
            <div class="panel screen-only">
                <div class="panel-title"><i class="fas fa-filter"></i> Apply Filters</div>
                <form method="GET" action="{{ route('client.report.rsmi') }}">
                    <input type="hidden" name="as_of"               value="{{ request('as_of') }}">
                    <input type="hidden" name="entity_name"         value="{{ request('entity_name') }}">
                    <input type="hidden" name="fund_cluster"        value="{{ request('fund_cluster') }}">
                    <input type="hidden" name="serial_no"           value="{{ request('serial_no') }}">
                    <input type="hidden" name="date"                value="{{ request('date') }}">
                    <input type="hidden" name="accountable_person"  value="{{ request('accountable_person') }}">
                    <input type="hidden" name="position"            value="{{ request('position') }}">
                    <input type="hidden" name="office"              value="{{ request('office') }}">
                    <input type="hidden" name="assumption_date"     value="{{ request('assumption_date') }}">
                    <input type="hidden" name="f_supply_name"       value="{{ request('f_supply_name') }}">
                    <input type="hidden" name="f_supply_role"       value="{{ request('f_supply_role') }}">
                    <input type="hidden" name="f_acctg_name"        value="{{ request('f_acctg_name') }}">
                    <input type="hidden" name="f_acctg_date"        value="{{ request('f_acctg_date') }}">

                    <div class="panel-grid" style="grid-template-columns:160px 160px 1fr 160px auto;">
                        <div class="fg"><label>Date From</label><input type="date" name="date_from" value="{{ request('date_from') }}"></div>
                        <div class="fg"><label>Date To</label><input type="date" name="date_to" value="{{ request('date_to') }}"></div>
                        <div class="fg">
                            <label>Description / Item</label>
                            <select name="description">
                                <option value="">-- All Items --</option>
                                @foreach($descriptions as $desc)
                                    <option value="{{ $desc }}" {{ request('description') == $desc ? 'selected' : '' }}>{{ $desc }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="fg">
                            <label>Status</label>
                            <select name="status">
                                <option value="">-- All --</option>
                                <option value="issued"  {{ request('status') == 'issued'  ? 'selected' : '' }}>Issued</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            </select>
                        </div>
                        <div class="fg-actions">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                            <a href="{{ route('client.report.rsmi', request()->only(['as_of','entity_name','fund_cluster','serial_no','date','accountable_person','position','office','assumption_date','f_supply_name','f_supply_role','f_acctg_name','f_acctg_date'])) }}" class="btn btn-secondary"><i class="fas fa-redo"></i></a>
                        </div>
                    </div>
                </form>
            </div>

            {{-- ══ 3. Apply Footer (Signatories) ══ --}}
            <div class="panel screen-only">
                <div class="panel-title"><i class="fas fa-users"></i> Apply Footer (Signatories)</div>
                <form method="GET" action="{{ route('client.report.rsmi') }}">
                    <input type="hidden" name="as_of"               value="{{ request('as_of') }}">
                    <input type="hidden" name="entity_name"         value="{{ request('entity_name') }}">
                    <input type="hidden" name="fund_cluster"        value="{{ request('fund_cluster') }}">
                    <input type="hidden" name="serial_no"           value="{{ request('serial_no') }}">
                    <input type="hidden" name="date"                value="{{ request('date') }}">
                    <input type="hidden" name="accountable_person"  value="{{ request('accountable_person') }}">
                    <input type="hidden" name="position"            value="{{ request('position') }}">
                    <input type="hidden" name="office"              value="{{ request('office') }}">
                    <input type="hidden" name="assumption_date"     value="{{ request('assumption_date') }}">
                    <input type="hidden" name="date_from"           value="{{ request('date_from') }}">
                    <input type="hidden" name="date_to"             value="{{ request('date_to') }}">
                    <input type="hidden" name="description"         value="{{ request('description') }}">
                    <input type="hidden" name="status"              value="{{ request('status') }}">

                    <div class="footer-grid">
                        <div>
                            <span class="footer-col-label">Supply and/or Property Custodian:</span>
                            <p class="footer-sub-label">Name</p>
                            <input type="text" name="f_supply_name" value="{{ request('f_supply_name', 'FRANKLIN A. SALCEDO') }}">
                            <input type="text" name="f_supply_role" value="{{ request('f_supply_role', 'Supply and Property Custodian') }}" placeholder="Designation">
                        </div>
                        <div>
                            <span class="footer-col-label">Designated Accounting Staff:</span>
                            <p class="footer-sub-label">Name</p>
                            <input type="text" name="f_acctg_name" value="{{ request('f_acctg_name', 'ANGELIQUE I. PEÑALBA, CPA') }}">
                            <p class="footer-sub-label">Date</p>
                            <input type="date" name="f_acctg_date" value="{{ request('f_acctg_date') }}">
                        </div>
                    </div>
                    <div style="display:flex;gap:8px;margin-top:12px;">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Apply Footer</button>
                        <a href="{{ route('client.report.rsmi', request()->only(['date_from','date_to','description','status','as_of','entity_name','fund_cluster','serial_no','date','accountable_person','position','office','assumption_date'])) }}" class="btn btn-secondary"><i class="fas fa-redo"></i> Reset</a>
                    </div>
                </form>
            </div>

            @if($flat->count() > 0)

                @include('client.report._export_fab', [
                    'excelUrl' => route('client.report.rsmi.export.excel', request()->query()),
                    'pdfUrl'   => route('client.report.rsmi.export.pdf',   request()->query()),
                ])

                <div class="table-toggle-bar screen-only">
                    <span class="record-count"><i class="fas fa-list"></i>&nbsp;{{ $flat->count() }} record(s) found</span>
                    <button type="button" class="btn btn-info" onclick="toggleTable()">
                        <i class="fas fa-eye-slash" id="toggleIcon"></i> <span id="toggleLabel">Hide Table</span>
                    </button>
                </div>

                <div class="tbl-wrap screen-only" id="table-wrapper">
                    {{-- Screen-only data table --}}
                    <table class="rsmi-tbl">
                        <colgroup>
                            <col class="cA"><col class="cB"><col class="cC"><col class="cD">
                            <col class="cE"><col class="cF"><col class="cG"><col class="cH">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>RIS No.</th>
                                <th>Responsibility<br>Center Code</th>
                                <th>Stock<br>No.</th>
                                <th>Item</th>
                                <th>Unit</th>
                                <th>Quantity<br>Issued</th>
                                <th>Unit Cost</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $grandTotalQty = 0; $grandTotalAmt = 0; @endphp
                            @foreach($grouped as $group)
                                @php
                                    $rows = $group['rows'];
                                    $rowCount = count($rows);
                                    $grandTotalQty += $group['total_qty'];
                                    $grandTotalAmt += $group['total_amount'];
                                @endphp
                                @foreach($rows as $i => $item)
                                    @php
                                        $isFirst = ($i === 0);
                                        $isLast  = ($i === $rowCount - 1);
                                    @endphp
                                    <tr>
                                        <td class="tdl">{{ $item->issue_no }}</td>
                                        @if($isFirst)
                                            <td rowspan="{{ $rowCount }}">{{ $item->responsibility_center }}</td>
                                            <td rowspan="{{ $rowCount }}">{{ $item->stock_no }}</td>
                                            <td class="tdl" rowspan="{{ $rowCount }}">{{ $item->item }}</td>
                                            <td rowspan="{{ $rowCount }}">{{ $item->unit }}</td>
                                        @endif
                                        <td class="tdr">{{ $item->quantity_issued > 0 ? number_format($item->quantity_issued) : '' }}</td>
                                        <td class="tdr">{{ $isLast && $group['unit_cost'] > 0 ? number_format($group['unit_cost'], 2) : '' }}</td>
                                        <td class="tdr">{{ $isLast && $group['total_amount'] > 0 ? number_format($group['total_amount'], 2) : '' }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                            <tr>
                                <td colspan="5" class="total-label">Total</td>
                                <td class="total-val">{{ number_format($grandTotalQty) }}</td>
                                <td class="total-val"></td>
                                <td class="total-val">{{ $grandTotalAmt > 0 ? number_format($grandTotalAmt, 2) : '' }}</td>
                            </tr>
                        </tbody>
                    </table>

                    {{-- Screen: Recapitulation --}}
                    <div style="display:flex; gap:20px; margin-top:16px;">
                        <div style="flex:1;">
                            <div style="font-weight:700; font-size:12px; margin-bottom:6px;">Recapitulation:</div>
                            <table class="rsmi-tbl" style="font-size:9px;">
                                <colgroup><col style="width:50%;"><col style="width:50%;"></colgroup>
                                <thead><tr><th>Stock No.</th><th>Quantity</th></tr></thead>
                                <tbody>
                                    @foreach($recapLeft as $row)
                                    <tr><td>{{ $row['stock_no'] }}</td><td>{{ number_format($row['quantity']) }}</td></tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div style="flex:1;">
                            <div style="font-weight:700; font-size:12px; margin-bottom:6px;">Recapitulation:</div>
                            <table class="rsmi-tbl" style="font-size:9px;">
                                <colgroup><col style="width:33%;"><col style="width:34%;"><col style="width:33%;"></colgroup>
                                <thead><tr><th>Unit Cost</th><th>Total Cost</th><th>UACS Object Code</th></tr></thead>
                                <tbody>
                                    @foreach($recapRight as $row)
                                    <tr>
                                        <td class="tdr">{{ $row['unit_cost']  > 0 ? number_format($row['unit_cost'],  2) : '' }}</td>
                                        <td class="tdr">{{ $row['total_cost'] > 0 ? number_format($row['total_cost'], 2) : '' }}</td>
                                        <td>{{ $row['uacs_code'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Screen: Signature Block Preview --}}
                <div class="sig-preview screen-only">
                    <div class="sig-preview-title"><i class="fas fa-signature"></i> Signature Block Preview</div>
                    <div class="sig-grid">
                        <div class="sig-col">
                            <span class="sig-label">I hereby certify to the correctness of the above information.</span>
                            <span class="sig-name">{{ strtoupper(request('f_supply_name', 'FRANKLIN A. SALCEDO')) }}</span>
                            <span class="sig-role">{{ request('f_supply_role', 'Supply and Property Custodian') }}</span>
                        </div>
                        <div class="sig-col">
                            <span class="sig-label">Posted by:</span>
                            <span class="sig-name">{{ strtoupper(request('f_acctg_name', 'ANGELIQUE I. PEÑALBA, CPA')) }}</span>
                            @if(request('f_acctg_date'))
                                <span class="sig-role">{{ \Carbon\Carbon::parse(request('f_acctg_date'))->format('F d, Y') }}</span>
                            @endif
                        </div>
                    </div>
                </div>

            @else
                <div class="empty-state screen-only">
                    <i class="fas fa-box-open"></i>
                    <h3>No Supplies Records Found</h3>
                    <p>No records match your filters.</p>
                </div>
            @endif

        </div>
    </div>
</div>
<script>
function toggleTable() {
    const w = document.getElementById('table-wrapper');
    const i = document.getElementById('toggleIcon');
    const l = document.getElementById('toggleLabel');
    const isHidden = w.style.display === 'none';
    w.style.display = isHidden ? '' : 'none';
    i.className = isHidden ? 'fas fa-eye-slash' : 'fas fa-eye';
    l.textContent = isHidden ? 'Hide Table' : 'Show Table';
}
</script>
</body>
</html>