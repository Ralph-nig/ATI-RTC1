{{-- filepath: resources/views/client/report/ics/print.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ICS {{ $equipment->document_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            color: #000;
            background: #fff;
        }

        @media screen {
            body { background: #ccc; padding: 24px; }
            .print-paper {
                background: #fff;
                width: 216mm; /* Letter width */
                margin: 0 auto;
                padding: 10mm 12mm;
                box-shadow: 0 4px 24px rgba(0,0,0,.25);
            }
            .print-toolbar {
                width: 216mm;
                margin: 0 auto 12px;
                display: flex;
                justify-content: flex-end;
                gap: 8px;
            }
            .print-btn {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                padding: 7px 16px;
                border-radius: 6px;
                font-size: 13px;
                font-weight: 600;
                cursor: pointer;
                border: none;
                text-decoration: none;
                font-family: Arial, sans-serif;
            }
            .btn-primary   { background: #296218; color: #fff; }
            .btn-secondary { background: #6c757d; color: #fff; }
        }

        @media print {
            body { background: #fff; }
            .print-paper { width: 100%; padding: 8mm 10mm; }
            .print-toolbar { display: none !important; }
            @page { size: letter portrait; margin: 0; }
        }

        /* ══ LETTERHEAD ══ */
        .letterhead {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            margin-bottom: 3px;
        }
        .lh-logos {
            display: flex;
            align-items: center;
            gap: 5px;
            flex-shrink: 0;
        }
        .lh-logos img { height: 62px; width: auto; object-fit: contain; }
        .lh-body { flex: 1; }
        .lh-republic { font-size: 9pt; }
        .lh-dept     { font-size: 9pt; color: #12410d; }
        .lh-agency   { font-size: 13pt; color: #12410d; font-weight: 700; text-transform: uppercase; line-height: 1.2; }
        .lh-addr     { font-size: 6pt; font-weight: 700; color: #222; margin-top: 2px; line-height: 1.4; }
        /* Appendix 59 — 14pt TNR, right-aligned */
        .lh-appendix { font-size: 14pt; font-style: italic; white-space: nowrap; align-self: flex-start; }

        /* ══ TITLE ══
           XLS row 3: merged cols 0-7, 14pt Times New Roman bold, centered
        ══ */
        .form-title {
            text-align: center;
            font-size: 14pt;
            font-weight: 700;
            text-transform: uppercase;
            margin: 6px 0 3px;
        }

        /* ══ HEADER FIELDS (rows 6-7, no borders in XLS) ══ */
        .hdr-block {
            font-size: 11pt;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .hdr-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-bottom: 1px;
        }
        .hdr-val {
            display: inline-block;
            min-width: 160px;
            border-bottom: 1px solid #000;
            font-weight: normal;
            padding: 0 3px;
        }

        /* ══ MAIN TABLE ══
           XLS col widths (256-units → %):
           Total = 2377+1682+2194+3254+3766+3072+3328+3657 = 23330
           C0=10.19% C1=7.21% C2=9.41% C3=13.95% C4=16.15% C5=13.17% C6=14.27% C7=15.68%

           Header spans (rows 9-11):
             Qty     = C0, rows 9-11 merged (3 rows)
             Unit    = C1, rows 9-11 merged
             Amount  = C2-C3 merged (Unit Cost / Total Cost sub-cols), rows 9-11
                       Row 10: C2=Unit Cost, C3=Total Cost
             Desc    = C4-C5 merged, rows 9-11
             Inv No  = C6, rows 9-11 merged
             Est Life= C7, rows 9-11 merged

           Data rows start row 12 (tall row = main data row, 91.5pt)
           Row 13 = next item row
           Signature block starts row 14.
        ══ */
        table { border-collapse: collapse; width: 100%; }

        .ics-table { table-layout: fixed; }

        /* col widths */
        .ics-table colgroup col:nth-child(1) { width: 10.19%; } /* Qty */
        .ics-table colgroup col:nth-child(2) { width:  7.21%; } /* Unit */
        .ics-table colgroup col:nth-child(3) { width:  9.41%; } /* Unit Cost */
        .ics-table colgroup col:nth-child(4) { width: 13.95%; } /* Total Cost */
        .ics-table colgroup col:nth-child(5) { width: 16.15%; } /* Description (merged w/ C5) */
        .ics-table colgroup col:nth-child(6) { width: 13.17%; } /* Description cont. */
        .ics-table colgroup col:nth-child(7) { width: 14.27%; } /* Inventory Item No. */
        .ics-table colgroup col:nth-child(8) { width: 15.68%; } /* Estimated Useful Life */

        .ics-table th, .ics-table td {
            font-size: 12pt;
            font-family: 'Times New Roman', Times, serif;
            padding: 2px 3px;
            vertical-align: middle;
            text-align: center;
        }

        /* ── Header rows (9-11) ── */
        /* Row 9: outer group headers — thick top & sides */
        .ics-table tr.hdr-r9 th {
            font-weight: 700;
            border-top:    2px solid #000;
            border-bottom: 0;
        }
        .ics-table tr.hdr-r9 th.c-qty  { border-left: 2px solid #000; border-right: 2px solid #000; }
        .ics-table tr.hdr-r9 th.c-unit { border-left: 2px solid #000; border-right: 2px solid #000; }
        .ics-table tr.hdr-r9 th.c-amt  { border-left: 2px solid #000; border-right: 0; }
        .ics-table tr.hdr-r9 th.c-amtr { border-left: 0;              border-right: 2px solid #000; }
        .ics-table tr.hdr-r9 th.c-desc { border-left: 2px solid #000; border-right: 0; }
        .ics-table tr.hdr-r9 th.c-descr{ border-left: 0;              border-right: 2px solid #000; }
        .ics-table tr.hdr-r9 th.c-inv  { border-left: 2px solid #000; border-right: 2px solid #000; }
        .ics-table tr.hdr-r9 th.c-life { border-left: 2px solid #000; border-right: 2px solid #000; }

        /* Row 10: sub-header — Amount splits into Unit Cost | Total Cost */
        .ics-table tr.hdr-r10 th {
            font-weight: normal;
            font-size: 11pt;
            border-top: 0;
            border-bottom: 0;
        }
        .ics-table tr.hdr-r10 th.c-qty  { border-left: 2px solid #000; border-right: 2px solid #000; }
        .ics-table tr.hdr-r10 th.c-unit { border-left: 2px solid #000; border-right: 2px solid #000; }
        .ics-table tr.hdr-r10 th.c-uc   { border-left: 2px solid #000; border-right: 1px solid #000; }
        .ics-table tr.hdr-r10 th.c-tc   { border-left: 0;              border-right: 2px solid #000; }
        .ics-table tr.hdr-r10 th.c-desc { border-left: 2px solid #000; border-right: 0; }
        .ics-table tr.hdr-r10 th.c-descr{ border-left: 0;              border-right: 2px solid #000; }
        .ics-table tr.hdr-r10 th.c-inv  { border-left: 2px solid #000; border-right: 2px solid #000; }
        .ics-table tr.hdr-r10 th.c-life { border-left: 2px solid #000; border-right: 2px solid #000; }

        /* Row 11: bottom of header — thick bottom border */
        .ics-table tr.hdr-r11 th {
            font-weight: normal;
            font-size: 10pt;
            border-top: 0;
            border-bottom: 2px solid #000;
        }
        .ics-table tr.hdr-r11 th.c-qty  { border-left: 2px solid #000; border-right: 2px solid #000; }
        .ics-table tr.hdr-r11 th.c-unit { border-left: 2px solid #000; border-right: 2px solid #000; }
        .ics-table tr.hdr-r11 th.c-uc   { border-left: 2px solid #000; border-right: 1px solid #000; }
        .ics-table tr.hdr-r11 th.c-tc   { border-left: 0;              border-right: 2px solid #000; }
        .ics-table tr.hdr-r11 th.c-desc { border-left: 2px solid #000; border-right: 0; }
        .ics-table tr.hdr-r11 th.c-descr{ border-left: 0;              border-right: 2px solid #000; }
        .ics-table tr.hdr-r11 th.c-inv  { border-left: 2px solid #000; border-right: 2px solid #000; }
        .ics-table tr.hdr-r11 th.c-life { border-left: 2px solid #000; border-right: 2px solid #000; }

        /* ── Data rows ── */
        .ics-table tbody tr { height: 20px; }
        .ics-table tbody td {
            border: 1px solid #000;
            font-size: 11pt;
        }
        /* Outer left/right thick borders */
        .ics-table tbody td:nth-child(1) { border-left:  2px solid #000; border-right: 2px solid #000; }
        .ics-table tbody td:nth-child(2) { border-left:  2px solid #000; border-right: 2px solid #000; }
        .ics-table tbody td:nth-child(3) { border-left:  2px solid #000; border-right: 1px solid #000; }
        .ics-table tbody td:nth-child(4) { border-left:  0;              border-right: 2px solid #000; }
        .ics-table tbody td:nth-child(5) { border-left:  2px solid #000; border-right: 0; }
        .ics-table tbody td:nth-child(6) { border-left:  0;              border-right: 2px solid #000; }
        .ics-table tbody td:nth-child(7) { border-left:  2px solid #000; border-right: 2px solid #000; }
        .ics-table tbody td:nth-child(8) { border-left:  2px solid #000; border-right: 2px solid #000; }
        .ics-table tbody td.desc-cell    { text-align: left; padding-left: 5px; }

        /* First data row is tall (row 12 in XLS = 91.5pt) */
        .ics-table tbody tr.data-main { height: 92pt; vertical-align: top; }
        .ics-table tbody tr.data-main td { vertical-align: top; padding-top: 4px; }

        /* ── Signature block (rows 14-21) ──
           Two halves: left = "Received from" (cols 0-4), right = "Received by" (cols 5-7)
           Outer border: thick (2px). Inner divider between halves: thick (2px).
        ── */
        .sig-table { table-layout: fixed; }
        .sig-table colgroup col:nth-child(1) { width: 55%; } /* cols 0-4 */
        .sig-table colgroup col:nth-child(2) { width: 45%; } /* cols 5-7 */

        .sig-table td {
            font-size: 12pt;
            font-family: 'Times New Roman', Times, serif;
            padding: 2px 8px;
            vertical-align: top;
        }

        /* Row 14: "Received from:" | "Received by:" — thick top */
        .sig-table tr.sig-lbl td {
            border-top:  2px solid #000;
            border-bottom: 0;
            font-size: 12pt;
        }
        .sig-table tr.sig-lbl td:first-child { border-left: 2px solid #000; border-right: 2px solid #000; }
        .sig-table tr.sig-lbl td:last-child  { border-left: 0;              border-right: 2px solid #000; }

        /* Rows 15-20: name/designation lines */
        .sig-table tr.sig-body td {
            border-top: 0; border-bottom: 0;
        }
        .sig-table tr.sig-body td:first-child { border-left: 2px solid #000; border-right: 2px solid #000; }
        .sig-table tr.sig-body td:last-child  { border-left: 0;              border-right: 2px solid #000; }

        /* Row 21: Date — thick bottom */
        .sig-table tr.sig-date td {
            border-top: 0;
            border-bottom: 2px solid #000;
        }
        .sig-table tr.sig-date td:first-child { border-left: 2px solid #000; border-right: 2px solid #000; }
        .sig-table tr.sig-date td:last-child  { border-left: 0;              border-right: 2px solid #000; }

        .sig-name  { font-weight: 700; text-align: center; font-size: 12pt; text-transform: uppercase; display: block; }
        .sig-sub   { font-size: 11pt; text-align: center; display: block; }
        .sig-line  { border-bottom: 1px solid #000; display: block; min-height: 28px; }
    </style>
</head>
<body>

<div class="print-toolbar">
    <a href="{{ route('client.report.ics') }}" class="print-btn btn-secondary">&#8592; Back</a>
    <button class="print-btn btn-primary" onclick="window.print()">&#128438; Print / Save PDF</button>
</div>

<div class="print-paper">

    {{-- ══ LETTERHEAD ══ --}}
    <div class="letterhead">
        <div class="lh-logos">
            <img src="{{ asset('assets/img/Bagong_Pilipinas_logo.png') }}" alt="Bagong Pilipinas">
            <img src="{{ asset('assets/img/ati-header.png') }}"           alt="ATI">
        </div>
        <div class="lh-body">
            <!-- <div class="lh-republic">Republic of the Philippines</div>
            <div class="lh-dept">Department of Agriculture</div>
            <div class="lh-agency">Agricultural Training Institute<br>Regional Training Center 1</div>
            <div class="lh-addr">
                Regional Training Center: Tebag East, Sta. Barbara, Pangasinan &nbsp;Tel. No.: (075) 523-2266<br>
                Satellite Training Center: Brgy. Tabug, Batac City, Ilocos Norte &nbsp;Tel. No.: (077) 600-1096<br>
                Email: rtc1.dooc@ati.da.gov.ph &nbsp;|&nbsp; Website: www.ati2.da.gov.ph/ati-1
            </div> -->
        </div>
        <div class="lh-appendix">Appendix 59</div>
    </div>

    {{-- ══ TITLE (row 3, 14pt TNR bold, centered, merged cols 0-7) ══ --}}
    <div class="form-title">INVENTORY CUSTODIAN SLIP</div>

    {{-- ══ HEADER FIELDS (rows 6-7, no borders, 11pt bold TNR) ══ --}}
    <div class="hdr-block">
        <div class="hdr-row">
            <div>
                <span>Entity Name: </span>
                <span class="hdr-val">ATI-RTC I</span>
            </div>
        </div>
        <div class="hdr-row">
            <div>
                <span>Fund Cluster : </span>
                <span class="hdr-val" style="min-width:220px;"></span>
            </div>
            <div>
                <span>ICS No : </span>
                <span class="hdr-val" style="min-width:140px;">{{ $equipment->document_number }}</span>
            </div>
        </div>
    </div>

    {{-- ══ MAIN TABLE (rows 9-13) ══ --}}
    <table class="ics-table">
        <colgroup>
            <col><col><col><col><col><col><col><col>
        </colgroup>
        <thead>
            {{-- Row 9: Group labels spanning sub-rows --}}
            <tr class="hdr-r9">
                <th class="c-qty"  rowspan="3">Quantity</th>
                <th class="c-unit" rowspan="3">Unit</th>
                <th class="c-amt"  colspan="2">Amount</th>
                <th class="c-desc" colspan="2" rowspan="3">Description</th>
                <th class="c-inv"  rowspan="3">Inventory<br>Item No.</th>
                <th class="c-life" rowspan="3">Estimated<br>Useful Life</th>
            </tr>
            {{-- Row 10: Amount sub-headers --}}
            <tr class="hdr-r10">
                <th class="c-uc">Unit Cost</th>
                <th class="c-tc">Total Cost</th>
            </tr>
            {{-- Row 11: (bottom spacer row for header — no new content) --}}
            <tr class="hdr-r11">
                <th class="c-uc"></th>
                <th class="c-tc"></th>
            </tr>
        </thead>
        <tbody>
            {{-- Row 12: main data row (tall — 91.5pt in XLS) --}}
            <tr class="data-main">
                <td>{{ $equipment->quantity ?? 1 }}</td>
                <td>{{ $equipment->unit_of_measurement }}</td>
                <td>{{ number_format($equipment->unit_value, 2) }}</td>
                <td>{{ number_format($equipment->unit_value * ($equipment->quantity ?? 1), 2) }}</td>
                <td class="desc-cell" colspan="2">
                    <strong>{{ $equipment->article }}</strong>
                    @if($equipment->description)
                        <br>{{ $equipment->description }}
                    @endif
                </td>
                <td>{{ $equipment->property_number }}</td>
                <td></td>
            </tr>
            {{-- Row 13: additional blank item rows (xxx placeholders in XLS) --}}
            @for ($i = 0; $i < 6; $i++)
            <!-- <tr>
                <td></td><td></td><td></td><td></td>
                <td class="desc-cell" colspan="2"></td>
                <td></td><td></td>
            </tr> -->
            @endfor
        </tbody>
    </table>

    {{-- ══ SIGNATURE BLOCK (rows 14-21) ══
         Left half  (cols 0-4) = "Received from:" → Franklin A. Salcedo (issuer)
         Right half (cols 5-7) = "Received by:"   → responsible person
    ══ --}}
    <table class="sig-table">
        <colgroup><col><col></colgroup>

        {{-- Row 14: section labels --}}
        <tr class="sig-lbl">
            <td>Received from:</td>
            <td>Received by:</td>
        </tr>

        {{-- Rows 15-16: signature line --}}
        <tr class="sig-body">
            <td style="height:32px;"><span class="sig-line"></span></td>
            <td style="height:32px;"><span class="sig-line"></span></td>
        </tr>

        {{-- Row 16-17: name --}}
        <tr class="sig-body">
            <td><span class="sig-name">Franklin A. Salcedo</span></td>
            <td><span class="sig-name">{{ strtoupper($equipment->responsible_person ?? '') }}</span></td>
        </tr>

        {{-- Row 17: "Signature Over Printed Name" --}}
        <tr class="sig-body">
            <td><span class="sig-sub">Signature Over Printed Name</span></td>
            <td><span class="sig-sub">Signature Over Printed Name</span></td>
        </tr>

        {{-- Row 18: designation --}}
        <tr class="sig-body">
            <td><span class="sig-sub">Supply and Property Officer</span></td>
            <td><span class="sig-sub">{{ $equipment->responsibility_center ?? '' }}</span></td>
        </tr>

        {{-- Row 19: Position/Office --}}
        <tr class="sig-body">
            <td><span class="sig-sub">Position/Office</span></td>
            <td><span class="sig-sub">Position/Office</span></td>
        </tr>

        {{-- Rows 20-21: blank + Date (thick bottom) --}}
        <tr class="sig-body">
            <td style="height:14px;"></td>
            <td style="height:14px;"></td>
        </tr>
        <tr class="sig-date">
            <td>Date</td>
            <td>Date</td>
        </tr>
    </table>

</div>{{-- /print-paper --}}

<script>
    window.addEventListener('load', () => setTimeout(() => window.print(), 600));
</script>
</body>
</html>