<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>IIRUP Report</title>
    <style>
        /*
         * DomPDF: @page controls printable area; table-layout:fixed + explicit widths is the only
         * reliable column sizing method; flex/grid NOT supported — use tables only.
         *
         * Column proportions match XLS exactly (B–S total = 183.84 units):
         *   B=7.62  C=24.86  D=9.49  E=5.62  F=12.62  G=13.49  H=12.49  I=9.86
         *   J=11.49  K+L=18.73(merged)  M=6.37  N=11.12  O=8.12  P=9.86  Q=11.49
         *   R=8.37  S=9.86
         * As % of 183.84:  B=4.1  C=13.5  D=5.2  E=3.1  F=6.9  G=7.3  H=6.8
         *   I=5.4  J=6.2  KL=10.2  M=3.5  N=6.0  O=4.4  P=5.4  Q=6.2  R=4.6  S=5.4
         */
        @page { size: 297mm 210mm; margin: 8mm 10mm 8mm 10mm; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            background: white;
            color: #000;
            line-height: 1.2;
        }

        /* ── Row 1: Appendix 74 ── */
        .appendix {
            text-align: right;
            font-size: 7pt;
            margin-bottom: 1pt;
        }

        /* ── Row 3: Title — Times New Roman 14pt bold, centered, UPPERCASE ── */
        .doc-title {
            font-family: 'Times New Roman', serif;
            font-size: 14pt;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            margin-bottom: 2pt;
        }

        /* ── Row 4: As of — Times New Roman 12pt centered ── */
        .doc-asof {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            text-align: center;
            margin-bottom: 4pt;
        }
        .doc-asof .blank-date {
            display: inline-block;
            border-bottom: 0.5pt solid #000;
            min-width: 140pt;
            vertical-align: bottom;
        }

        /* ── Row 6: Entity / Fund Cluster ── */
        .doc-meta { width: 100%; margin-bottom: 2pt; }
        .doc-meta table { width: 100%; border-collapse: collapse; }
        .doc-meta td { border: none; padding: 0; vertical-align: bottom; font-size: 8pt; }

        /* ── Row 7: Accountable Officer ── */
        .doc-officer { width: 100%; margin-bottom: 0; }
        .doc-officer table { width: 100%; border-collapse: collapse; }
        .doc-officer td { border: none; padding: 0 2pt; font-size: 8pt; text-align: center; vertical-align: bottom; }
        .doc-officer .ul { border-bottom: 0.5pt solid #000; }

        /* ── Row 8: Italic labels ── */
        .doc-labels { width: 100%; margin-bottom: 5pt; }
        .doc-labels table { width: 100%; border-collapse: collapse; }
        .doc-labels td { border: none; padding: 0 2pt; font-size: 7pt; font-style: italic; text-align: center; }

        /* ════════════════════════════════════════════
           MAIN TABLE — exact XLS column proportions
           ════════════════════════════════════════════ */
        table.main {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 5.5pt;
            margin-bottom: 4pt;
        }
        table.main th, table.main td {
            border: 0.5pt solid #000;
            padding: 1pt 1.5pt;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
            overflow-wrap: break-word;
            line-height: 1.2;
        }
        table.main th { font-weight: bold; background: white; }
        table.main td.tdl { text-align: left; }
        table.main td.tdr { text-align: right; }
        table.main tr.row-total td { font-weight: bold; }
        table.main tr.banner td { font-weight: bold; text-align: center; }

        /* ── Certification boxes ── */
        table.cert {
            width: 100%;
            border-collapse: collapse;
            font-size: 5.5pt;
            margin-bottom: 0;
        }
        table.cert td {
            border: 0.5pt solid #000;
            padding: 3pt 4pt;
            vertical-align: top;
        }

        /* ── Signature blocks ── */
        table.sigs {
            width: 100%;
            border-collapse: collapse;
            font-size: 5.5pt;
            margin-top: 6pt;
        }
        table.sigs td { padding: 0 4pt; vertical-align: bottom; border: none; }
        .sc { font-style: italic; display: block; margin-bottom: 12pt; font-size: 5.5pt; }
        .sn { border-bottom: 0.5pt solid #000; font-weight: bold; text-align: center; display: block; min-height: 10pt; font-size: 5.5pt; }
        .sr { text-align: center; display: block; margin-top: 1pt; font-size: 5pt; }
    </style>
</head>
<body>

{{-- Row 1: Appendix 74 --}}
<div class="appendix">Appendix  74</div>

{{-- Row 3: Title — Times NR 14pt bold, centered, uppercase --}}
<div class="doc-title">INVENTORY AND INSPECTION REPORT OF UNSERVICEABLE PROPERTY</div>

{{-- Row 4: As of --}}
<div class="doc-asof">
    @if(!empty($header['as_of']) && trim($header['as_of']) !== '')
        As of &nbsp; {{ $header['as_of'] }}
    @else
        As of &nbsp; <span class="blank-date">&nbsp;</span>
    @endif
</div>

{{-- Row 6: Entity Name | Fund Cluster — bold, matching XLS --}}
<div class="doc-meta">
    <table>
        <tr>
            <td style="width:65%; text-align:left;">
                <strong>Entity Name:</strong>&nbsp;{{ $header['entity_name'] ?? '' }}
            </td>
            <td style="width:35%; text-align:right;">
                <strong>Fund Cluster :</strong>&nbsp;{{ $header['fund_cluster'] ?? '' }}
            </td>
        </tr>
    </table>
</div>

{{-- Row 7: Accountable | Designation | Station (underlined, matching XLS B7:E7 / F7:I7 / K7:N7) --}}
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

{{-- Row 8: Italic labels --}}
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
  Main Table — 18 data columns matching XLS B–S proportions exactly.
  KL merged (Remarks) matches K:L merged per row in XLS.

  Col widths as % (total = 100%):
  B=4.1  C=13.5  D=5.2  E=3.1  F=6.9  G=7.3  H=6.8  I=5.4  J=6.2
  KL(merged)=10.2  M=3.5  N=6.0  O=4.4  P=5.4  Q=6.2  R=4.6  S=5.4
--}}
<table class="main">
    <colgroup>
        <col style="width:4.14%">   {{-- B: Date Acquired --}}
        <col style="width:13.53%">  {{-- C: Particulars/Articles --}}
        <col style="width:5.17%">   {{-- D: Property No. --}}
        <col style="width:3.06%">   {{-- E: Qty --}}
        <col style="width:6.86%">   {{-- F: Unit Cost --}}
        <col style="width:7.34%">   {{-- G: Total Cost --}}
        <col style="width:6.80%">   {{-- H: Accumulated Depreciation --}}
        <col style="width:5.37%">   {{-- I: Accumulated Impairment Losses --}}
        <col style="width:6.25%">   {{-- J: Carrying Amount --}}
        <col style="width:10.19%">  {{-- K+L merged: Remarks --}}
        <col style="width:3.47%">   {{-- M: Sale --}}
        <col style="width:6.05%">   {{-- N: Transfer --}}
        <col style="width:4.42%">   {{-- O: Destruction --}}
        <col style="width:5.37%">   {{-- P: Others (Specify) --}}
        <col style="width:6.25%">   {{-- Q: Total Disposal --}}
        <col style="width:6.25%">   {{-- Q: Appraised Value --}}
        <col style="width:4.56%">   {{-- R: OR No. --}}
        <col style="width:4.56%">   {{-- S: Amount --}}
    </colgroup>
    <thead>
        {{-- INVENTORY (cols 1–10) | INSPECTION and DISPOSAL (cols 11–18) --}}
        <tr>
            <th colspan="10">INVENTORY</th>
            <th colspan="8">INSPECTION and DISPOSAL</th>
        </tr>
        {{-- Column labels row 1: singles span 3 rows; DISPOSAL spans cols 11-15; REC.SALES spans 16-18 --}}
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
        <tr>
            <th rowspan="2">Sale</th>
            <th rowspan="2">Transfer</th>
            <th rowspan="2">Destruction</th>
            <th rowspan="2">Others<br>(Specify)</th>
            <th rowspan="2">Total</th>
            <th rowspan="2">OR No.</th>
            <th rowspan="2">Amount</th>
        </tr>
        <tr></tr>
        <tr>
            <th>(1)</th><th>(2)</th><th>(3)</th><th>(4)</th>
            <th>(5)</th><th>(6)</th><th>(7)</th><th>(8)</th>
            <th>(9)</th><th>(10)</th><th>(11)</th><th>(12)</th>
            <th>(13)</th><th>(14)</th><th>(15)</th><th>(16)</th>
            <th>(17)</th><th>(18)</th>
        </tr>
    </thead>
    <tbody>
        @php
            $dataItems = collect($ppesItems)->filter(fn($i) => empty($i->is_section_label));
        @endphp

        {{-- Row 15: PANGASINAN banner — always shown, mirrors XLS B15:S15 --}}
        <tr class="banner"><td colspan="18">PANGASINAN</td></tr>

        @forelse($ppesItems as $item)
            @if(!empty($item->is_section_label))
                <tr class="banner"><td colspan="18">{{ $item->label }}</td></tr>
            @else
            <tr>
                <td>{{ $item->date_acquired }}</td>
                <td class="tdl">{{ $item->particulars_articles }}</td>
                <td>{{ $item->property_no }}</td>
                <td>{{ $item->qty }}</td>
                <td class="tdr">{{ number_format((float)($item->unit_cost ?? 0), 2) }}</td>
                <td class="tdr">{{ (float)($item->total_cost ?? 0) ? number_format((float)$item->total_cost, 2) : '' }}</td>
                <td class="tdr">{{ (float)($item->accumulated_depreciation ?? 0) ? number_format((float)$item->accumulated_depreciation, 2) : '' }}</td>
                <td class="tdr">{{ (float)($item->accumulated_impairment_losses ?? 0) ? number_format((float)$item->accumulated_impairment_losses, 2) : '' }}</td>
                <td class="tdr">{{ (float)($item->carrying_amount ?? 0) ? number_format((float)$item->carrying_amount, 2) : '' }}</td>
                <td class="tdl">{{ $item->remarks }}</td>
                <td>{{ $item->sale }}</td>
                <td>{{ $item->transfer }}</td>
                <td>{{ $item->destruction }}</td>
                <td>{{ $item->others }}</td>
                <td class="tdr">{{ ($item->total_disposal !== '' && (float)($item->total_disposal ?? 0)) ? number_format((float)$item->total_disposal, 2) : '' }}</td>
                <td class="tdr">{{ (float)($item->appraised_value ?? 0) ? number_format((float)$item->appraised_value, 2) : '' }}</td>
                <td>{{ $item->or_no }}</td>
                <td class="tdr">{{ (float)($item->amount ?? 0) ? number_format((float)$item->amount, 2) : '' }}</td>
            </tr>
            @endif
        @empty
            <tr><td colspan="18" style="text-align:center;padding:6pt;">No unserviceable equipment found.</td></tr>
        @endforelse

        {{-- Totals row — mirrors XLS SUM row --}}
        @if($dataItems->count() > 0)
        <tr class="row-total">
            <td colspan="4"></td>
            <td class="tdr">{{ number_format($dataItems->sum(fn($i) => (float)($i->unit_cost ?? 0)), 2) }}</td>
            <td class="tdr">{{ number_format($dataItems->sum(fn($i) => (float)($i->total_cost ?? 0)), 2) }}</td>
            <td class="tdr">{{ number_format($dataItems->sum(fn($i) => (float)($i->accumulated_depreciation ?? 0)), 2) }}</td>
            <td></td>
            <td class="tdr">{{ number_format($dataItems->sum(fn($i) => (float)($i->carrying_amount ?? 0)), 2) }}</td>
            <td></td><td></td><td></td><td></td><td></td><td></td>
            <td class="tdr">{{ number_format($dataItems->sum(fn($i) => (float)($i->appraised_value ?? 0)), 2) }}</td>
            <td></td>
            <td class="tdr">{{ number_format($dataItems->sum(fn($i) => (float)($i->amount ?? 0)), 2) }}</td>
        </tr>
        @endif
    </tbody>
