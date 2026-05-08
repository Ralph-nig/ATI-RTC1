<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INVENTORY AND INSPECTION REPORT OF UNSERVICEABLE PROPERTY</title>
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
        .footer-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }
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

        /* ── IIRUP table (screen) — full-width, fits page, no scroll ── */
        .tbl-wrap { overflow-x: visible; width: 100%; }
        table.iirup-tbl {
            width: 100%; border-collapse: collapse;
            font-size: 9.5px; table-layout: fixed;
        }
        table.iirup-tbl th, table.iirup-tbl td {
            border: 1px solid #000; padding: 3px 3px;
            text-align: center; vertical-align: middle;
            word-wrap: break-word; overflow-wrap: break-word;
            white-space: normal; line-height: 1.25;
        }
        table.iirup-tbl th {
            font-weight: bold; background: #d9d9d9;
            -webkit-print-color-adjust: exact; print-color-adjust: exact;
            overflow-wrap: normal; word-break: keep-all; hyphens: none;
        }
        table.iirup-tbl td.tdl { text-align: left; }
        table.iirup-tbl td.tdr { text-align: right; }
        table.iirup-tbl tr.row-total td { font-weight: bold; background: #f5f5f5; }
        table.iirup-tbl tr.banner td { font-weight: bold; background: #e8f5e9; text-align: center; }

        /* Column widths — balanced so every header word fits without mid-word breaks */
        table.iirup-tbl col.cB  { width: 5.5%; }    /* Date Acquired */
        table.iirup-tbl col.cC  { width: 14.0%; }   /* Particulars/Articles */
        table.iirup-tbl col.cD  { width: 6.5%; }    /* Property No. */
        table.iirup-tbl col.cE  { width: 2.5%; }    /* Qty */
        table.iirup-tbl col.cF  { width: 6.0%; }    /* Unit Cost */
        table.iirup-tbl col.cG  { width: 6.5%; }    /* Total Cost */
        table.iirup-tbl col.cH  { width: 7.5%; }    /* Accum Depreciation */
        table.iirup-tbl col.cI  { width: 7.0%; }    /* Accum Impairment Losses */
        table.iirup-tbl col.cJ  { width: 6.5%; }    /* Carrying Amount */
        table.iirup-tbl col.cKL { width: 9.0%; }    /* Remarks */
        table.iirup-tbl col.cM  { width: 3.5%; }    /* Sale */
        table.iirup-tbl col.cN  { width: 5.5%; }    /* Transfer */
        table.iirup-tbl col.cO  { width: 6.0%; }    /* Destruction */
        table.iirup-tbl col.cP  { width: 6.0%; }    /* Others (Specify) */
        table.iirup-tbl col.cQ  { width: 5.0%; }    /* Total */
        table.iirup-tbl col.cR  { width: 5.5%; }    /* Appraised Value */
        table.iirup-tbl col.cS  { width: 4.0%; }    /* OR No. */
        table.iirup-tbl col.cT  { width: 4.0%; }    /* Amount */

        /* ── Sig preview ── */
        .sig-preview { margin-top: 20px; padding: 16px; border: 1px solid #ddd; border-radius: 8px; background: #fafafa; }
        .sig-preview-title { font-weight: 700; color: #296218; margin-bottom: 12px; font-size: 13px; }
        .sig-grid { display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 16px; }
        .sig-col { display: flex; flex-direction: column; gap: 2px; }
        .sig-col .sig-label { font-size: 11px; color: #666; margin-bottom: 4px; font-style: italic; }
        .sig-col .sig-name  { font-weight: bold; font-size: 12px; }
        .sig-col .sig-role  { font-size: 11px; color: #444; }

        /* ── Empty state ── */
        .empty-state { text-align: center; padding: 60px 20px; color: #6c757d; }
        .empty-state i { font-size: 64px; color: #dee2e6; margin-bottom: 20px; display: block; }

        /* ═══════════════════════════════════════════════════════════════
           PRINT STYLES — exact match to IIRUP-2025-Pangasinan.xls
           A4 landscape, Arial font, proportional column widths
           ═══════════════════════════════════════════════════════════════ */
        @media print {
            /* A4 landscape — margins mirror XLS page setup (top/bottom 0.25in, left/right 0.3in) */
            @page { size: 297mm 210mm; margin: 6mm 8mm 6mm 8mm; }

            * { box-sizing: border-box !important; }
            html, body {
                width: 100% !important; margin: 0 !important; padding: 0 !important;
                background: white !important;
                font-family: Arial, sans-serif !important;
                font-size: 10pt !important;
                color: #000 !important;
            }

            /* ── Hide all screen UI chrome ── */
            .back-button, .panel, .table-toggle-bar, .export-fab, .fab,
            .sig-preview, .sidebar, .header, .navbar, .navigation,
            .dashboard-header, .header-left, .header-right,
            .notifications, .user-profile, .user-avatar,
            .screen-only, .btn { display: none !important; }

            .container, .details { margin: 0 !important; padding: 0 !important; width: 100% !important; left: 0 !important; }
            .iirup-content { padding: 0 !important; width: 100% !important; }
            #table-wrapper { display: block !important; }
            .print-only { display: block !important; }

            /* No ATI header image — matches XLS which has none */
            .print-ati-header { display: none !important; }
            .print-ati-line   { display: none !important; }

            /* ── Row 1: Appendix 74 — right-aligned, 7pt ── */
            .print-appendix {
                display: block !important;
                text-align: right;
                font-size: 7pt;
                font-family: Arial, sans-serif;
                margin-bottom: 1pt;
                line-height: 1.2;
            }

            /* ── Row 3: Title — Times New Roman 14pt bold centered uppercase ── */
            .print-title {
                display: block !important;
                text-align: center;
                font-family: 'Times New Roman', serif !important;
                font-size: 14pt !important;
                font-weight: bold !important;
                text-transform: uppercase;
                margin-bottom: 2pt;
                line-height: 1.2;
            }

            /* ── Row 4: As of — Times New Roman 12pt centered ── */
            .print-asof {
                display: block !important;
                text-align: center;
                font-family: 'Times New Roman', serif !important;
                font-size: 12pt !important;
                margin-bottom: 4pt;
            }

            /* ── Row 6: Entity / Fund Cluster ── */
            .print-meta { display: block !important; margin-bottom: 2pt; }
            .print-meta table { width: 100%; border-collapse: collapse; }
            .print-meta td { border: none; padding: 0; font-size: 8pt; font-weight: bold; vertical-align: bottom; }

            /* ── Row 7: Accountable Officer — underlined cells ── */
            .print-officer-wrap { display: block !important; margin-bottom: 0; }
            .print-officer-wrap table { width: 100%; border-collapse: collapse; }
            .print-officer-wrap td { border: none; padding: 0 2pt; font-size: 8pt; text-align: center; vertical-align: bottom; }
            .print-officer-wrap .ul { border-bottom: 0.5pt solid #000; }

            /* ── Row 8: Italic labels ── */
            .print-label-wrap { display: block !important; margin-bottom: 5pt; }
            .print-label-wrap table { width: 100%; border-collapse: collapse; }
            .print-label-wrap td { border: none; padding: 0 2pt; font-size: 7pt; font-style: italic; text-align: center; }

            /* ── Main IIRUP table ── */
            .tbl-wrap { overflow: visible !important; }
            table.iirup-tbl {
                width: 100% !important;
                font-family: Arial, sans-serif !important;
                font-size: 6.5pt !important;
                border-collapse: collapse !important;
                table-layout: fixed !important;
                margin-bottom: 3pt !important;
            }
            table.iirup-tbl th {
                padding: 1.5pt 2pt !important;
                font-size: 6.5pt !important;
                font-weight: bold !important;
                border: 0.5pt solid #000 !important;
                background: white !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                text-align: center !important;
                vertical-align: middle !important;
                word-wrap: break-word !important;
                overflow-wrap: normal !important;
                word-break: keep-all !important;
                hyphens: none !important;
                line-height: 1.2 !important;
            }
            table.iirup-tbl td {
                padding: 1.5pt 2pt !important;
                border: 0.5pt solid #000 !important;
                background: white !important;
                font-size: 6.5pt !important;
                word-wrap: break-word !important;
                overflow-wrap: break-word !important;
                vertical-align: top !important;
                line-height: 1.2 !important;
            }
            table.iirup-tbl td.tdr { text-align: right !important; }
            table.iirup-tbl td.tdl { text-align: left !important; }
            table.iirup-tbl tr.row-total td { font-weight: bold !important; }
            table.iirup-tbl tr.banner td { font-weight: bold !important; text-align: center !important; }

            /* Print column widths — wide enough for every header word to stay intact */
            table.iirup-tbl col.cB  { width: 5.5% !important; }   /* Date Acquired */
            table.iirup-tbl col.cC  { width: 14.0% !important; }  /* Particulars/Articles */
            table.iirup-tbl col.cD  { width: 6.5% !important; }   /* Property No. */
            table.iirup-tbl col.cE  { width: 2.5% !important; }   /* Qty */
            table.iirup-tbl col.cF  { width: 6.0% !important; }   /* Unit Cost */
            table.iirup-tbl col.cG  { width: 6.5% !important; }   /* Total Cost */
            table.iirup-tbl col.cH  { width: 7.5% !important; }   /* Accum Depreciation */
            table.iirup-tbl col.cI  { width: 7.0% !important; }   /* Accum Impairment Losses */
            table.iirup-tbl col.cJ  { width: 6.5% !important; }   /* Carrying Amount */
            table.iirup-tbl col.cKL { width: 9.0% !important; }   /* Remarks */
            table.iirup-tbl col.cM  { width: 3.5% !important; }   /* Sale */
            table.iirup-tbl col.cN  { width: 5.5% !important; }   /* Transfer */
            table.iirup-tbl col.cO  { width: 6.0% !important; }   /* Destruction */
            table.iirup-tbl col.cP  { width: 6.0% !important; }   /* Others (Specify) */
            table.iirup-tbl col.cQ  { width: 5.0% !important; }   /* Total */
            table.iirup-tbl col.cR  { width: 5.5% !important; }   /* Appraised Value */
            table.iirup-tbl col.cS  { width: 4.0% !important; }   /* OR No. */
            table.iirup-tbl col.cT  { width: 4.0% !important; }   /* Amount */

            /* ── Certification table ── */
            .print-cert { display: block !important; margin-top: 4pt; }
            .print-cert table { width: 100%; border-collapse: collapse; font-size: 5.5pt; }
            .print-cert td {
                padding: 3pt 4pt;
                font-size: 5.5pt;
                line-height: 1.3;
            }

            /* ── Signature blocks ── */
            .print-sigs { display: block !important; margin-top: 4pt; }
            .print-sigs table { width: 100%; border-collapse: collapse; }
            .print-sigs td { padding: 0 4pt; vertical-align: bottom; width: 25%; border: none; }
            /* "Requested by:" / "Approved by:" italic label with space below */
            .print-sigs .sc { font-style: italic; display: block; margin-bottom: 10pt; font-size: 5.5pt; }
            /* Name WITH underline (Inspection Officer + Auditor only — matches XLS) */
            .print-sigs .sn {
                border-bottom: 0.5pt solid #000;
                font-weight: bold; text-align: center; display: block;
                min-height: 10pt; font-size: 5.5pt; padding-bottom: 1pt;
            }
            /* Name WITHOUT underline (Requested by + Approved by — matches XLS) */
            .print-sigs .sn-plain {
                font-weight: bold; text-align: center; display: block;
                min-height: 10pt; font-size: 5.5pt;
            }
            .print-sigs .sr { text-align: center; display: block; margin-top: 1pt; font-size: 5pt; }
        }

        /* Hide print-only elements on screen */
        .print-cert, .print-sigs, .print-appendix, .print-title, .print-asof,
        .print-meta, .print-officer-wrap, .print-label-wrap { display: none; }
    </style>
</head>
<body>
<div class="container">
    @include('layouts.core.sidebar')
    <div class="details">
        @include('layouts.core.header')
        <div class="iirup-content">

            <a href="{{ route('client.reports.index') }}" class="back-button">
                <i class="fas fa-arrow-left"></i> Back to Reports
            </a>

            {{-- ══ PRINT ONLY: Appendix 74 ══ --}}
            <div class="print-only print-appendix">Appendix  74</div>

            {{-- ══ PRINT ONLY: Title — Times NR 14pt bold, uppercase ══ --}}
            <div class="print-only print-title">INVENTORY AND INSPECTION REPORT OF UNSERVICEABLE PROPERTY</div>

            {{-- ══ PRINT ONLY: As of — Times NR 12pt centered ══ --}}
            <div class="print-only print-asof">
                As of&nbsp;
                @if(!empty($header['as_of']) && trim($header['as_of']) !== '')
                    {{ $header['as_of'] }}
                @else
                    <span style="display:inline-block;border-bottom:0.5pt solid #000;min-width:140pt;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                @endif
            </div>

            {{-- ══ PRINT ONLY: Entity Name | Fund Cluster ══ --}}
            <div class="print-only print-meta">
                <table>
                    <tr>
                        <td style="width:65%; text-align:left;"><strong>Entity Name:</strong>&nbsp;{{ $header['entity_name'] ?? '' }}</td>
                        <td style="width:35%; text-align:right;"><strong>Fund Cluster :</strong>&nbsp;{{ $header['fund_cluster'] ?? '' }}</td>
                    </tr>
                </table>
            </div>

            {{-- ══ PRINT ONLY: Accountable Officer row (underlined) ══ --}}
            <div class="print-only print-officer-wrap">
                <table>
                    <tr>
                        <td class="ul" style="width:28%;">{{ $header['accountable_person'] ?? '' }}</td>
                        <td style="width:4%;"></td>
                        <td class="ul" style="width:28%;">{{ $header['position'] ?? '' }}</td>
                        <td style="width:4%;"></td>
                        <td class="ul" style="width:24%;">{{ $header['office'] ?? '' }}</td>
                        <td style="width:12%;"></td>
                    </tr>
                </table>
            </div>

            {{-- ══ PRINT ONLY: Italic label row ══ --}}
            <div class="print-only print-label-wrap">
                <table>
                    <tr>
                        <td style="width:28%;">(Name of Accountable Officer)</td>
                        <td style="width:4%;"></td>
                        <td style="width:28%;">(Designation)</td>
                        <td style="width:4%;"></td>
                        <td style="width:24%;">(Station)</td>
                        <td style="width:12%;"></td>
                    </tr>
                </table>
            </div>

            {{-- ══ Screen: Report title ══ --}}
            <div style="text-align:center; margin-bottom:14px;" class="screen-only">
                <div style="font-size:15px; font-weight:bold; text-transform:uppercase; margin-bottom:4px;">INVENTORY AND INSPECTION REPORT OF UNSERVICEABLE PROPERTY</div>
                <div style="font-size:13px;">
                    As of&nbsp;
                    @if(!empty($header['as_of']) && trim($header['as_of']) !== '')
                        {{ $header['as_of'] }}
                    @else
                        <span style="display:inline-block;border-bottom:1px solid #000;min-width:120px;">&nbsp;</span>
                    @endif
                </div>
            </div>

            {{-- ══ 1. Apply Header ══ --}}
            <div class="panel screen-only">
                <div class="panel-title"><i class="fas fa-heading"></i> Apply Header</div>
                <form method="GET" action="{{ route('client.report.iirup') }}">
                    <input type="hidden" name="date_from"      value="{{ request('date_from') }}">
                    <input type="hidden" name="date_to"        value="{{ request('date_to') }}">
                    <input type="hidden" name="classification" value="{{ request('classification') }}">
                    <input type="hidden" name="disposed"       value="{{ request('disposed','all') }}">
                    <input type="hidden" name="f_req_name"     value="{{ request('f_req_name') }}">
                    <input type="hidden" name="f_req_role"     value="{{ request('f_req_role') }}">
                    <input type="hidden" name="f_appr_name"    value="{{ request('f_appr_name') }}">
                    <input type="hidden" name="f_appr_role"    value="{{ request('f_appr_role') }}">
                    <input type="hidden" name="f_insp_name"    value="{{ request('f_insp_name') }}">
                    <input type="hidden" name="f_insp_role"    value="{{ request('f_insp_role') }}">
                    <input type="hidden" name="f_aud_name"     value="{{ request('f_aud_name') }}">
                    <input type="hidden" name="f_aud_role"     value="{{ request('f_aud_role') }}">
                    <input type="hidden" name="f_aud_role2"    value="{{ request('f_aud_role2') }}">

                    <div class="panel-grid" style="grid-template-columns:160px 1fr 130px 160px 160px 160px auto;">
                        <div class="fg"><label>As of Date</label><input type="date" name="as_of" value="{{ request('as_of') }}"></div>
                        <div class="fg"><label>Entity Name</label><input type="text" name="entity_name" placeholder="e.g. Agricultural Training Institute-RTC I" value="{{ request('entity_name') }}"></div>
                        <div class="fg"><label>Fund Cluster</label><input type="text" name="fund_cluster" placeholder="e.g. 01" value="{{ request('fund_cluster') }}"></div>
                        <div class="fg"><label>Accountable Person</label><input type="text" name="accountable_person" placeholder="Name" value="{{ request('accountable_person') }}"></div>
                        <div class="fg"><label>Designation</label><input type="text" name="position" placeholder="Position/Title" value="{{ request('position') }}"></div>
                        <div class="fg"><label>Station</label><input type="text" name="office" placeholder="e.g. ATI-RTC I" value="{{ request('office') }}"></div>
                        <div class="fg-actions">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Apply</button>
                            <a href="{{ route('client.report.iirup') }}" class="btn btn-secondary"><i class="fas fa-redo"></i></a>
                        </div>
                    </div>
                </form>
            </div>

            {{-- ══ 2. Apply Filters ══ --}}
            <div class="panel screen-only">
                <div class="panel-title"><i class="fas fa-filter"></i> Apply Filters</div>
                <form method="GET" action="{{ route('client.report.iirup') }}">
                    <input type="hidden" name="as_of"               value="{{ request('as_of') }}">
                    <input type="hidden" name="entity_name"         value="{{ request('entity_name') }}">
                    <input type="hidden" name="fund_cluster"        value="{{ request('fund_cluster') }}">
                    <input type="hidden" name="accountable_person"  value="{{ request('accountable_person') }}">
                    <input type="hidden" name="position"            value="{{ request('position') }}">
                    <input type="hidden" name="office"              value="{{ request('office') }}">
                    <input type="hidden" name="f_req_name"          value="{{ request('f_req_name') }}">
                    <input type="hidden" name="f_req_role"          value="{{ request('f_req_role') }}">
                    <input type="hidden" name="f_appr_name"         value="{{ request('f_appr_name') }}">
                    <input type="hidden" name="f_appr_role"         value="{{ request('f_appr_role') }}">
                    <input type="hidden" name="f_insp_name"         value="{{ request('f_insp_name') }}">
                    <input type="hidden" name="f_insp_role"         value="{{ request('f_insp_role') }}">
                    <input type="hidden" name="f_aud_name"          value="{{ request('f_aud_name') }}">
                    <input type="hidden" name="f_aud_role"          value="{{ request('f_aud_role') }}">
                    <input type="hidden" name="f_aud_role2"         value="{{ request('f_aud_role2') }}">

                    <div class="panel-grid" style="grid-template-columns:160px 160px 1fr 1fr auto;">
                        <div class="fg"><label>Date From</label><input type="date" name="date_from" value="{{ request('date_from') }}"></div>
                        <div class="fg"><label>Date To</label><input type="date" name="date_to" value="{{ request('date_to') }}"></div>
                        <div class="fg">
                            <label>Classification</label>
                            <select name="classification">
                                <option value="">All Classifications</option>
                                @foreach($classifications as $c)
                                    <option value="{{ $c }}" {{ request('classification') == $c ? 'selected' : '' }}>{{ $c }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="fg">
                            <label>Disposed Status</label>
                            <select name="disposed">
                                <option value="all"          {{ request('disposed','all') == 'all'         ? 'selected' : '' }}>All</option>
                                <option value="disposed"     {{ request('disposed') == 'disposed'           ? 'selected' : '' }}>Disposed</option>
                                <option value="not_disposed" {{ request('disposed') == 'not_disposed'       ? 'selected' : '' }}>Not Disposed</option>
                            </select>
                        </div>
                        <div class="fg-actions">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                            <a href="{{ route('client.report.iirup', request()->only(['as_of','entity_name','fund_cluster','accountable_person','position','office','f_req_name','f_req_role','f_appr_name','f_appr_role','f_insp_name','f_insp_role','f_aud_name','f_aud_role','f_aud_role2'])) }}" class="btn btn-secondary"><i class="fas fa-redo"></i></a>
                        </div>
                    </div>
                </form>
            </div>

            {{-- ══ 3. Apply Footer (Signatories) ══ --}}
            <div class="panel screen-only">
                <div class="panel-title"><i class="fas fa-users"></i> Apply Footer (Signatories)</div>
                <form method="GET" action="{{ route('client.report.iirup') }}">
                    <input type="hidden" name="as_of"               value="{{ request('as_of') }}">
                    <input type="hidden" name="entity_name"         value="{{ request('entity_name') }}">
                    <input type="hidden" name="fund_cluster"        value="{{ request('fund_cluster') }}">
                    <input type="hidden" name="accountable_person"  value="{{ request('accountable_person') }}">
                    <input type="hidden" name="position"            value="{{ request('position') }}">
                    <input type="hidden" name="office"              value="{{ request('office') }}">
                    <input type="hidden" name="date_from"           value="{{ request('date_from') }}">
                    <input type="hidden" name="date_to"             value="{{ request('date_to') }}">
                    <input type="hidden" name="classification"      value="{{ request('classification') }}">
                    <input type="hidden" name="disposed"            value="{{ request('disposed','all') }}">

                    <div class="footer-grid">
                        <div>
                            <span class="footer-col-label">Requested by:</span>
                            <p class="footer-sub-label">Name</p>
                            <input type="text" name="f_req_name"  value="{{ request('f_req_name',  'FRANKLIN A. SALCEDO') }}">
                            <input type="text" name="f_req_role"  value="{{ request('f_req_role',  'Supply and Property Officer') }}" placeholder="Designation">
                        </div>
                        <div>
                            <span class="footer-col-label">Approved by:</span>
                            <p class="footer-sub-label">Name</p>
                            <input type="text" name="f_appr_name" value="{{ request('f_appr_name', 'JAYVEE BRYAN G. CARILLO, Ph.D.') }}">
                            <input type="text" name="f_appr_role" value="{{ request('f_appr_role', 'Center Director') }}" placeholder="Designation">
                            <p class="footer-sub-label" style="margin-top:8px;">Inspection Officer</p>
                            <input type="text" name="f_insp_name" value="{{ request('f_insp_name', 'JOSE O. KANLAS, JR.') }}">
                            <input type="text" name="f_insp_role" value="{{ request('f_insp_role', 'Inspection Officer') }}" placeholder="Designation">
                        </div>
                        <div>
                            <span class="footer-col-label">Verified by (Auditor):</span>
                            <p class="footer-sub-label">Name</p>
                            <input type="text" name="f_aud_name"  value="{{ request('f_aud_name',  'JELANIE S. WANAWAN') }}">
                            <input type="text" name="f_aud_role"  value="{{ request('f_aud_role',  'State Auditor II') }}" placeholder="Role">
                            <input type="text" name="f_aud_role2" value="{{ request('f_aud_role2', 'OIC - Audit Team Leader') }}" placeholder="Secondary Role">
                        </div>
                    </div>
                    <div style="display:flex;gap:8px;margin-top:12px;">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Apply Footer</button>
                        <a href="{{ route('client.report.iirup', request()->only(['date_from','date_to','classification','disposed','as_of','entity_name','fund_cluster','accountable_person','position','office'])) }}" class="btn btn-secondary"><i class="fas fa-redo"></i> Reset</a>
                    </div>
                </form>
            </div>

            @if($ppesItems->count() > 0)

                @include('client.report._export_fab', [
                    'excelUrl' => route('client.report.iirup.export.excel', request()->query()),
                    'pdfUrl'   => route('client.report.iirup.export.pdf',   request()->query()),
                ])

                <div class="table-toggle-bar screen-only">
                    <span class="record-count"><i class="fas fa-list"></i>&nbsp;{{ $ppesItems->count() }} record(s) found</span>
                    <button type="button" class="btn btn-info" onclick="toggleTable()">
                        <i class="fas fa-eye-slash" id="toggleIcon"></i> <span id="toggleLabel">Hide Table</span>
                    </button>
                </div>

                {{-- ══ MAIN TABLE ══ --}}
                <div class="tbl-wrap" id="table-wrapper">
                    <table class="iirup-tbl">
                        {{-- 18 columns matching XLS B–S (K+L merged as one Remarks col) --}}
                        <colgroup>
                            <col class="cB">  {{-- Date Acquired --}}
                            <col class="cC">  {{-- Particulars/Articles --}}
                            <col class="cD">  {{-- Property No. --}}
                            <col class="cE">  {{-- Qty --}}
                            <col class="cF">  {{-- Unit Cost --}}
                            <col class="cG">  {{-- Total Cost --}}
                            <col class="cH">  {{-- Accum Depreciation --}}
                            <col class="cI">  {{-- Accum Impairment Losses --}}
                            <col class="cJ">  {{-- Carrying Amount --}}
                            <col class="cKL"> {{-- Remarks (K+L merged) --}}
                            <col class="cM">  {{-- Sale --}}
                            <col class="cN">  {{-- Transfer --}}
                            <col class="cO">  {{-- Destruction --}}
                            <col class="cP">  {{-- Others (Specify) --}}
                            <col class="cQ">  {{-- Total Disposal --}}
                            <col class="cR">  {{-- Appraised Value --}}
                            <col class="cS">  {{-- OR No. --}}
                            <col class="cT">  {{-- Amount --}}
                        </colgroup>
                        <thead>
                            {{-- Row 10: Group headers --}}
                            <tr>
                                <th colspan="10">INVENTORY</th>
                                <th colspan="8">INSPECTION and DISPOSAL</th>
                            </tr>
                            {{-- Row 11: Column headers (rowspan=3) + sub-group headers --}}
                            <tr>
                                <th rowspan="3">Date<br>Acquired</th>
                                <th rowspan="3">Particulars/<br>Articles</th>
                                <th rowspan="3">Property<br>No.</th>
                                <th rowspan="3">Qty</th>
                                <th rowspan="3">Unit<br>Cost</th>
                                <th rowspan="3">Total<br>Cost</th>
                                <th rowspan="3">Accumulated<br>Depreciation</th>
                                <th rowspan="3">Accumulated<br>Impairment<br>Losses</th>
                                <th rowspan="3">Carrying<br>Amount</th>
                                <th rowspan="3">Remarks</th>
                                <th colspan="5">DISPOSAL</th>
                                <th rowspan="3">Appraised<br>Value</th>
                                <th colspan="2">RECORD OF SALES</th>
                            </tr>
                            {{-- Row 12: DISPOSAL sub-headers (rowspan=2) + REC.SALES sub-headers --}}
                            <tr>
                                <th rowspan="2">Sale</th>
                                <th rowspan="2">Transfer</th>
                                <th rowspan="2">Destruction</th>
                                <th rowspan="2">Others<br>(Specify)</th>
                                <th rowspan="2">Total</th>
                                <th rowspan="2">OR No.</th>
                                <th rowspan="2">Amount</th>
                            </tr>
                            {{-- Row 13: empty (consumed by rowspans) --}}
                            <tr></tr>
                            {{-- Row 14: Column numbers (1)–(18) --}}
                            <tr>
                                <th>(1)</th><th>(2)</th><th>(3)</th><th>(4)</th>
                                <th>(5)</th><th>(6)</th><th>(7)</th><th>(8)</th>
                                <th>(9)</th><th>(10)</th><th>(11)</th><th>(12)</th>
                                <th>(13)</th><th>(14)</th><th>(15)</th><th>(16)</th>
                                <th>(17)</th><th>(18)</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Row 15: PANGASINAN banner — mirrors XLS B15:S15 --}}
                            <tr class="banner"><td colspan="18">PANGASINAN</td></tr>
                            @foreach($ppesItems as $item)
                            <tr>
                                <td>{{ $item->date_acquired }}</td>
                                <td class="tdl">{{ $item->particulars_articles }}</td>
                                <td>{{ $item->property_no }}</td>
                                <td>{{ $item->qty }}</td>
                                <td class="tdr">{{ number_format((float)($item->unit_cost ?? 0), 2) }}</td>
                                <td class="tdr">{{ (float)($item->total_cost ?? 0) != 0 ? number_format((float)$item->total_cost, 2) : '' }}</td>
                                <td class="tdr">{{ (float)($item->accumulated_depreciation ?? 0) != 0 ? number_format((float)$item->accumulated_depreciation, 2) : '' }}</td>
                                <td class="tdr">{{ (float)($item->accumulated_impairment_losses ?? 0) != 0 ? number_format((float)$item->accumulated_impairment_losses, 2) : '' }}</td>
                                <td class="tdr">{{ (float)($item->carrying_amount ?? 0) != 0 ? number_format((float)$item->carrying_amount, 2) : '' }}</td>
                                <td class="tdl">{{ $item->remarks }}</td>
                                <td class="tdr">{{ (float)($item->sale ?? 0) != 0 ? number_format((float)$item->sale, 2) : '' }}</td>
                                <td class="tdr">{{ (float)($item->transfer ?? 0) != 0 ? number_format((float)$item->transfer, 2) : '' }}</td>
                                <td class="tdr">{{ (float)($item->destruction ?? 0) != 0 ? number_format((float)$item->destruction, 2) : '' }}</td>
                                <td class="tdr">{{ (float)($item->others ?? 0) != 0 ? number_format((float)$item->others, 2) : '' }}</td>
                                <td class="tdr">{{ ($item->total_disposal !== '' && (float)($item->total_disposal ?? 0) != 0) ? number_format((float)$item->total_disposal, 2) : '' }}</td>
                                <td class="tdr">{{ (float)($item->appraised_value ?? 0) != 0 ? number_format((float)$item->appraised_value, 2) : '' }}</td>
                                <td>{{ $item->or_no }}</td>
                                <td class="tdr">{{ (float)($item->amount ?? 0) != 0 ? number_format((float)$item->amount, 2) : '' }}</td>
                            </tr>
                            @endforeach

                            {{-- Totals row — mirrors XLS SUM row --}}
                            <tr class="row-total">
                                <td colspan="4"></td>
                                <td class="tdr">{{ number_format($ppesItems->sum(fn($i) => (float)($i->unit_cost ?? 0)), 2) }}</td>
                                <td class="tdr">{{ number_format($ppesItems->sum(fn($i) => (float)($i->total_cost ?? 0)), 2) }}</td>
                                <td class="tdr">{{ number_format($ppesItems->sum(fn($i) => (float)($i->accumulated_depreciation ?? 0)), 2) }}</td>
                                <td></td>
                                <td class="tdr">{{ number_format($ppesItems->sum(fn($i) => (float)($i->carrying_amount ?? 0)), 2) }}</td>
                                <td></td><td class="tdr">{{ number_format($ppesItems->sum(fn($i) => (float)($i->sale ?? 0)), 2) }}</td><td class="tdr">{{ number_format($ppesItems->sum(fn($i) => (float)($i->transfer ?? 0)), 2) }}</td><td class="tdr">{{ number_format($ppesItems->sum(fn($i) => (float)($i->destruction ?? 0)), 2) }}</td><td class="tdr">{{ number_format($ppesItems->sum(fn($i) => (float)($i->others ?? 0)), 2) }}</td><td class="tdr">{{ number_format($ppesItems->sum(fn($i) => (float)($i->sale ?? 0) + (float)($i->transfer ?? 0) + (float)($i->destruction ?? 0) + (float)($i->others ?? 0)), 2) }}</td>
                                <td class="tdr">{{ number_format($ppesItems->sum(fn($i) => (float)($i->appraised_value ?? 0)), 2) }}</td>
                                <td></td>
                                <td class="tdr">{{ number_format($ppesItems->sum(fn($i) => (float)($i->amount ?? 0)), 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- ══ Signature Block Preview (screen only) ══ --}}
                <div class="sig-preview screen-only">
                    <div class="sig-preview-title"><i class="fas fa-signature"></i> Signature Block Preview</div>
                    <div class="sig-grid">
                        <div class="sig-col">
                            <span class="sig-label">Requested by:</span>
                            <span class="sig-name">{{ request('f_req_name', 'FRANKLIN A. SALCEDO') }}</span>
                            <span class="sig-role">{{ request('f_req_role', 'Supply and Property Officer') }}</span>
                        </div>
                        <div class="sig-col">
                            <span class="sig-label">Approved by:</span>
                            <span class="sig-name">{{ request('f_appr_name', 'JAYVEE BRYAN G. CARILLO, Ph.D.') }}</span>
                            <span class="sig-role">{{ request('f_appr_role', 'Center Director') }}</span>
                        </div>
                        <div class="sig-col">
                            <span class="sig-label">Inspection Officer:</span>
                            <span class="sig-name">{{ request('f_insp_name', 'JOSE O. KANLAS, JR.') }}</span>
                            <span class="sig-role">{{ request('f_insp_role', 'Inspection Officer') }}</span>
                        </div>
                        <div class="sig-col">
                            <span class="sig-label">Verified by:</span>
                            <span class="sig-name">{{ request('f_aud_name', 'JELANIE S. WANAWAN') }}</span>
                            <span class="sig-role">{{ request('f_aud_role', 'State Auditor II') }}</span>
                            <span class="sig-role">{{ request('f_aud_role2', 'OIC - Audit Team Leader') }}</span>
                        </div>
                    </div>
                </div>

                {{-- ══ PRINT ONLY: Certification boxes (mirrors XLS B134:K135 | L134:O139 | Q134:S138) ══ --}}
                @php
                    $fReqName  = $header['f_req_name']  ?? 'FRANKLIN A. SALCEDO';
                    $fReqRole  = $header['f_req_role']  ?? 'Supply and Property Officer';
                    $fApprName = $header['f_appr_name'] ?? 'JAYVEE BRYAN G. CARILLO, Ph.D.';
                    $fApprRole = $header['f_appr_role'] ?? 'Center Director';
                    $fInspName = $header['f_insp_name'] ?? 'JOSE O. KANLAS, JR.';
                    $fInspRole = $header['f_insp_role'] ?? 'Inspection Officer';
                    $fAudName  = $header['f_aud_name']  ?? 'JELANIE S. WANAWAN';
                    $fAudRole  = $header['f_aud_role']  ?? 'State Auditor II';
                    $fAudRole2 = $header['f_aud_role2'] ?? 'OIC - Audit Team Leader';
                @endphp

                {{-- ════ PRINT ONLY: Footer — exact replica of XLS rows 133–142 ════
                     Verified from XLS binary:
                     Row 133: medium TOP full width; medium left@B, right@K, left@L, right@S
                     Rows 134–135: B:K merged = "I HEREBY request..."
                     Rows 134–139: L:O merged = "I CERTIFY inspected..."
                     Rows 134–138: Q:S merged = "I CERTIFY witnessed..."
                     Row 137: "Requested by:" @B, "Approved by:" @G (inside left box)
                     Row 140: C:F=req(bold,no underline), G:I=appr(bold,no underline),
                              L:O=insp(bold,thin bottom), Q:S=aud(bold,thin bottom)
                     Row 141: C:F=req role, G:I=appr role,
                              L:O=insp role(thin TOP), Q:S=aud role(thin TOP, 2 lines)
                     Row 142: medium BOTTOM full width
                --}}
                <div class="print-only" style="margin-top:3pt; font-family:'Arial Narrow',Arial,sans-serif; font-size:5.5pt; line-height:1.35;">
                    <table style="width:100%; border-collapse:collapse; table-layout:fixed;">
                        <colgroup>
                            <col style="width:4.01%">   {{-- B: left-border-only col --}}
                            <col style="width:27.67%">  {{-- C:F: req name/role --}}
                            <col style="width:18.87%">  {{-- G:I: appr name/role --}}
                            <col style="width:12.95%">  {{-- J:K: gap + right border --}}
                            <col style="width:16.43%">  {{-- L:O: insp name/role --}}
                            <col style="width:5.20%">   {{-- P: gap --}}
                            <col style="width:14.86%">  {{-- Q:S: aud name/role --}}
                        </colgroup>

                        {{-- Row 133: blank separator — MEDIUM TOP --}}
                        <tr style="height:4pt;">
                            <td style="border-top:1.5pt solid #000; border-left:1.5pt solid #000; border-bottom:none; border-right:none;"></td>
                            <td style="border-top:1.5pt solid #000; border:none; border-top:1.5pt solid #000;"></td>
                            <td style="border-top:1.5pt solid #000; border:none; border-top:1.5pt solid #000;"></td>
                            <td style="border-top:1.5pt solid #000; border-left:none; border-bottom:none; border-right:1.5pt solid #000;"></td>
                            <td style="border-top:1.5pt solid #000; border-left:1.5pt solid #000; border-bottom:none; border-right:none;"></td>
                            <td style="border-top:1.5pt solid #000; border:none; border-top:1.5pt solid #000;"></td>
                            <td style="border-top:1.5pt solid #000; border-left:none; border-bottom:none; border-right:1.5pt solid #000;"></td>
                        </tr>

                        {{-- Rows 134–139: cert text (rendered as one row) --}}
                        <tr>
                            <td colspan="4" style="padding:3pt 4pt 3pt 4pt; vertical-align:top;
                                                   border-left:1.5pt solid #000; border-right:1.5pt solid #000;
                                                   border-top:none; border-bottom:none;">
                                <div style="margin-bottom:8pt;">
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;I HEREBY request inspection and disposition, pursuant to Section&nbsp;&nbsp;79 of PD 1445, of the property enumerated above.
                                </div>
                                <table style="width:100%; border-collapse:collapse; font-size:5.5pt; font-family:'Arial Narrow',Arial,sans-serif;">
                                    <tr>
                                        <td style="width:50%; padding:0; text-align:left; border:none; vertical-align:bottom;">Requested by:</td>
                                        <td style="width:50%; padding:0; text-align:left; border:none; vertical-align:bottom;">Approved by:</td>
                                    </tr>
                                </table>
                            </td>
                            <td style="padding:3pt 4pt; vertical-align:top;
                                       border-left:1.5pt solid #000; border-right:none;
                                       border-top:none; border-bottom:none;">
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;I CERTIFY that I have inspected each and every article enumerated in this report, and that the disposition made thereof was, in my judgment, the best for the public interest.
                            </td>
                            <td style="border:none;"></td>
                            <td style="padding:3pt 4pt; vertical-align:top;
                                       border-left:none; border-right:1.5pt solid #000;
                                       border-top:none; border-bottom:none;">
                                &nbsp;&nbsp;&nbsp;&nbsp;I CERTIFY that I have witnessed the disposition of the articles enumerated on this report this ____day of _____________, _____.
                            </td>
                        </tr>

                        {{-- Row 140: Names --}}
                        <tr style="height:14pt;">
                            <td style="border-left:1.5pt solid #000; border-top:none; border-bottom:none; border-right:none;"></td>
                            <td style="padding:1pt 2pt 0; text-align:center; vertical-align:bottom; border:none; font-weight:bold;">
                                <span class="sn-plain">{{ strtoupper($fReqName) }}</span>
                            </td>
                            <td style="padding:1pt 2pt 0; text-align:center; vertical-align:bottom; border:none; font-weight:bold;">
                                <span class="sn-plain">{{ strtoupper($fApprName) }}</span>
                            </td>
                            <td style="border-left:none; border-top:none; border-bottom:none; border-right:1.5pt solid #000;"></td>
                            <td style="padding:1pt 2pt 0; text-align:center; vertical-align:bottom;
                                       border-left:1.5pt solid #000; border-right:none;
                                       border-top:none; border-bottom:0.5pt solid #000; font-weight:bold;">
                                <span class="sn-plain">{{ strtoupper($fInspName) }}</span>
                            </td>
                            <td style="border:none;"></td>
                            <td style="padding:1pt 2pt 0; text-align:center; vertical-align:bottom;
                                       border-left:none; border-right:1.5pt solid #000;
                                       border-top:none; border-bottom:0.5pt solid #000; font-weight:bold;">
                                <span class="sn-plain">{{ strtoupper($fAudName) }}</span>
                            </td>
                        </tr>

                        {{-- Row 141: Roles --}}
                        <tr style="height:10pt;">
                            <td style="border-left:1.5pt solid #000; border-top:none; border-bottom:none; border-right:none;"></td>
                            <td style="padding:0 2pt 1pt; text-align:center; vertical-align:top; border:none;">
                                <span class="sr">{{ $fReqRole }}</span>
                            </td>
                            <td style="padding:0 2pt 1pt; text-align:center; vertical-align:top; border:none;">
                                <span class="sr">{{ $fApprRole }}</span>
                            </td>
                            <td style="border-left:none; border-top:none; border-bottom:none; border-right:1.5pt solid #000;"></td>
                            <td style="padding:0 2pt 1pt; text-align:center; vertical-align:top;
                                       border-left:1.5pt solid #000; border-right:none;
                                       border-top:0.5pt solid #000; border-bottom:none;">
                                <span class="sr">{{ $fInspRole }}</span>
                            </td>
                            <td style="border:none;"></td>
                            <td style="padding:0 2pt 1pt; text-align:center; vertical-align:top;
                                       border-left:none; border-right:1.5pt solid #000;
                                       border-top:0.5pt solid #000; border-bottom:none;">
                                <span class="sr">{{ $fAudRole }}<br>{{ $fAudRole2 }}</span>
                            </td>
                        </tr>

                        {{-- Row 142: blank closing — MEDIUM BOTTOM --}}
                        <tr style="height:3pt;">
                            <td style="border-top:none; border-bottom:1.5pt solid #000; border-left:1.5pt solid #000; border-right:none;"></td>
                            <td style="border-top:none; border-bottom:1.5pt solid #000; border-left:none; border-right:none;"></td>
                            <td style="border-top:none; border-bottom:1.5pt solid #000; border-left:none; border-right:none;"></td>
                            <td style="border-top:none; border-bottom:1.5pt solid #000; border-left:none; border-right:1.5pt solid #000;"></td>
                            <td style="border-top:none; border-bottom:1.5pt solid #000; border-left:1.5pt solid #000; border-right:none;"></td>
                            <td style="border-top:none; border-bottom:1.5pt solid #000; border-left:none; border-right:none;"></td>
                            <td style="border-top:none; border-bottom:1.5pt solid #000; border-left:none; border-right:1.5pt solid #000;"></td>
                        </tr>

                    </table>
                </div>

            @else
                <div class="empty-state screen-only">
                    <i class="fas fa-box-open"></i>
                    <h3>No Unserviceable Equipment Found</h3>
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
    const h = w.style.display === 'none';
    w.style.display = h ? '' : 'none';
    i.className = h ? 'fas fa-eye-slash' : 'fas fa-eye';
    l.textContent = h ? 'Hide Table' : 'Show Table';
}
</script>
</body>
</html>