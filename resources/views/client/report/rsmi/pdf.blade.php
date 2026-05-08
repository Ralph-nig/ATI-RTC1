<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>RSMI - Report of Supplies and Materials Issued</title>
    <style>
        @page {
            margin: 12mm 14mm 12mm 14mm;
            size: A4 portrait;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: Arial, sans-serif;
            font-size: 8pt;
            line-height: 1.3;
            background: white;
            color: #000;
        }

        /* ── Appendix 64 top-right ── */
        .appendix {
            text-align: right;
            font-size: 8pt;
            font-style: italic;
            margin-bottom: 2pt;
        }

        /* ── Title ── */
        .title {
            text-align: center;
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3pt;
            margin-bottom: 8pt;
        }

        /* ─────────────────────────────────────────────
           HEADER BLOCK — NO border box, just labels + underlined values
           Two rows, two columns (left ~55%, right ~45%)
        ───────────────────────────────────────────── */
        .header-block {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin-bottom: 4pt;
        }
        .header-block td {
            padding: 1pt 0;
            vertical-align: bottom;
            border: none;
        }
        .header-block .hl { width: 55%; }
        .header-block .hr { width: 45%; text-align: right; }
        .hval {
            display: inline-block;
            border-bottom: 0.75pt solid #000;
            min-width: 130pt;
            padding-bottom: 0.5pt;
        }
        .hval-sm {
            display: inline-block;
            border-bottom: 0.75pt solid #000;
            min-width: 90pt;
            padding-bottom: 0.5pt;
        }

        /* ─────────────────────────────────────────────
           OUTER BORDER BOX
           Wraps: banner + data table + recap + signature
           Uses a wrapper table so DomPDF respects the border correctly.
        ───────────────────────────────────────────── */
        .outer-box {
            width: 100%;
            border-collapse: collapse;
            border: 0.75pt solid #000;
        }
        .outer-box > tbody > tr > td {
            padding: 0;
            border: none;
        }

        /* ── Banner row ── */
        .banner {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
            font-style: italic;
            border-bottom: 0.75pt solid #000;
        }
        .banner td { padding: 2.5pt 4pt; vertical-align: middle; }
        .banner .bl { width: 55%; border-right: 0.75pt solid #000; }
        .banner .br { width: 45%; text-align: right; }

        /* ── Main Data Table ── */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
            table-layout: fixed;
            border-bottom: 0.75pt solid #000;
        }
        .data-table col.cA { width: 16%; }
        .data-table col.cB { width: 14%; }
        .data-table col.cC { width:  8%; }
        .data-table col.cD { width: 26%; }
        .data-table col.cE { width:  7%; }
        .data-table col.cF { width:  9%; }
        .data-table col.cG { width: 11%; }
        .data-table col.cH { width:  9%; }

        .data-table th {
            border: 0.75pt solid #000;
            border-top: none;
            padding: 3pt 2pt;
            text-align: center;
            vertical-align: middle;
            background-color: #d9d9d9;
            font-weight: bold;
            font-size: 7.5pt;
            line-height: 1.3;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .data-table th:first-child { border-left: none; }
        .data-table th:last-child  { border-right: none; }

        .data-table td {
            border: 0.75pt solid #000;
            border-top: none;
            padding: 2.5pt 3pt;
            text-align: center;
            vertical-align: middle;
            font-size: 7.5pt;
            word-wrap: break-word;
            line-height: 1.3;
        }
        .data-table td:first-child { border-left: none; }
        .data-table td:last-child  { border-right: none; }
        .data-table td.tdl { text-align: left; }
        .data-table td.tdr { text-align: right; }
        .data-table tr.total-row td {
            font-weight: bold;
        }
        .data-table tr.total-row td.total-label {
            text-align: right;
            padding-right: 4pt;
        }
        .data-table tr.total-row td.total-val { text-align: right; }

        /* ── Recapitulation section ── */
        /* Uses a 2-column table inside the outer box.
           Left cell spans ~cols 1-5 area, right cell spans ~cols 6-8 area.
           Each cell has an inner recap table. */
        .recap-row {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 0.75pt solid #000;
        }
        .recap-row td { padding: 0; vertical-align: top; border: none; }
        .recap-left  { width: 55%; border-right: 0.75pt solid #000; }
        .recap-right { width: 45%; }

        .recap-inner-wrap {
            padding: 4pt 6pt;
        }
        .recap-title {
            font-weight: bold;
            font-size: 8pt;
            text-align: center;
            display: block;
            margin-bottom: 3pt;
        }
        .recap-tbl {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
            table-layout: fixed;
        }
        .recap-tbl th {
            border: 0.75pt solid #000;
            padding: 2.5pt 2pt;
            background-color: #d9d9d9;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
            font-size: 7.5pt;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .recap-tbl td {
            border: 0.75pt solid #000;
            padding: 2pt 3pt;
            text-align: center;
            vertical-align: middle;
            font-size: 7.5pt;
            min-height: 12pt;
            height: 12pt;
        }
        .recap-tbl td.tdr { text-align: right; }

        /* ── Signature section ── */
        .sig-cert-row {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 0.75pt solid #000;
        }
        .sig-cert-row td {
            padding: 4pt 6pt;
            font-size: 8pt;
            vertical-align: middle;
            border: none;
        }
        .sig-cert-row .sc-left {
            width: 55%;
            border-right: 0.75pt solid #000;
        }
        .sig-cert-row .sc-right {
            width: 45%;
        }

        /* Three-column name/label boxes */
        .sig-names-row {
            width: 100%;
            border-collapse: collapse;
        }
        .sig-names-row td { padding: 0; border: none; }

        .sig-name-box {
            width: 100%;
            border-collapse: collapse;
        }
        .sig-name-box td.name-cell {
            border-right: 0.75pt solid #000;
            border-bottom: none;
            padding: 8pt 4pt 0pt 4pt;
            text-align: center;
            font-weight: bold;
            font-size: 8pt;
            vertical-align: bottom;
            min-height: 20pt;
        }
        .sig-name-box td.name-cell:last-child { border-right: none; }
        .sig-name-box td.label-cell {
            border-right: 0.75pt solid #000;
            border-top: 0.75pt solid #000;
            padding: 2pt 4pt 5pt 4pt;
            text-align: center;
            font-size: 7pt;
            font-style: italic;
            vertical-align: top;
        }
        .sig-name-box td.label-cell:last-child { border-right: none; }
    </style>
</head>
<body>

{{-- Appendix 64 --}}
<div class="appendix">Appendix 64</div>

{{-- Title --}}
<div class="title">Report of Supplies and Materials Issued</div>

{{-- ── Header block: NO border box, just label + underline value ── --}}
<table class="header-block">
    <tr>
        <td class="hl">
            <strong>Entity Name:</strong>&nbsp;&nbsp;&nbsp;<span class="hval">{{ isset($header['entity_name']) && trim($header['entity_name']) !== '' ? $header['entity_name'] : '&nbsp;' }}</span>
        </td>
        <td class="hr">
            <strong>Serial No. :</strong>&nbsp;<span class="hval-sm">{{ isset($header['serial_no']) && trim($header['serial_no']) !== '' ? $header['serial_no'] : '&nbsp;' }}</span>
        </td>
    </tr>
    <tr>
        <td class="hl">
            <strong>Fund Cluster:</strong>&nbsp;<span class="hval">{{ isset($header['fund_cluster']) && trim($header['fund_cluster']) !== '' ? $header['fund_cluster'] : '&nbsp;' }}</span>
        </td>
        <td class="hr">
            <strong>Date :</strong>&nbsp;<span class="hval-sm">{{ isset($header['date']) && trim($header['date']) !== '' ? $header['date'] : '&nbsp;' }}</span>
        </td>
    </tr>
</table>

{{-- ════════════════════════════════════════════
     OUTER BORDER BOX — contains everything below
     ════════════════════════════════════════════ --}}
<table class="outer-box">
<tbody>

{{-- ── Banner row ── --}}
<tr><td>
    <table class="banner">
        <tr>
            <td class="bl"><em>To be filled up by the Supply and/or Property Division/Unit</em></td>
            <td class="br"><em>To be filled up by the Accounting Division/Unit</em></td>
        </tr>
    </table>
</td></tr>

{{-- ── Main Data Table ── --}}
<tr><td>
    <table class="data-table">
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
            @forelse($grouped as $group)
                @php
                    $rows     = $group['rows'];
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
            @empty
                <tr>
                    <td colspan="8" style="text-align:center; padding:10pt; border:none; border-left:none; border-right:none;">No records found.</td>
                </tr>
            @endforelse

            @if($grouped->count() > 0)
            <tr class="total-row">
                <td colspan="5" class="total-label">Total</td>
                <td class="total-val">{{ number_format($grandTotalQty) }}</td>
                <td class="total-val"></td>
                <td class="total-val">{{ $grandTotalAmt > 0 ? number_format($grandTotalAmt, 2) : '' }}</td>
            </tr>
            @endif
        </tbody>
    </table>
</td></tr>

{{-- ── Recapitulation row ── --}}
<tr><td>
    <table class="recap-row">
        <tr>
            {{-- Left: Stock No. + Quantity --}}
            <td class="recap-left">
                <div class="recap-inner-wrap">
                    <span class="recap-title">Recapitulation:</span>
                    <table class="recap-tbl">
                        <thead>
                            <tr>
                                <th style="width:50%;">Stock No.</th>
                                <th style="width:50%;">Quantity</th>
                            </tr>
                        </thead>
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
            {{-- Right: Unit Cost + Total Cost + UACS --}}
            <td class="recap-right">
                <div class="recap-inner-wrap">
                    <span class="recap-title">Recapitulation:</span>
                    <table class="recap-tbl">
                        <thead>
                            <tr>
                                <th style="width:28%;">Unit Cost</th>
                                <th style="width:36%;">Total Cost</th>
                                <th style="width:36%;">UACS Object Code</th>
                            </tr>
                        </thead>
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

{{-- ── "I hereby certify" / "Posted by:" row ── --}}
<tr><td>
    <table class="sig-cert-row">
        <tr>
            <td class="sc-left">I hereby certify to the correctness of the above information.</td>
            <td class="sc-right">Posted by:</td>
        </tr>
    </table>
</td></tr>

{{-- ── Signature name boxes ── --}}
<tr><td>
    <table class="sig-names-row">
        <tr>
            <td>
                <table class="sig-name-box">
                    <tr>
                        <td class="name-cell" style="width:38%;">
                            {!! isset($header['supply_name']) && trim($header['supply_name']) !== ''
                                ? e(strtoupper($header['supply_name']))
                                : '&nbsp;' !!}
                        </td>
                        <td class="name-cell" style="width:38%;">
                            {!! isset($header['acctg_name']) && trim($header['acctg_name']) !== ''
                                ? e(strtoupper($header['acctg_name']))
                                : '&nbsp;' !!}
                        </td>
                        <td class="name-cell" style="width:24%;">
                            {!! isset($header['acctg_date']) && trim($header['acctg_date']) !== ''
                                ? e($header['acctg_date'])
                                : '&nbsp;' !!}
                        </td>
                    </tr>
                    <tr>
                        <td class="label-cell" style="width:38%;">
                            Signature over Printed Name of Supply and/or<br>Property Custodian
                        </td>
                        <td class="label-cell" style="width:38%;">
                            Signature over Printed Name of<br>Designated Accounting Staff
                        </td>
                        <td class="label-cell" style="width:24%;">
                            Date
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</td></tr>

</tbody>
</table>

</body>
</html>