</table>

{{--
  ══ FOOTER — pixel-perfect replica of XLS rows 133–142 ══
  Row 133: medium TOP across full width (top frame)
  Rows 134–139: 3 cert boxes, medium L+R borders only
    B134:K135 = left box  (55%, 2 rows, vertically centered)
    L134:O139 = mid  box  (23%, 6 rows, top-aligned)
    Q134:S138 = right box (22%, 5 rows, top-aligned)
  Row 140: sig names — C:F NO underline, G:I NO underline, L:O thin BOTTOM, Q:S thin BOTTOM
  Row 141: sig roles — C:F no border, G:I no border, L:O thin TOP, Q:S thin TOP
  Row 142: medium BOTTOM across full width (bottom frame)
--}}
@php
    $fReqName  = $header['f_req_name']  ?? 'FRANKLIN A. SALCEDO';
    $fReqRole  = $header['f_req_role']  ?? 'Supply and Property Officer';
    $fApprName = $header['f_appr_name'] ?? 'JAYVEE BRYAN G. CARILLO, Ph.D.';
    $fApprRole = $header['f_appr_role'] ?? 'Center Director';
    $fInspName = $header['f_insp_name'] ?? 'JOSE O. KANLAS,JR.';
    $fInspRole = $header['f_insp_role'] ?? 'Inspection Officer';
    $fAudName  = $header['f_aud_name']  ?? 'JELANIE S. WANAWAN';
    $fAudRole  = $header['f_aud_role']  ?? 'State Auditor II';
    $fAudRole2 = $header['f_aud_role2'] ?? 'OIC - Audit Team Leader';
