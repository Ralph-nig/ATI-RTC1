{{-- filepath: resources/views/client/report/par/print.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAR {{ $equipment->document_number }}</title>
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
                width: 216mm; /* Letter */
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
        .lh-logos { display: flex; align-items: center; gap: 5px; flex-shrink: 0; }
        .lh-logos img { height: 62px; width: auto; object-fit: contain; }
        .lh-body { flex: 1; }
        .lh-republic { font-size: 9pt; }
        .lh-dept     { font-size: 9pt; color: #12410d; }
        .lh-agency   { font-size: 13pt; color: #12410d; font-weight: 700; text-transform: uppercase; line-height: 1.2; }
        .lh-addr     { font-size: 6pt; font-weight: 700; color: #222; margin-top: 2px; line-height: 1.4; }
        /* Appendix 71 — 14pt TNR, right */
        .lh-appendix { font-size: 14pt; font-style: italic; white-space: nowrap; align-self: flex-start; }

        /* ══ TITLE (row 4, merged cols 0-5, 14pt bold TNR, centered) ══ */
        .form-title {
            text-align: center;
            font-size: 14pt;
            font-weight: 700;
            text-transform: uppercase;
            margin: 6px 0 4px;
        }

        /* ══ HEADER FIELDS (rows 7-8, no borders, 12pt bold TNR) ══ */
        .hdr-block {
            font-size: 12pt;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .hdr-row {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
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
           XLS col widths (256-units):
           C0=2596 C1=2596 C2=7533 C3=3254 C4=3547 C5=4461 C6=2267
           Total = 26254 (C6 is outside-border col, skip for % — use C0-C5 only)
           Using C0-C5 for the 6 data columns:
           Total C0-C5 = 23987
           C0=10.82% C1=10.82% C2=31.40% C3=13.57% C4=14.79% C5=18.60%

           Header (rows 10-11 merged):
             Quantity | Unit | Description | Property Number | Date Acquired | Amount
           Data rows 12-14 (tall data row at row 12 = 64pt)
           Row 15 = bottom border of table (thick bottom)
        ══ */
        table { border-collapse: collapse; width: 100%; }

        .par-table { table-layout: fixed; }
        .par-table colgroup col:nth-child(1) { width: 10.82%; } /* Qty */
        .par-table colgroup col:nth-child(2) { width: 10.82%; } /* Unit */
        .par-table colgroup col:nth-child(3) { width: 31.40%; } /* Description */
        .par-table colgroup col:nth-child(4) { width: 13.57%; } /* Property No. */
        .par-table colgroup col:nth-child(5) { width: 14.79%; } /* Date Acquired */
        .par-table colgroup col:nth-child(6) { width: 18.60%; } /* Amount */

        .par-table th, .par-table td {
            font-size: 12pt;
            font-family: 'Times New Roman', Times, serif;
            padding: 2px 4px;
            vertical-align: middle;
            text-align: center;
        }

        /* ── Header rows 10-11 (merged) ──
           Row 10: thick top + thick sides, no bottom
           Row 11: thick bottom, thick sides, no top
        ── */
        .par-table tr.hdr-r10 th {
            font-weight: 700;
            border-top:    2px solid #000;
            border-bottom: 0;
        }
        .par-table tr.hdr-r10 th:first-child { border-left: 2px solid #000; }
        .par-table tr.hdr-r10 th:last-child  { border-right: 2px solid #000; }
        .par-table tr.hdr-r10 th             { border-left: 2px solid #000; border-right: 2px solid #000; }

        .par-table tr.hdr-r11 th {
            font-weight: 700;
            border-top: 0;
            border-bottom: 2px solid #000;
        }
        .par-table tr.hdr-r11 th { border-left: 2px solid #000; border-right: 2px solid #000; }

        /* ── Data rows ── */
        .par-table tbody tr         { height: 18px; }
        .par-table tbody td         { border: 1px solid #000; font-size: 11pt; }
        .par-table tbody td         { border-left: 2px solid #000; border-right: 2px solid #000; }
        .par-table tbody td.desc-cell { text-align: left; padding-left: 5px; }

        /* First data row is tall (row 12 in XLS = 64pt) */
        .par-table tbody tr.data-main { height: 64pt; vertical-align: top; }
        .par-table tbody tr.data-main td { vertical-align: top; padding-top: 4px; }

        /* Row 14 = thick bottom (end of data area) */
        .par-table tbody tr.data-last td { border-bottom: 2px solid #000; }

        /* ── Signature block (rows 16-22) ──
           Two halves: left cols 0-2 = "Received by", right cols 3-5 = "Issued by"
           XLS: Franklin A. Salcedo is on the ISSUED BY (right) side
        ── */
        .sig-table { table-layout: fixed; }
        .sig-table colgroup col:nth-child(1) { width: 50%; } /* cols 0-2 */
        .sig-table colgroup col:nth-child(2) { width: 50%; } /* cols 3-5 */

        .sig-table td {
            font-size: 12pt;
            font-family: 'Times New Roman', Times, serif;
            padding: 2px 8px;
            vertical-align: top;
        }

        /* Row 16: "Received by" | "Issued by" — thick top */
        .sig-table tr.sig-lbl td {
            border-top: 2px solid #000;
            border-bottom: 0;
            font-weight: 700;
            font-size: 11pt;
        }
        .sig-table tr.sig-lbl td:first-child { border-left: 2px solid #000; border-right: 2px solid #000; }
        .sig-table tr.sig-lbl td:last-child  { border-left: 0;              border-right: 2px solid #000; }

        /* Rows 17-21: body content */
        .sig-table tr.sig-body td { border-top: 0; border-bottom: 0; }
        .sig-table tr.sig-body td:first-child { border-left: 2px solid #000; border-right: 2px solid #000; }
        .sig-table tr.sig-body td:last-child  { border-left: 0;              border-right: 2px solid #000; }

        /* Row 22: Date — thick bottom */
        .sig-table tr.sig-date td {
            border-top: 0;
            border-bottom: 2px solid #000;
        }
        .sig-table tr.sig-date td:first-child { border-left: 2px solid #000; border-right: 2px solid #000; }
        .sig-table tr.sig-date td:last-child  { border-left: 0;              border-right: 2px solid #000; }

        .sig-name  { font-weight: 700; text-align: center; font-size: 11pt; text-transform: uppercase; display: block; }
        .sig-sub   { font-size: 11pt; text-align: center; display: block; }
        .sig-line  { border-bottom: 1px solid #000; display: block; min-height: 28px; }
    </style>
</head>
<body>

<div class="print-toolbar">
    <a href="{{ route('client.report.par') }}" class="print-btn btn-secondary">&#8592; Back</a>
    <button class="print-btn btn-primary" onclick="window.print()">&#128438; Print / Save PDF</button>
</div>

<div class="print-paper">

    {{-- ══ LETTERHEAD ══ --}}
    <div class="letterhead">
        <div class="lh-logos">
            <img src="{{ asset('assets/img/Bagong_Pilipinas_logo.png') }}" alt="Bagong Pilipinas">
            <img src="{{ asset('assets/img/ati-header.png') }}" alt="ATI">
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
        <div class="lh-appendix">Appendix 71</div>
    </div>

    {{-- ══ TITLE (row 4, merged cols 0-5, 14pt bold TNR, centered) ══ --}}
    <div class="form-title">PROPERTY ACKNOWLEDGMENT RECEIPT</div>

    {{-- ══ HEADER FIELDS (rows 7-8, no borders, 12pt bold TNR) ══ --}}
    <div class="hdr-block">
        <div class="hdr-row">
            <div>
                <span>Entity Name : </span>
                <span class="hdr-val">ATI-RTC I</span>
            </div>
        </div>
        <div class="hdr-row">
            <div>
                <span>Fund Cluster: </span>
                <span class="hdr-val" style="min-width:220px;"></span>
            </div>
            <div>
                <span>PAR No.: </span>
                <span class="hdr-val" style="min-width:140px;">{{ $equipment->document_number }}</span>
            </div>
        </div>
    </div>

    {{-- ══ MAIN TABLE (rows 10-15) ══ --}}
    <table class="par-table">
        <colgroup>
            <col><col><col><col><col><col>
        </colgroup>
        <thead>
            {{-- Row 10: column headers top half --}}
            <tr class="hdr-r10">
                <th>Quantity</th>
                <th>Unit</th>
                <th>Description</th>
                <th>Property<br>Number</th>
                <th>Date<br>Acquired</th>
                <th>Amount</th>
            </tr>
            {{-- Row 11: bottom of header (empty continuation row with thick bottom) --}}
            <tr class="hdr-r11">
                <th></th><th></th><th></th><th></th><th></th><th></th>
            </tr>
        </thead>
        <tbody>
            {{-- Row 12: main data row (tall — 64pt) --}}
            <tr class="data-main">
                <td>{{ $equipment->quantity ?? 1 }}</td>
                <td>{{ $equipment->unit_of_measurement }}</td>
                <td class="desc-cell">
                    <strong>{{ $equipment->article }}</strong>
                    @if($equipment->description)
                        <br>{{ $equipment->description }}
                    @endif
                    @if($equipment->classification)
                        <br><em>{{ $equipment->classification }}</em>
                    @endif
                </td>
                <td>{{ $equipment->property_number }}</td>
                <td>{{ $equipment->acquisition_date ? \Carbon\Carbon::parse($equipment->acquisition_date)->format('d-M-Y') : '' }}</td>
                <td>{{ number_format($equipment->unit_value, 2) }}</td>
            </tr>

            {{-- Rows 13-14: additional blank rows --}}
            <tr>
                <td></td><td></td><td class="desc-cell"></td><td></td><td></td><td></td>
            </tr>

        </tbody>
    </table>

    {{-- ══ SIGNATURE BLOCK (rows 16-22) ══
         Left  (cols 0-2) = Received by  → responsible person
         Right (cols 3-5) = Issued by    → Franklin A. Salcedo
    ══ --}}
    <table class="sig-table">
        <colgroup><col><col></colgroup>

        {{-- Row 16: section labels --}}
        <tr class="sig-lbl">
            <td>Received by:</td>
            <td>Issued by:</td>
        </tr>

        {{-- Rows 17-18: signature space --}}
        <tr class="sig-body">
            <td style="height:32px;"><span class="sig-line"></span></td>
            <td style="height:32px;"><span class="sig-line"></span></td>
        </tr>

        {{-- Row 18: printed name --}}
        <tr class="sig-body">
            <td><span class="sig-name">{{ strtoupper($equipment->responsible_person ?? '') }}</span></td>
            <td><span class="sig-name">Franklin A. Salcedo</span></td>
        </tr>

        {{-- Row 19: designation --}}
        <tr class="sig-body">
            <td><span class="sig-sub">{{ $equipment->responsibility_center ?? '' }}</span></td>
            <td><span class="sig-sub">Supply and Property Officer</span></td>
        </tr>

        {{-- Row 20: Position/Office --}}
        <tr class="sig-body">
            <td><span class="sig-sub">Position/Office</span></td>
            <td><span class="sig-sub">Position/Office</span></td>
        </tr>

        {{-- Row 21: blank spacer --}}
        <tr class="sig-body">
            <td style="height:14px;"></td>
            <td style="height:14px;"></td>
        </tr>

        {{-- Row 22: Date — thick bottom --}}
        <tr class="sig-date">
            <td>Date</td>
            <td>Date</td>
        </tr>
    </table>

</div>{{-- /print-paper --}}

<script>
    window.addEventListener('load', () => setTimeout(() => window.print(), 700));
</script>
</body>
</html>