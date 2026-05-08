<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RPC-PPE Report</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    {{-- Font Awesome for this page's own buttons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
          crossorigin="anonymous" referrerpolicy="no-referrer">
    {{-- Ionicons: required for sidebar ion-icon web components --}}
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* ═══════════════════════════════════════════════════════════
           SIDEBAR & HEADER ICON FIX
           The sidebar uses ion-icon web components (ionicons).
           The ionicons JS is loaded above in <head> so they render.
           These rules ensure nothing from this page's CSS hides them.
           ═══════════════════════════════════════════════════════════ */

        /* ion-icon web components in sidebar */
        .sidebar ion-icon {
            display: inline-block !important;
            visibility: visible !important;
            opacity: 1 !important;
            pointer-events: none;
        }

        /* All icons, svgs, images in sidebar */
        .sidebar svg,
        .sidebar img:not([src=""]),
        .sidebar .icon,
        .sidebar .nav-icon,
        .sidebar i {
            display: inline-flex !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        /* Notification bell icon in header */
        .header ion-icon,
        .header .notif-icon ion-icon,
        .header .notification ion-icon,
        .header svg,
        .notification-count,
        .notif-icon,
        .bell-icon {
            display: inline-flex !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        /* Profile / user avatar image in header */
        .header img,
        .header .profile-pic,
        .header .user-avatar img,
        .header .avatar img,
        .user-profile img,
        .profile-image {
            display: inline-block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        /* Ensure ion-icon shadow DOM renders (Chrome/Edge) */
        ion-icon {
            --ionicon-stroke-width: 32px;
        }

        /* ── Base ─────────────────────────────────────────────────── */
        .rpc-ppe-content { padding: 20px; background: white; }

        /* Hidden on screen, shown only on print */
        .print-only { display: none !important; }

        /* ── Back button ──────────────────────────────────────────── */
        .back-button {
            display: inline-flex; align-items: center; gap: 8px;
            background: #296218; color: white; padding: 10px 20px;
            border: none; border-radius: 8px; text-decoration: none;
            font-weight: 500; transition: all .3s; margin-bottom: 20px;
        }
        .back-button:hover { background: #1e4612; color: white; }

        /* ── Report title ─────────────────────────────────────────── */
        .report-header { text-align: center; margin-bottom: 10px; }
        .report-header h1 {
            font-size: 17px; font-weight: bold; text-transform: uppercase;
            margin: 0 0 4px; color: #000;
        }
        .report-header .asof { font-size: 13px; margin: 0; color: #000; }

        /* ── Entity name box ──────────────────────────────────────── */
        .entity-name-box {
            border: 2px solid #000;
            text-align: center; font-weight: bold; font-size: 14px;
            padding: 7px 8px; margin-bottom: 16px; min-height: 32px;
        }

        /* ── Panel cards ──────────────────────────────────────────── */
        .panel {
            background: #f8f9fa; padding: 14px 16px; border-radius: 8px;
            margin-bottom: 14px; border: 1px solid #ddd;
        }
        .panel-title {
            font-size: 13px; font-weight: 700; color: #296218;
            margin: 0 0 10px; display: flex; align-items: center; gap: 6px;
        }
        .panel-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(155px, 1fr));
            gap: 10px; align-items: end;
        }
        .filter-group label {
            display: block; font-size: 12px; font-weight: 600;
            color: #495057; margin-bottom: 4px;
        }
        .filter-group select,
        .filter-group input {
            width: 100%; padding: 6px 10px;
            border: 1px solid #ccc; border-radius: 4px; font-size: 13px;
        }
        .filter-actions { display: flex; gap: 8px; align-items: flex-end; flex-wrap: wrap; }

        /* ── Buttons ──────────────────────────────────────────────── */
        .btn {
            padding: 6px 14px; border: none; border-radius: 4px;
            font-size: 13px; font-weight: 500; cursor: pointer;
            transition: all .3s; text-decoration: none;
            display: inline-flex; align-items: center; gap: 6px;
        }
        .btn-primary   { background: #296218; color: white; }
        .btn-primary:hover  { background: #1e4612; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-secondary:hover { background: #545b62; color: white; }
        .btn-info      { background: #0d6efd; color: white; }
        .btn-info:hover { background: #0b5ed7; }

        /* ── Table toggle bar ─────────────────────────────────────── */
        .table-toggle-bar {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 8px;
        }
        .record-count { font-size: 13px; color: #555; }

        /* ── Equipment table ──────────────────────────────────────── */
        .equipment-table {
            width: 100%; border-collapse: collapse;
            font-size: 11px; border: 2px solid #000;
        }
        .equipment-table th {
            padding: 8px 6px; text-align: center; font-weight: bold;
            border: 1px solid #000; background: #d9d9d9;
            font-size: 10px; text-transform: uppercase; vertical-align: middle;
            -webkit-print-color-adjust: exact; print-color-adjust: exact;
        }
        .equipment-table td {
            padding: 7px 6px; border: 1px solid #000;
            vertical-align: top; font-size: 11px;
        }
        .classification-header-row td {
            background: #9DC3E6 !important; font-weight: bold;
            font-size: 12px; padding: 8px 10px; text-align: center;
            -webkit-print-color-adjust: exact; print-color-adjust: exact;
        }
        .text-center { text-align: center; }
        .text-right  { text-align: right; }

        /* ── Signature preview (screen only) ─────────────────────── */
        .sig-preview {
            margin-top: 20px; padding: 16px;
            border: 1px solid #ddd; border-radius: 8px; background: #fafafa;
        }
        .sig-preview-title { font-weight: 700; color: #296218; margin-bottom: 12px; font-size: 13px; }
        .sig-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }
        .sig-col { display: flex; flex-direction: column; gap: 2px; }
        .sig-col .sig-label  { font-size: 11px; color: #666; margin-bottom: 4px; }
        .sig-col .sig-name   { font-weight: bold; font-size: 12px; }
        .sig-col .sig-role   { font-size: 11px; color: #444; }
        .sig-col .sig-name2  { font-weight: bold; font-size: 12px; margin-top: 8px; }

        /* ── Empty state ──────────────────────────────────────────── */
        .empty-state { text-align: center; padding: 60px 20px; color: #6c757d; }
        .empty-state i { font-size: 64px; color: #dee2e6; margin-bottom: 20px; }

        /* ═══════════════════════════ PRINT STYLES ════════════════════
           Matches the exported PDF and sample Excel exactly.
           Margins = exact sample: left=0.54in right=0.09in top=0.25in bottom=0.38in
           Column widths proportional to sample Excel columns (203.125 total units).
           ═══════════════════════════════════════════════════════════ */
        @media print {
            @page {
                size: 257mm 182mm;
                margin: 0.25in 0.09in 0.38in 0.54in;
            }

            * { box-sizing: border-box !important; }
            html, body {
                width: 100% !important; margin: 0 !important;
                padding: 0 !important; background: white !important;
                font-family: Calibri, Arial, sans-serif !important;
            }

            /* Hide all UI chrome + screen-only helpers */
            .back-button, .panel, .table-toggle-bar,
            .export-fab, .fab, .fab-print, .fab-pdf, .fab-excel,
            .sig-preview,
            .sidebar, .header, .navbar, .navigation, .brand,
            .dashboard-header, .header-left, .header-right,
            .notifications, .user-profile, .user-avatar, .user-info,
            .system-title, .profile-pic, .brand-logo, .nav-icons,
            .screen-only, .print-ati-divider, .print-footer {
                display: none !important;
            }

            .container, .details {
                margin: 0 !important; padding: 0 !important;
                width: 100% !important; left: 0 !important;
            }
            .rpc-ppe-content { padding: 0 !important; width: 100% !important; }
            #table-wrapper { display: block !important; }
            .print-only { display: block !important; }

            /* ATI header: image centered on cols D-I (left 31.5%, width 37.1%) */
            .print-ati-header {
                display: block !important; width: 100%; margin-bottom: 2pt;
            }
            .print-ati-header img {
                display: block !important;
                margin-left: 31.5% !important;
                width: 37.1% !important;
                height: auto !important;
            }
            /* Green divider line */
            .print-ati-line {
                display: block !important;
                border-top: 2pt solid #2d6a2d;
                margin-bottom: 3pt;
            }

            /* Report title: 9pt bold */
            .report-header { text-align: center; margin-bottom: 0; }
            .report-header h1 {
                font-family: Calibri, Arial, sans-serif !important;
                font-size: 9pt !important; font-weight: bold !important;
                text-transform: uppercase !important;
                margin: 0 !important; line-height: 1.2 !important;
            }
            .report-header .asof {
                font-family: Calibri, Arial, sans-serif !important;
                font-size: 8.5pt !important; margin: 0 0 1pt !important;
            }

            /* Entity name box: FULL border all 4 sides, 8.5pt */
            .entity-name-box {
                border: 1.5pt solid #000 !important;
                text-align: center !important;
                font-family: Calibri, Arial, sans-serif !important;
                font-weight: bold !important;
                font-size: 8.5pt !important;
                padding: 2pt !important;
                margin-bottom: 2pt !important;
                line-height: 1.3 !important;
            }

            /* Equipment table: 5.5pt — smaller so all text is readable */
            .equipment-table {
                width: 100% !important;
                font-family: Calibri, Arial, sans-serif !important;
                font-size: 5.5pt !important;
                border-collapse: collapse !important;
                table-layout: fixed !important;
                margin-bottom: 3pt !important;
            }
            .equipment-table thead tr:first-child th { height: 24pt !important; }
            .equipment-table thead tr:last-child  th { height: 20pt !important; }
            .equipment-table th {
                padding: 1pt !important;
                font-family: Calibri, Arial, sans-serif !important;
                font-size: 5.5pt !important; font-weight: bold !important;
                border: 0.5pt solid #000 !important;
                background: white !important;
                text-align: center !important; vertical-align: middle !important;
                word-wrap: break-word !important; overflow-wrap: break-word !important;
                white-space: normal !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .equipment-table tbody tr { height: 15pt !important; }
            .equipment-table td {
                padding: 1pt !important;
                border: 0.5pt solid #000 !important;
                background: white !important;
                font-family: Calibri, Arial, sans-serif !important;
                font-size: 5.5pt !important;
                word-wrap: break-word !important; overflow-wrap: break-word !important;
                white-space: normal !important; vertical-align: middle !important;
            }
            /* Classification banner: no color, plain bold */
            .classification-header-row td {
                background: white !important;
                font-weight: bold !important; font-size: 6pt !important;
                text-align: center !important; padding: 1.5pt !important;
                border: 0.5pt solid #000 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /* Column widths via colgroup (bypasses rowspan/colspan nth-child issues) */
            .equipment-table col.col-a { width: 8.65%  !important; }
            .equipment-table col.col-b { width: 15.96% !important; }
            .equipment-table col.col-c { width: 6.89%  !important; }
            .equipment-table col.col-d { width: 5.98%  !important; }
            .equipment-table col.col-e { width: 6.68%  !important; }
            .equipment-table col.col-f { width: 6.40%  !important; }
            .equipment-table col.col-g { width: 7.03%  !important; }
            .equipment-table col.col-h { width: 6.40%  !important; }
            .equipment-table col.col-i { width: 4.57%  !important; }
            .equipment-table col.col-j { width: 4.50%  !important; }
            .equipment-table col.col-k { width: 10.41% !important; }
            .equipment-table col.col-l { width: 8.93%  !important; }
            .equipment-table col.col-m { width: 7.59%  !important; }

            /* Signature block: 6pt names, 5.5pt roles */
            .print-sig {
                display: block !important;
                margin-top: 4pt; width: 100%;
                font-family: Calibri, Arial, sans-serif !important;
            }
            .print-sig > table { width: 100%; border-collapse: collapse; table-layout: fixed; }
            .print-sig > table > tr > td {
                vertical-align: top; padding: 0 2pt 0 0;
                font-family: Calibri, Arial, sans-serif !important; border: none;
            }
            .print-sig .sig-inner { width: 100%; border-collapse: collapse; table-layout: fixed; }
            .print-sig .sig-inner td { vertical-align: top; border: none; padding: 0; font-size: 6pt; }
            .print-sig .name-bold { font-weight: bold; font-size: 6pt; display: block; }
            .print-sig .role-text  { font-size: 5.5pt; display: block; }
        }
    </style>
</head>
<body>
<div class="container">
    @include('layouts.core.sidebar')
    <div class="details">
        @include('layouts.core.header')

        <div class="rpc-ppe-content">

            {{-- Back button (screen only) --}}
            <a href="{{ route('client.reports.index') }}" class="back-button">
                <i class="fas fa-arrow-left"></i> Back to Reports
            </a>

            {{-- ══ ATI Header — HIDDEN on screen, SHOWN on print ══ --}}
            <div class="print-only print-ati-header">
                <img src="{{ asset('assets/img/ati_header.jpg') }}" alt="">
            </div>
            <div class="print-only print-ati-line"></div>

            {{-- ══ Report Title ══ --}}
            <div class="report-header">
                <h1>REPORT ON THE PHYSICAL COUNT OF PROPERTY PLANT AND EQUIPMENT</h1>
                <p class="asof">
                    as of {!! $header['as_of'] !== ''
                        ? e($header['as_of'])
                        : '<span class="screen-only" style="border-bottom:1px solid #000;padding:0 50px;display:inline-block;">&nbsp;</span>' !!}
                </p>
            </div>

            {{-- ══ Entity name box ══ --}}
            <div class="entity-name-box">
                {!! $header['entity_name'] !== ''
                    ? e(strtoupper($header['entity_name']))
                    : '<span class="screen-only" style="color:#aaa;font-weight:400;font-size:13px;">— set entity name in Apply Header —</span>' !!}
            </div>

            {{-- ══ Apply Header Panel ══ --}}
            <div class="panel">
                <div class="panel-title"><i class="fas fa-heading"></i> Apply Header</div>
                <form method="GET" action="{{ route('client.report.rpc-ppe') }}" class="panel-grid">
                    <input type="hidden" name="classification" value="{{ request('classification') }}">
                    <input type="hidden" name="condition"      value="{{ request('condition') }}">
                    <input type="hidden" name="date_from"      value="{{ request('date_from') }}">
                    <input type="hidden" name="date_to"        value="{{ request('date_to') }}">
                    <input type="hidden" name="f_cc1_name" value="{{ request('f_cc1_name') }}">
                    <input type="hidden" name="f_cc1_role" value="{{ request('f_cc1_role') }}">
                    <input type="hidden" name="f_cc2_name" value="{{ request('f_cc2_name') }}">
                    <input type="hidden" name="f_cc2_role" value="{{ request('f_cc2_role') }}">
                    <input type="hidden" name="f_cc3_name" value="{{ request('f_cc3_name') }}">
                    <input type="hidden" name="f_cc3_role" value="{{ request('f_cc3_role') }}">
                    <input type="hidden" name="f_cc4_name" value="{{ request('f_cc4_name') }}">
                    <input type="hidden" name="f_cc4_role" value="{{ request('f_cc4_role') }}">
                    <input type="hidden" name="f_ab_name"  value="{{ request('f_ab_name') }}">
                    <input type="hidden" name="f_ab_role"  value="{{ request('f_ab_role') }}">
                    <input type="hidden" name="f_vb_name"  value="{{ request('f_vb_name') }}">
                    <input type="hidden" name="f_vb_role"  value="{{ request('f_vb_role') }}">
                    <input type="hidden" name="f_vb_role2" value="{{ request('f_vb_role2') }}">

                    <div class="filter-group">
                        <label>As of</label>
                        <input type="date" name="as_of" value="{{ request('as_of') }}">
                    </div>
                    <div class="filter-group" style="min-width:220px;">
                        <label>Entity / Place Name</label>
                        <input type="text" name="entity_name"
                               placeholder="e.g. STA. BARBARA, PANGASINAN"
                               value="{{ request('entity_name') }}">
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check"></i> Apply Header
                        </button>
                        <a href="{{ route('client.report.rpc-ppe', array_merge(request()->except(['as_of','entity_name']), ['as_of'=>'','entity_name'=>''])) }}"
                           class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Clear
                        </a>
                    </div>
                </form>
            </div>

            {{-- ══ Apply Filters Panel ══ --}}
            <div class="panel">
                <div class="panel-title"><i class="fas fa-filter"></i> Apply Filters</div>
                <form method="GET" action="{{ route('client.report.rpc-ppe') }}" class="panel-grid">
                    <input type="hidden" name="as_of"        value="{{ request('as_of') }}">
                    <input type="hidden" name="entity_name"  value="{{ request('entity_name') }}">
                    <input type="hidden" name="f_cc1_name" value="{{ request('f_cc1_name') }}">
                    <input type="hidden" name="f_cc1_role" value="{{ request('f_cc1_role') }}">
                    <input type="hidden" name="f_cc2_name" value="{{ request('f_cc2_name') }}">
                    <input type="hidden" name="f_cc2_role" value="{{ request('f_cc2_role') }}">
                    <input type="hidden" name="f_cc3_name" value="{{ request('f_cc3_name') }}">
                    <input type="hidden" name="f_cc3_role" value="{{ request('f_cc3_role') }}">
                    <input type="hidden" name="f_cc4_name" value="{{ request('f_cc4_name') }}">
                    <input type="hidden" name="f_cc4_role" value="{{ request('f_cc4_role') }}">
                    <input type="hidden" name="f_ab_name"  value="{{ request('f_ab_name') }}">
                    <input type="hidden" name="f_ab_role"  value="{{ request('f_ab_role') }}">
                    <input type="hidden" name="f_vb_name"  value="{{ request('f_vb_name') }}">
                    <input type="hidden" name="f_vb_role"  value="{{ request('f_vb_role') }}">
                    <input type="hidden" name="f_vb_role2" value="{{ request('f_vb_role2') }}">

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
                                <option value="{{ $class }}"
                                    {{ request('classification') == $class ? 'selected' : '' }}>
                                    {{ $class }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Condition</label>
                        <select name="condition">
                            <option value="">All Conditions</option>
                            <option value="Serviceable"
                                {{ request('condition') == 'Serviceable' ? 'selected' : '' }}>Serviceable</option>
                            <option value="Unserviceable"
                                {{ request('condition') == 'Unserviceable' ? 'selected' : '' }}>Unserviceable</option>
                        </select>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Apply Filters
                        </button>
                        <a href="{{ route('client.report.rpc-ppe', ['as_of'=>request('as_of'),'entity_name'=>request('entity_name')]) }}"
                           class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset Filters
                        </a>
                    </div>
                </form>
            </div>

            {{-- ══ Apply Footer Panel ══ --}}
            <div class="panel">
                <div class="panel-title"><i class="fas fa-signature"></i> Apply Footer (Signatories)</div>
                <form method="GET" action="{{ route('client.report.rpc-ppe') }}">
                    <input type="hidden" name="as_of"          value="{{ request('as_of') }}">
                    <input type="hidden" name="entity_name"    value="{{ request('entity_name') }}">
                    <input type="hidden" name="classification" value="{{ request('classification') }}">
                    <input type="hidden" name="condition"      value="{{ request('condition') }}">
                    <input type="hidden" name="date_from"      value="{{ request('date_from') }}">
                    <input type="hidden" name="date_to"        value="{{ request('date_to') }}">

                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-bottom:12px;">

                        {{-- Certified Correct by --}}
                        <div>
                            <p style="font-size:12px;font-weight:700;margin:0 0 8px;color:#296218;">Certified Correct by:</p>
                            <div style="margin-bottom:6px;">
                                <label style="font-size:11px;font-weight:600;color:#495057;display:block;margin-bottom:2px;">Name 1</label>
                                <input type="text" name="f_cc1_name"
                                       value="{{ request('f_cc1_name', 'FRANKLIN A. SALCEDO') }}"
                                       style="width:100%;padding:5px 8px;border:1px solid #ccc;border-radius:4px;font-size:12px;">
                                <input type="text" name="f_cc1_role"
                                       value="{{ request('f_cc1_role', 'Inventory Committee - Chairman') }}"
                                       style="width:100%;padding:5px 8px;border:1px solid #ccc;border-radius:4px;font-size:12px;margin-top:3px;"
                                       placeholder="Role">
                            </div>
                            <div style="margin-bottom:6px;">
                                <label style="font-size:11px;font-weight:600;color:#495057;display:block;margin-bottom:2px;">Name 2</label>
                                <input type="text" name="f_cc2_name"
                                       value="{{ request('f_cc2_name', 'ALYSSA MAE M. ESTRADA') }}"
                                       style="width:100%;padding:5px 8px;border:1px solid #ccc;border-radius:4px;font-size:12px;">
                                <input type="text" name="f_cc2_role"
                                       value="{{ request('f_cc2_role', 'Inventory Committee - Member') }}"
                                       style="width:100%;padding:5px 8px;border:1px solid #ccc;border-radius:4px;font-size:12px;margin-top:3px;"
                                       placeholder="Role">
                            </div>
                            <div style="margin-bottom:6px;">
                                <label style="font-size:11px;font-weight:600;color:#495057;display:block;margin-bottom:2px;">Name 3</label>
                                <input type="text" name="f_cc3_name"
                                       value="{{ request('f_cc3_name', 'AMOR JOYCE M. MARCELO, CPA') }}"
                                       style="width:100%;padding:5px 8px;border:1px solid #ccc;border-radius:4px;font-size:12px;">
                                <input type="text" name="f_cc3_role"
                                       value="{{ request('f_cc3_role', 'Inventory Committee - Member') }}"
                                       style="width:100%;padding:5px 8px;border:1px solid #ccc;border-radius:4px;font-size:12px;margin-top:3px;"
                                       placeholder="Role">
                            </div>
                            <div>
                                <label style="font-size:11px;font-weight:600;color:#495057;display:block;margin-bottom:2px;">Name 4</label>
                                <input type="text" name="f_cc4_name"
                                       value="{{ request('f_cc4_name', 'ANGELIQUE I. PENALBA, CPA') }}"
                                       style="width:100%;padding:5px 8px;border:1px solid #ccc;border-radius:4px;font-size:12px;">
                                <input type="text" name="f_cc4_role"
                                       value="{{ request('f_cc4_role', 'Inventory Committee - Member') }}"
                                       style="width:100%;padding:5px 8px;border:1px solid #ccc;border-radius:4px;font-size:12px;margin-top:3px;"
                                       placeholder="Role">
                            </div>
                        </div>

                        {{-- Approved by --}}
                        <div>
                            <p style="font-size:12px;font-weight:700;margin:0 0 8px;color:#296218;">Approved by:</p>
                            <div>
                                <label style="font-size:11px;font-weight:600;color:#495057;display:block;margin-bottom:2px;">Name</label>
                                <input type="text" name="f_ab_name"
                                       value="{{ request('f_ab_name', 'JOSEPHINE K. ABEN, Ph.D.') }}"
                                       style="width:100%;padding:5px 8px;border:1px solid #ccc;border-radius:4px;font-size:12px;">
                                <input type="text" name="f_ab_role"
                                       value="{{ request('f_ab_role', 'Assistant Center Director / Authorized Representative') }}"
                                       style="width:100%;padding:5px 8px;border:1px solid #ccc;border-radius:4px;font-size:12px;margin-top:3px;"
                                       placeholder="Role">
                            </div>
                        </div>

                        {{-- Verified by --}}
                        <div>
                            <p style="font-size:12px;font-weight:700;margin:0 0 8px;color:#296218;">Verified by:</p>
                            <div>
                                <label style="font-size:11px;font-weight:600;color:#495057;display:block;margin-bottom:2px;">Name</label>
                                <input type="text" name="f_vb_name"
                                       value="{{ request('f_vb_name', 'JELANIE S. WANAWAN') }}"
                                       style="width:100%;padding:5px 8px;border:1px solid #ccc;border-radius:4px;font-size:12px;">
                                <input type="text" name="f_vb_role"
                                       value="{{ request('f_vb_role', 'State Auditor II') }}"
                                       style="width:100%;padding:5px 8px;border:1px solid #ccc;border-radius:4px;font-size:12px;margin-top:3px;"
                                       placeholder="Primary Role">
                                <input type="text" name="f_vb_role2"
                                       value="{{ request('f_vb_role2', 'OIC - Audit Team Leader') }}"
                                       style="width:100%;padding:5px 8px;border:1px solid #ccc;border-radius:4px;font-size:12px;margin-top:3px;"
                                       placeholder="Secondary Role">
                            </div>
                        </div>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check"></i> Apply Footer
                        </button>
                        <a href="{{ route('client.report.rpc-ppe',
                                array_merge(request()->except(['f_cc1_name','f_cc1_role','f_cc2_name','f_cc2_role',
                                                               'f_cc3_name','f_cc3_role','f_cc4_name','f_cc4_role',
                                                               'f_ab_name','f_ab_role','f_vb_name','f_vb_role','f_vb_role2']))) }}"
                           class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset Footer
                        </a>
                    </div>
                </form>
            </div>

            {{-- ══ Equipment Table ══ --}}
            @if($groupedEquipment->count() > 0)

                <div class="table-toggle-bar">
                    <span class="record-count">
                        <i class="fas fa-list"></i>
                        {{ $groupedEquipment->flatten()->count() }} record(s) found
                    </span>
                    <button class="btn btn-info" onclick="toggleTable()">
                        <i class="fas fa-eye-slash" id="toggleIcon"></i>
                        <span id="toggleLabel">Hide Table</span>
                    </button>
                </div>

                <div id="table-wrapper">
                    <table class="equipment-table">
                        <colgroup>
                            <col class="col-a"><col class="col-b"><col class="col-c">
                            <col class="col-d"><col class="col-e"><col class="col-f">
                            <col class="col-g"><col class="col-h"><col class="col-i">
                            <col class="col-j"><col class="col-k"><col class="col-l">
                            <col class="col-m">
                        </colgroup>
                        <thead>
                            <tr>
                                <th rowspan="2">ARTICLE</th>
                                <th rowspan="2">DESCRIPTION</th>
                                <th rowspan="2">PROPERTY NUMBER</th>
                                <th rowspan="2">UNIT OF MEASURE</th>
                                <th rowspan="2">UNIT VALUE</th>
                                <th rowspan="2">Acquisition Date</th>
                                <th rowspan="2">QUANTITY per PROPERTY CARD</th>
                                <th rowspan="2">QUANTITY per PHYSICAL COUNT</th>
                                <th colspan="2">SHORTAGE/OVERAGE</th>
                                <th colspan="3">REMARKS</th>
                            </tr>
                            <tr>
                                <th>Quantity</th>
                                <th>Value</th>
                                <th style="text-align:right;">Person Responsible</th>
                                <th style="text-align:left;">/Responsibility Center</th>
                                <th>Condition of Properties</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($groupedEquipment as $classification => $equipmentItems)
                                <tr class="classification-header-row">
                                    <td colspan="13">
                                        {{ strtoupper($classification ?: 'UNCLASSIFIED EQUIPMENT') }}
                                    </td>
                                </tr>
                                @foreach($equipmentItems->groupBy('article') as $article => $items)
                                    @foreach($items as $index => $equipment)
                                        <tr>
                                            @if($index === 0)
                                                <td rowspan="{{ $items->count() }}"
                                                    style="vertical-align:middle;font-weight:bold;text-align:center;">
                                                    {{ $article }}
                                                </td>
                                            @endif
                                            <td>{{ $equipment->description ?: '' }}</td>
                                            <td class="text-center">{{ $equipment->property_number ?: '-' }}</td>
                                            <td class="text-center">{{ $equipment->unit_of_measurement ?: '' }}</td>
                                            <td class="text-right">
                                                {{ $equipment->unit_value !== null ? number_format($equipment->unit_value, 2) : '' }}
                                            </td>
                                            <td class="text-center">
                                                {{ $equipment->acquisition_date ? $equipment->acquisition_date->format('M-d') : '' }}
                                            </td>
                                            <td class="text-center">
                                                {{ ($equipment->property_number && $equipment->property_number !== '-') ? 1 : '' }}
                                            </td>
                                            <td class="text-center">
                                                {{ ($equipment->property_number && $equipment->property_number !== '-') ? 1 : '' }}
                                            </td>
                                            <td class="text-center"></td>
                                            <td class="text-center"></td>
                                            <td>{{ $equipment->responsible_person ?: 'Unknown / Book of the Accountant' }}</td>
                                            <td class="text-center">{{ $equipment->location ?: '' }}</td>
                                            <td class="text-center">{{ $equipment->condition ?: '' }}</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Signature preview (screen only) --}}
                <div class="sig-preview">
                    <div class="sig-preview-title"><i class="fas fa-signature"></i> Signature Block Preview</div>
                    <div class="sig-grid">
                        <div class="sig-col">
                            <span class="sig-label">Certified Correct by:</span>
                            <span class="sig-name">{{ request('f_cc1_name', 'FRANKLIN A. SALCEDO') }}</span>
                            <span class="sig-role">{{ request('f_cc1_role', 'Inventory Committee - Chairman') }}</span>
                            <span class="sig-name2">{{ request('f_cc2_name', 'ALYSSA MAE M. ESTRADA') }}</span>
                            <span class="sig-role">{{ request('f_cc2_role', 'Inventory Committee - Member') }}</span>
                            <span class="sig-name2">{{ request('f_cc3_name', 'AMOR JOYCE M. MARCELO, CPA') }}</span>
                            <span class="sig-role">{{ request('f_cc3_role', 'Inventory Committee - Member') }}</span>
                            <span class="sig-name2">{{ request('f_cc4_name', 'ANGELIQUE I. PENALBA, CPA') }}</span>
                            <span class="sig-role">{{ request('f_cc4_role', 'Inventory Committee - Member') }}</span>
                        </div>
                        <div class="sig-col">
                            <span class="sig-label">Approved by:</span>
                            <span class="sig-name">{{ request('f_ab_name', 'JOSEPHINE K. ABEN, Ph.D.') }}</span>
                            <span class="sig-role">{{ request('f_ab_role', 'Assistant Center Director / Authorized Representative') }}</span>
                        </div>
                        <div class="sig-col">
                            <span class="sig-label">Verified by:</span>
                            <span class="sig-name">{{ request('f_vb_name', 'JELANIE S. WANAWAN') }}</span>
                            <span class="sig-role">{{ request('f_vb_role', 'State Auditor II') }}</span>
                            <span class="sig-role">{{ request('f_vb_role2', 'OIC - Audit Team Leader') }}</span>
                        </div>
                    </div>
                </div>

                @include('client.report._export_fab', [
                    'excelUrl' => route('client.report.rpc-ppe.export.excel', request()->query()),
                    'pdfUrl'   => route('client.report.rpc-ppe.export.pdf',   request()->query()),
                ])

            @else
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <h3>No Equipment Found</h3>
                    <p>No equipment records to display. Try adjusting your filters.</p>
                </div>
            @endif

            {{-- ══ PRINT ONLY: Signature block ══ --}}
            <div class="print-only print-sig">
                {{-- Outer 3-column table: [Certified Correct ~47%] [Approved ~25%] [Verified ~28%] --}}
                <table>
                    <tr>
                        <td style="width:47%; padding-bottom:12pt; font-size:6pt;">Certified Correct by:</td>
                        <td style="width:25%; padding-bottom:12pt; font-size:6pt;">Approved by:</td>
                        <td style="width:28%; padding-bottom:12pt; font-size:6pt;">Verified by:</td>
                    </tr>
                    <tr>
                        {{-- CC column: nested 2-col table so cc1 & cc2 are truly side-by-side --}}
                        <td style="vertical-align:top;">
                            <table class="sig-inner">
                                <tr>
                                    <td style="width:50%;">
                                        <span class="name-bold">{{ request('f_cc1_name','FRANKLIN A. SALCEDO') }}</span>
                                        <span class="role-text">{{ request('f_cc1_role','Inventory Committee - Chairman') }}</span>
                                    </td>
                                    <td style="width:50%;">
                                        <span class="name-bold">{{ request('f_cc2_name','ALYSSA MAE M. ESTRADA') }}</span>
                                        <span class="role-text">{{ request('f_cc2_role','Inventory Committee - Member') }}</span>
                                    </td>
                                </tr>
                                @if(request('f_cc3_name','AMOR JOYCE M. MARCELO, CPA'))
                                <tr>
                                    <td style="padding-top:6pt;">
                                        <span class="name-bold">{{ request('f_cc3_name','AMOR JOYCE M. MARCELO, CPA') }}</span>
                                        <span class="role-text">{{ request('f_cc3_role','Inventory Committee - Member') }}</span>
                                    </td>
                                    @if(request('f_cc4_name','ANGELIQUE I. PENALBA, CPA'))
                                    <td style="padding-top:6pt;">
                                        <span class="name-bold">{{ request('f_cc4_name','ANGELIQUE I. PENALBA, CPA') }}</span>
                                        <span class="role-text">{{ request('f_cc4_role','Inventory Committee - Member') }}</span>
                                    </td>
                                    @endif
                                </tr>
                                @endif
                            </table>
                        </td>
                        {{-- Approved by --}}
                        <td style="vertical-align:top;">
                            <span class="name-bold">{{ request('f_ab_name','JOSEPHINE K. ABEN, Ph.D.') }}</span>
                            <span class="role-text">{{ request('f_ab_role','Assistant Center Director / Authorized Representative') }}</span>
                        </td>
                        {{-- Verified by --}}
                        <td style="vertical-align:top;">
                            <span class="name-bold">{{ request('f_vb_name','JELANIE S. WANAWAN') }}</span>
                            <span class="role-text">{{ request('f_vb_role','State Auditor II') }}</span>
                            @if(request('f_vb_role2','OIC - Audit Team Leader'))
                            <span class="role-text">{{ request('f_vb_role2','OIC - Audit Team Leader') }}</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>


        </div>
    </div>
</div>

<script>
    function toggleTable() {
        const wrapper = document.getElementById('table-wrapper');
        const icon    = document.getElementById('toggleIcon');
        const label   = document.getElementById('toggleLabel');
        const hidden  = wrapper.style.display === 'none';
        wrapper.style.display = hidden ? '' : 'none';
        icon.className  = hidden ? 'fas fa-eye-slash' : 'fas fa-eye';
        label.textContent = hidden ? 'Hide Table' : 'View Table';
    }
</script>
</body>
</html>