@endphp

{{-- Row 133: medium TOP border = top frame of footer block --}}
<div style="width:100%; border-top:1pt solid #000; margin-top:3pt;"></div>

{{-- Rows 134–139: Cert boxes — side borders only (no top/bottom on the cells) --}}
<table style="width:100%; border-collapse:collapse; font-size:5.5pt; line-height:1.4; margin:0;">
    <tr>
        <td style="width:55%; padding:3pt 5pt; vertical-align:middle;
                   border-left:1pt solid #000; border-right:1pt solid #000;
                   border-top:none; border-bottom:none; min-height:22pt;">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;I HEREBY request inspection and disposition, pursuant to Section&nbsp;&nbsp;79 of PD 1445, of the property enumerated above.
        </td>
        <td style="width:23%; padding:3pt 5pt; vertical-align:top;
                   border-left:1pt solid #000; border-right:1pt solid #000;
                   border-top:none; border-bottom:none; min-height:42pt;">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;I CERTIFY that I have inspected each and every article enumerated in this report, and that the disposition made thereof was, in my judgment, the best for the public interest.
        </td>
        <td style="width:22%; padding:3pt 5pt; vertical-align:top;
                   border-left:1pt solid #000; border-right:1pt solid #000;
                   border-top:none; border-bottom:none; min-height:36pt;">
            &nbsp;&nbsp;&nbsp;&nbsp;I CERTIFY that I have witnessed the disposition of the articles enumerated on this report this ____day of _____________, _____.
        </td>
    </tr>
