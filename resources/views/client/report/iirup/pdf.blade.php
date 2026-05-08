<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>IIRUP Report</title>
    <style>
        /*
         * DomPDF A4 landscape
         * @page margin=0, spacing controlled by .page-wrap padding.
         * Width = 297mm - 8mm*2 = 281mm
         */
        @page {
            size: 297mm 210mm;
            margin: 0;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 6.5pt;
            line-height: 1.2;
            color: #000;
            background: white;
            margin: 0;
            padding: 0;
        }

        .page-wrap {
            width: 281mm;
            margin: 0 auto;
            padding: 6mm 0;
        }

        /* ── Appendix 74 ── */
        .appendix {
            text-align: right;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 7pt;
            margin-bottom: 1pt;
            line-height: 1.2;
        }

        /*
         * DomPDF does NOT ship Times New Roman.
         * We use Georgia (a serif font DomPDF does include) which is visually
         * very close to TNR and renders correctly without font substitution.
         */

        /* ── Title ── */
        .doc-title {
            font-family: Georgia, "Times New Roman", serif;
            font-size: 13pt;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            margin-bottom: 2pt;
            line-height: 1.3;
        }

        /* ── As of ── */
        .doc-asof {
            font-family: Georgia, "Times New Roman", serif;
            font-size: 11pt;
            text-align: center;
            margin-bottom: 4pt;
        }

        /*
         * DomPDF does not support display:inline-block reliably for underline spans.
         * Use a table-based layout for the "As of ___" blank line instead.
         */
        .doc-asof-tbl { width: 100%; border-collapse: collapse; }
        .doc-asof-tbl td { border: none; padding: 0; text-align: center;
                           font-family: Georgia, "Times New Roman", serif;
                           font-size: 11pt; }
        .doc-asof-blank { border-bottom: 0.5pt solid #000; width: 140pt; }

        /* ── Entity / Fund Cluster ── */
        .doc-meta { width: 100%; margin-bottom: 2pt; }
        .doc-meta table { width: 100%; border-collapse: collapse; }
        .doc-meta td {
            padding: 0;
            font-size: 8pt;
            font-weight: bold;
            vertical-align: bottom;
            border: none;
        }

        /* ── Accountable Officer | Designation | Station ── */
        .doc-officer { width: 100%; margin-bottom: 0; }
        .doc-officer table { width: 100%; border-collapse: collapse; }
        .doc-officer td {
            padding: 0 2pt;
            font-size: 8pt;
            text-align: center;
            vertical-align: bottom;
            border: none;
        }
        /* DomPDF supports border-bottom on td reliably */
        .doc-officer .ul { border-bottom: 0.5pt solid #000; }

        /* ── Italic label row ── */
        .doc-labels { width: 100%; margin-bottom: 5pt; }
        .doc-labels table { width: 100%; border-collapse: collapse; }
        .doc-labels td {
            padding: 0 2pt;
            font-size: 7pt;
            font-style: italic;
            text-align: center;
            border: none;
        }

        /*
         * MAIN TABLE
         * DomPDF has partial rowspan support — it works for simple cases but
         * can mis-render complex multi-level rowspan/colspan headers.
         * Strategy: flatten the 4-row header into separate header rows without
         * rowspan, using explicit height to keep them compact. This is the most
         * reliable approach for DomPDF.
         *
         * Column widths (total = 100%):
         *   B=5.5  C=14.0  D=6.5   E=2.5
         *   F=6.0  G=6.5   H=7.5   I=7.0
         *   J=6.5  KL=9.0
         *   M=3.5  N=5.5   O=6.0   P=6.0
         *   Q=5.0  R=5.5   S=4.0   T=4.0
         */
        table.main {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 6pt;
            margin-bottom: 3pt;
        }
        table.main th, table.main td {
            border: 0.5pt solid #000;
            padding: 1pt 1.5pt;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
            overflow-wrap: break-word;
            word-break: break-word;
            white-space: normal;
            line-height: 1.15;
        }
        table.main th {
            font-weight: bold;
            background: white;
            font-size: 6pt;
            word-break: keep-all;
        }

        table.main td.tdl { text-align: left; }
        table.main td.tdr { text-align: right; }

        /* PANGASINAN / section banner */
        table.main tr.banner td {
            font-weight: bold;
            text-align: center;
            font-size: 6.5pt;
        }

        /* Data rows */
        table.main td { vertical-align: top; }

        /* Totals row */
        table.main tr.row-total td { font-weight: bold; }

        /* ── Footer ── */
        .footer-wrap {
            margin-top: 3pt;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 5.5pt;
            line-height: 1.3;
        }
        .footer-wrap table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .footer-wrap td { vertical-align: top; }

        .sn-plain {
            font-weight: bold;
            text-align: center;
            display: block;
            font-size: 5.5pt;
        }
        .sr {
            text-align: center;
            display: block;
            margin-top: 1pt;
            font-size: 5pt;
        }
    </style>
</head>
<body>
<div class="page-wrap">

{{-- Appendix 74 --}}
<div class="appendix">Appendix&nbsp;&nbsp;74</div>

{{-- Title --}}
<div class="doc-title">INVENTORY AND INSPECTION REPORT OF UNSERVICEABLE PROPERTY</div>

{{-- As of — table-based to avoid DomPDF inline-block issues --}}
<table class="doc-asof-tbl" style="margin-bottom:4pt;">
    <tr>
        <td style="text-align:center; font-family:Georgia,serif; font-size:11pt; border:none; padding:0;">
            As of&nbsp;
            @if(!empty($header['as_of']) && trim($header['as_of']) !== '')
                {{ $header['as_of'] }}
            @else
                <span style="border-bottom:0.5pt solid #000; padding-bottom:0;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
            @endif
        </td>
    </tr>
</table>

{{-- Entity Name | Fund Cluster --}}
<div class="doc-meta">
    <table>
        <tr>
            <td style="width:65%; text-align:left;"><strong>Entity Name:</strong>&nbsp;{{ $header['entity_name'] ?? '' }}</td>
            <td style="width:35%; text-align:right;"><strong>Fund Cluster :</strong>&nbsp;{{ $header['fund_cluster'] ?? '' }}</td>
        </tr>
    </table>
</div>

{{-- Accountable Officer | Designation | Station --}}
<div class="doc-officer">
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

{{-- Italic labels --}}
<div class="doc-labels">
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

{{--
    MAIN TABLE
    DomPDF rowspan support is unreliable for complex multi-level headers.
    We use a flat 4-row header approach:
      Row 1 : group labels  (INVENTORY colspan=10 | INSPECTION and DISPOSAL colspan=8)
      Row 2 : column names  — all 18 cells, no rowspan
      Row 3 : sub-group row — disposal sub-headers + RECORD OF SALES sub-headers
      Row 4 : column numbers (1)–(18)
    Rows 2 and 3 together approximate the multi-level header.
--}}
<table class="main">
    <colgroup>
        <col style="width:5.5%">   {{-- 1: Date Acquired --}}
        <col style="width:14.0%">  {{-- 2: Particulars/Articles --}}
        <col style="width:6.5%">   {{-- 3: Property No. --}}
        <col style="width:2.5%">   {{-- 4: Qty --}}
        <col style="width:6.0%">   {{-- 5: Unit Cost --}}
        <col style="width:6.5%">   {{-- 6: Total Cost --}}
        <col style="width:7.5%">   {{-- 7: Accumulated Depreciation --}}
        <col style="width:7.0%">   {{-- 8: Accumulated Impairment Losses --}}
        <col style="width:6.5%">   {{-- 9: Carrying Amount --}}
        <col style="width:9.0%">   {{-- 10: Remarks --}}
        <col style="width:3.5%">   {{-- 11: Sale --}}
        <col style="width:5.5%">   {{-- 12: Transfer --}}
        <col style="width:6.0%">   {{-- 13: Destruction --}}
        <col style="width:6.0%">   {{-- 14: Others (Specify) --}}
        <col style="width:5.0%">   {{-- 15: Total --}}
        <col style="width:5.5%">   {{-- 16: Appraised Value --}}
        <col style="width:4.0%">   {{-- 17: OR No. --}}
        <col style="width:4.0%">   {{-- 18: Amount --}}
    </colgroup>
    <thead>
        {{-- Row 1: Group headers --}}
        <tr>
            <th colspan="10" style="border-top:1pt solid #000; border-bottom:1pt solid #000; border-left:1pt solid #000;">INVENTORY</th>
            <th colspan="8" style="border-top:1pt solid #000; border-bottom:1pt solid #000; border-right:1pt solid #000;">INSPECTION and DISPOSAL</th>
        </tr>

        {{-- Row 2: Column names (flat, no rowspan) --}}
        <tr>
            <th style="border-left:1pt solid #000;">Date<br>Acquired</th>
            <th>Particulars/<br>Articles</th>
            <th>Property<br>No.</th>
            <th>Qty</th>
            <th>Unit<br>Cost</th>
            <th>Total<br>Cost</th>
            <th>Accumulated<br>Depreciation</th>
            <th>Accumulated<br>Impairment<br>Losses</th>
            <th>Carrying<br>Amount</th>
            <th>Remarks</th>
            {{-- DISPOSAL sub-group --}}
            <th colspan="5">DISPOSAL</th>
            {{-- Appraised Value spans rows 2+3 — use explicit height --}}
            <th>Appraised<br>Value</th>
            {{-- RECORD OF SALES --}}
            <th colspan="2" style="border-right:1pt solid #000;">RECORD OF<br>SALES</th>
        </tr>

        {{-- Row 3: Sub-group detail headers --}}
        <tr>
            {{-- cols 1–10: blank (already labelled above) --}}
            <th style="border-left:1pt solid #000;"></th>
            <th></th><th></th><th></th><th></th>
            <th></th><th></th><th></th><th></th><th></th>
            {{-- DISPOSAL detail --}}
            <th>Sale</th>
            <th>Transfer</th>
            <th>Destruction</th>
            <th>Others<br>(Specify)</th>
            <th>Total</th>
            {{-- Appraised Value (blank — labelled in row 2) --}}
            <th></th>
            {{-- RECORD OF SALES detail --}}
            <th style="border-right:1pt solid #000; border-left:0.5pt solid #000;">OR No.</th>
            <th style="border-right:1pt solid #000;">Amount</th>
        </tr>

        {{-- Row 4: Column numbers --}}
        <tr style="font-size:5.5pt; font-weight:normal;">
            <th style="border-left:1pt solid #000; font-weight:normal;">(1)</th>
            <th style="font-weight:normal;">(2)</th>
            <th style="font-weight:normal;">(3)</th>
            <th style="font-weight:normal;">(4)</th>
            <th style="font-weight:normal;">(5)</th>
            <th style="font-weight:normal;">(6)</th>
            <th style="font-weight:normal;">(7)</th>
            <th style="font-weight:normal;">(8)</th>
            <th style="font-weight:normal;">(9)</th>
            <th style="font-weight:normal;">(10)</th>
            <th style="font-weight:normal;">(11)</th>
            <th style="font-weight:normal;">(12)</th>
            <th style="font-weight:normal;">(13)</th>
            <th style="font-weight:normal;">(14)</th>
            <th style="font-weight:normal;">(15)</th>
            <th style="font-weight:normal;">(16)</th>
            <th style="font-weight:normal;">(17)</th>
            <th style="border-right:1pt solid #000; font-weight:normal;">(18)</th>
        </tr>
    </thead>
    <tbody>
        @php
            $dataItems = collect($ppesItems)->filter(fn($i) => empty($i->is_section_label));
        @endphp

        {{-- PANGASINAN banner --}}
        <tr class="banner">
            <td colspan="18" style="border-left:1pt solid #000; border-right:1pt solid #000; border-top:1pt solid #000; border-bottom:0.5pt solid #000;">PANGASINAN</td>
        </tr>

        @forelse($ppesItems as $item)
            @if(!empty($item->is_section_label))
                <tr class="banner">
                    <td colspan="18" style="border-left:1pt solid #000; border-right:1pt solid #000; border-top:1pt solid #000; border-bottom:0.5pt solid #000;">{{ $item->label }}</td>
                </tr>
            @else
            @php
                $totalDisposal = (float)($item->sale ?? 0)
                               + (float)($item->transfer ?? 0)
                               + (float)($item->destruction ?? 0)
                               + (float)($item->others ?? 0);
                $carryingAmt   = (float)($item->carrying_amount ?? 0);
                $accumDepr     = (float)($item->accumulated_depreciation ?? 0);
                $accumImp      = (float)($item->accumulated_impairment_losses ?? 0);
                $appraisedVal  = (float)($item->appraised_value ?? 0);
                $saleAmt       = (float)($item->sale ?? 0);
                $transferAmt   = (float)($item->transfer ?? 0);
                $destructAmt   = (float)($item->destruction ?? 0);
                $othersAmt     = (float)($item->others ?? 0);
                $amount        = (float)($item->amount ?? 0);
            @endphp
            <tr>
                <td style="border-left:1pt solid #000;">{{ $item->date_acquired }}</td>
                <td class="tdl">{{ $item->particulars_articles }}</td>
                <td>{{ $item->property_no }}</td>
                <td>{{ $item->qty }}</td>
                <td class="tdr">{{ number_format((float)($item->unit_cost ?? 0), 2) }}</td>
                <td class="tdr">{{ (float)($item->total_cost ?? 0) != 0 ? number_format((float)$item->total_cost, 2) : '' }}</td>
                <td class="tdr">{{ $accumDepr  != 0 ? number_format($accumDepr,  2) : '' }}</td>
                <td class="tdr">{{ $accumImp   != 0 ? number_format($accumImp,   2) : '' }}</td>
                <td class="tdr">{{ $carryingAmt != 0 ? number_format($carryingAmt, 2) : '' }}</td>
                <td class="tdl">{{ $item->remarks }}</td>
                <td class="tdr">{{ $saleAmt     != 0 ? number_format($saleAmt,     2) : '' }}</td>
                <td class="tdr">{{ $transferAmt != 0 ? number_format($transferAmt, 2) : '' }}</td>
                <td class="tdr">{{ $destructAmt != 0 ? number_format($destructAmt, 2) : '' }}</td>
                <td class="tdr">{{ $othersAmt   != 0 ? number_format($othersAmt,   2) : '' }}</td>
                <td class="tdr">{{ $totalDisposal > 0 ? number_format($totalDisposal, 2) : '' }}</td>
                <td class="tdr">{{ $appraisedVal != 0 ? number_format($appraisedVal, 2) : '' }}</td>
                <td>{{ $item->or_no }}</td>
                <td class="tdr" style="border-right:1pt solid #000;">{{ $amount != 0 ? number_format($amount, 2) : '' }}</td>
            </tr>
            @endif
        @empty
            <tr>
                <td colspan="18" style="text-align:center; padding:6pt; border-left:1pt solid #000; border-right:1pt solid #000;">
                    No unserviceable equipment found.
                </td>
            </tr>
        @endforelse

        {{-- Totals row --}}
        @if($dataItems->count() > 0)
        <tr class="row-total">
            <td style="border-left:1pt solid #000; border-top:1pt solid #000; border-bottom:1pt solid #000;"></td>
            <td style="border-top:1pt solid #000; border-bottom:1pt solid #000;"></td>
            <td style="border-top:1pt solid #000; border-bottom:1pt solid #000;"></td>
            <td style="border-top:1pt solid #000; border-bottom:1pt solid #000;"></td>
            <td class="tdr" style="border-top:1pt solid #000; border-bottom:1pt solid #000;">{{ number_format($dataItems->sum(fn($i) => (float)($i->unit_cost ?? 0)), 2) }}</td>
            <td class="tdr" style="border-top:1pt solid #000; border-bottom:1pt solid #000;">{{ number_format($dataItems->sum(fn($i) => (float)($i->total_cost ?? 0)), 2) }}</td>
            <td class="tdr" style="border-top:1pt solid #000; border-bottom:1pt solid #000;">{{ number_format($dataItems->sum(fn($i) => (float)($i->accumulated_depreciation ?? 0)), 2) }}</td>
            <td style="border-top:1pt solid #000; border-bottom:1pt solid #000;"></td>
            <td class="tdr" style="border-top:1pt solid #000; border-bottom:1pt solid #000;">{{ number_format($dataItems->sum(fn($i) => (float)($i->carrying_amount ?? 0)), 2) }}</td>
            <td style="border-top:1pt solid #000; border-bottom:1pt solid #000;"></td>
            <td class="tdr" style="border-top:1pt solid #000; border-bottom:1pt solid #000;">{{ number_format($dataItems->sum(fn($i) => (float)($i->sale ?? 0)), 2) }}</td>
            <td class="tdr" style="border-top:1pt solid #000; border-bottom:1pt solid #000;">{{ number_format($dataItems->sum(fn($i) => (float)($i->transfer ?? 0)), 2) }}</td>
            <td class="tdr" style="border-top:1pt solid #000; border-bottom:1pt solid #000;">{{ number_format($dataItems->sum(fn($i) => (float)($i->destruction ?? 0)), 2) }}</td>
            <td class="tdr" style="border-top:1pt solid #000; border-bottom:1pt solid #000;">{{ number_format($dataItems->sum(fn($i) => (float)($i->others ?? 0)), 2) }}</td>
            <td class="tdr" style="border-top:1pt solid #000; border-bottom:1pt solid #000;">{{ number_format($dataItems->sum(fn($i) => (float)($i->sale ?? 0) + (float)($i->transfer ?? 0) + (float)($i->destruction ?? 0) + (float)($i->others ?? 0)), 2) }}</td>
            <td class="tdr" style="border-top:1pt solid #000; border-bottom:1pt solid #000;">{{ number_format($dataItems->sum(fn($i) => (float)($i->appraised_value ?? 0)), 2) }}</td>
            <td style="border-top:1pt solid #000; border-bottom:1pt solid #000;"></td>
            <td class="tdr" style="border-top:1pt solid #000; border-bottom:1pt solid #000; border-right:1pt solid #000;">{{ number_format($dataItems->sum(fn($i) => (float)($i->amount ?? 0)), 2) }}</td>
        </tr>
        @endif
    </tbody>
</table>

{{-- ════ FOOTER ════ --}}
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

<div class="footer-wrap">
    <table>
        <colgroup>
            <col style="width:4.01%">
            <col style="width:27.67%">
            <col style="width:18.87%">
            <col style="width:12.95%">
            <col style="width:16.43%">
            <col style="width:5.20%">
            <col style="width:14.87%">
        </colgroup>

        {{-- Top border row --}}
        <tr style="height:4pt;">
            <td style="border-top:1.5pt solid #000; border-left:1.5pt solid #000; border-bottom:none; border-right:none;"></td>
            <td style="border-top:1.5pt solid #000; border-left:none; border-bottom:none; border-right:none;"></td>
            <td style="border-top:1.5pt solid #000; border-left:none; border-bottom:none; border-right:none;"></td>
            <td style="border-top:1.5pt solid #000; border-left:none; border-bottom:none; border-right:1.5pt solid #000;"></td>
            <td style="border-top:1.5pt solid #000; border-left:1.5pt solid #000; border-bottom:none; border-right:none;"></td>
            <td style="border-top:1.5pt solid #000; border-left:none; border-bottom:none; border-right:none;"></td>
            <td style="border-top:1.5pt solid #000; border-left:none; border-bottom:none; border-right:1.5pt solid #000;"></td>
        </tr>

        {{-- Main content row: left box (cert+sigs) | insp cert | gap | witness cert --}}
        <tr>
            {{--
                LEFT BOX: single colspan=4 cell containing a nested table with:
                  Row A — cert text (full width)
                  Row B — "Requested by:" | "Approved by:" labels
                  Row C — REQ name (centered, bold) | APPR name (centered, bold)
                  Row D — REQ role | APPR role
                This keeps the whole left-box signature block self-contained so
                DomPDF cannot split the rows apart.
            --}}
            <td colspan="4" style="padding:3pt 4pt 2pt 4pt; vertical-align:top;
                                   border-left:1.5pt solid #000; border-right:1.5pt solid #000;
                                   border-top:none; border-bottom:none; font-size:5.5pt;">
                <table style="width:100%; border-collapse:collapse; font-size:5.5pt; font-family:Arial,Helvetica,sans-serif;">

                    {{-- Row A: cert text --}}
                    <tr>
                        <td colspan="2" style="border:none; padding:0 0 7pt 0; vertical-align:top; text-align:left;">
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;I HEREBY request inspection and disposition, pursuant to Section&nbsp;&nbsp;79 of PD 1445, of the property enumerated above.
                        </td>
                    </tr>

                    {{-- Row B: labels --}}
                    <tr>
                        <td style="width:50%; border:none; padding:0 2pt 2pt 0; text-align:left; vertical-align:bottom; font-size:5.5pt;">
                            Requested by:
                        </td>
                        <td style="width:50%; border:none; padding:0 0 2pt 2pt; text-align:left; vertical-align:bottom; font-size:5.5pt;">
                            Approved by:
                        </td>
                    </tr>

                    {{-- Row C: names --}}
                    <tr style="height:14pt;">
                        <td style="width:50%; border:none; padding:1pt 2pt 0 0; text-align:center; vertical-align:bottom; font-weight:bold; font-size:5.5pt;">
                            {{ strtoupper($fReqName) }}
                        </td>
                        <td style="width:50%; border:none; padding:1pt 0 0 2pt; text-align:center; vertical-align:bottom; font-weight:bold; font-size:5.5pt;">
                            {{ strtoupper($fApprName) }}
                        </td>
                    </tr>

                    {{-- Row D: roles --}}
                    <tr>
                        <td style="width:50%; border:none; padding:0 2pt 0 0; text-align:center; vertical-align:top; font-size:5pt;">
                            {{ $fReqRole }}
                        </td>
                        <td style="width:50%; border:none; padding:0 0 0 2pt; text-align:center; vertical-align:top; font-size:5pt;">
                            {{ $fApprRole }}
                        </td>
                    </tr>

                </table>
            </td>

            {{-- Inspection cert --}}
            <td style="padding:3pt 4pt; vertical-align:top;
                       border-left:1.5pt solid #000; border-right:none;
                       border-top:none; border-bottom:none; font-size:5.5pt;">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;I CERTIFY that I have inspected each and every article enumerated in this report, and that the disposition made thereof was, in my judgment, the best for the public interest.
                <br><br>
                <table style="width:100%; border-collapse:collapse; font-size:5.5pt;">
                    <tr>
                        <td style="border:none; border-bottom:0.5pt solid #000; text-align:center; font-weight:bold; padding:12pt 0 0; font-size:5.5pt;">
                            {{ strtoupper($fInspName) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="border:none; text-align:center; font-size:5pt; padding:0;">
                            {{ $fInspRole }}
                        </td>
                    </tr>
                </table>
            </td>

            {{-- Gap --}}
            <td style="border:none;"></td>

            {{-- Witness cert --}}
            <td style="padding:3pt 4pt; vertical-align:top;
                       border-left:none; border-right:1.5pt solid #000;
                       border-top:none; border-bottom:none; font-size:5.5pt;">
                &nbsp;&nbsp;&nbsp;&nbsp;I CERTIFY that I have witnessed the disposition of the articles enumerated on this report this ____day of _____________, _____.
                <br><br>
                <table style="width:100%; border-collapse:collapse; font-size:5.5pt;">
                    <tr>
                        <td style="border:none; border-bottom:0.5pt solid #000; text-align:center; font-weight:bold; padding:12pt 0 0; font-size:5.5pt;">
                            {{ strtoupper($fAudName) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="border:none; text-align:center; font-size:5pt; padding:0;">
                            {{ $fAudRole }}<br>{{ $fAudRole2 }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        {{-- Bottom border row --}}
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

</div>{{-- /.page-wrap --}}
</body>
</html>