</table>

{{-- Row 142: medium BOTTOM border = bottom frame of footer block --}}
<div style="width:100%; border-bottom:1pt solid #000; margin-bottom:4pt;"></div>

{{-- Rows 140–141: Sig names + roles --}}
<table style="width:100%; border-collapse:collapse; font-size:5.5pt; margin-top:2pt;">
    {{-- Row 140: sig names --}}
    <tr>
        {{-- C140:F140 — Requested by — NO underline --}}
        <td style="width:25%; padding:0 4pt 0; vertical-align:bottom; border:none; text-align:center;">
            <div style="font-style:italic; margin-bottom:9pt; font-size:5.5pt; text-align:left;">Requested by:</div>
            <div style="font-weight:bold; font-size:5.5pt;">{{ strtoupper($fReqName) }}</div>
        </td>
        {{-- G140:I140 — Approved by — NO underline --}}
        <td style="width:25%; padding:0 4pt 0; vertical-align:bottom; border:none; text-align:center;">
            <div style="font-style:italic; margin-bottom:9pt; font-size:5.5pt; text-align:left;">Approved by:</div>
            <div style="font-weight:bold; font-size:5.5pt;">{{ strtoupper($fApprName) }}</div>
        </td>
        {{-- L140:O140 — Inspection Officer — thin BOTTOM underline --}}
        <td style="width:25%; padding:0 4pt 0; vertical-align:bottom; border:none; text-align:center;">
            <div style="margin-bottom:9pt; font-size:5.5pt;">&nbsp;</div>
            <div style="font-weight:bold; font-size:5.5pt; border-bottom:0.5pt solid #000; padding-bottom:1pt;">{{ strtoupper($fInspName) }}</div>
        </td>
        {{-- Q140:S140 — State Auditor — thin BOTTOM underline --}}
        <td style="width:25%; padding:0 4pt 0; vertical-align:bottom; border:none; text-align:center;">
            <div style="margin-bottom:9pt; font-size:5.5pt;">&nbsp;</div>
            <div style="font-weight:bold; font-size:5.5pt; border-bottom:0.5pt solid #000; padding-bottom:1pt;">{{ strtoupper($fAudName) }}</div>
        </td>
    </tr>
    {{-- Row 141: sig roles --}}
    <tr>
        {{-- C141:F141 — Requested by role — NO border --}}
        <td style="width:25%; padding:1pt 4pt 0; vertical-align:top; border:none; text-align:center; font-size:5pt;">
            {{ $fReqRole }}
        </td>
        {{-- G141:I141 — Approved by role — NO border --}}
        <td style="width:25%; padding:1pt 4pt 0; vertical-align:top; border:none; text-align:center; font-size:5pt;">
            {{ $fApprRole }}
        </td>
        {{-- L141:O141 — Inspection Officer role — thin TOP border --}}
        <td style="width:25%; padding:1pt 4pt 0; vertical-align:top; text-align:center; font-size:5pt;
                   border-top:0.5pt solid #000; border-left:none; border-right:none; border-bottom:none;">
            {{ $fInspRole }}
        </td>
        {{-- Q141:S141 — Auditor roles — thin TOP border, two lines --}}
        <td style="width:25%; padding:1pt 4pt 0; vertical-align:top; text-align:center; font-size:5pt;
                   border-top:0.5pt solid #000; border-left:none; border-right:none; border-bottom:none;">
            {{ $fAudRole }}<br>{{ $fAudRole2 }}
        </td>
    </tr>
</table>

</body>
</html>