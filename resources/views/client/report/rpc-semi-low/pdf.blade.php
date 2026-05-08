<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Semi-Expendable Property Card (Low Value)</title>
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            src: url('file://{{ str_replace('\\', '/', public_path('fonts/DejaVuSans.ttf')) }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @page {
            size: A4 landscape;
            margin: 0.8cm 1.2cm 0.8cm 1.2cm;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 6.5pt;
            line-height: 1.15;
            background-color: #ffffff;
            color: #000000;
            width: 100%;
        }

        /* ── Title ── */
        .report-title {
            text-align: center;
            font-size: 8.5pt;
            font-weight: bold;
            letter-spacing: 0.4pt;
            margin-bottom: 3pt;
        }

        /* ── Card ── */
        .property-card {
            margin-bottom: 5pt;
            page-break-inside: avoid;
            width: 100%;
        }

        /* ── Entity Name / Fund Cluster ── */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5pt;
            font-size: 6.5pt;
        }
        .meta-table td { border: none; padding: 0 0 1pt 0; vertical-align: bottom; }
        .meta-left  { text-align: left; }
        .meta-right { text-align: right; }
        .blank-long  { display: inline-block; width: 100pt; border-bottom: 0.5pt solid #000000; }
        .blank-short { display: inline-block; width: 50pt;  border-bottom: 0.5pt solid #000000; }

        /* ── Main Table ── */
        .property-table {
            width: 100%;
            border-collapse: collapse;
            border: 0.75pt solid #000000;
            font-size: 6.5pt;
            table-layout: fixed;
        }
        .property-table th,
        .property-table td {
            border: 0.5pt solid #000000;
            padding: 1pt 2pt;
            vertical-align: middle;
            background-color: #ffffff;
            color: #000000;
            overflow: hidden;
        }

        /* Info rows */
        .info-td {
            font-size: 6.5pt;
            font-weight: normal;
            height: 10pt;
            padding: 1pt 2pt;
        }
        .info-bold { font-weight: bold; }

        /* Property Number cell */
        .prop-num-td {
            font-size: 6.5pt;
            height: 10pt;
            padding: 1pt 2pt;
        }
        .prop-num-inner { width: 100%; border-collapse: collapse; }
        .prop-num-inner td { border: none; padding: 0; vertical-align: bottom; }
        .prop-num-label { white-space: nowrap; font-weight: bold; font-size: 6.5pt; }
        .prop-num-line  { width: 100%; border-bottom: 0.5pt solid #000000; font-size: 6.5pt; }

        /* Header rows */
        .header-th {
            text-align: center;
            font-weight: bold;
            font-size: 6.5pt;
            height: 9pt;
            white-space: nowrap;
            background-color: #ffffff;
        }

        /* Data rows */
        .data-td {
            height: 9pt;
            font-size: 6.5pt;
        }

        .text-center { text-align: center; }
        .text-right  { text-align: right; }
    </style>
</head>
<body>

    <div class="report-title">SEMI-EXPENDABLE PROPERTY CARD</div>

    @if($groupedEquipment->count() > 0)
        @foreach($groupedEquipment as $classification => $equipmentItems)
            @php $groupedByArticle = $equipmentItems->groupBy('article'); @endphp
            @foreach($groupedByArticle as $article => $items)
                @foreach($items as $equipment)

                    <div class="property-card">

                        {{-- Entity Name / Fund Cluster --}}
                        <table class="meta-table">
                            <tr>
                                <td class="meta-left">
                                    <strong>Entity Name :</strong>&nbsp;
                                    @if(!empty(trim($header['entity_name'] ?? '')))
                                        {{ $header['entity_name'] }}
                                    @else
                                        <span class="blank-long">&nbsp;</span>
                                    @endif
                                </td>
                                <td class="meta-right">
                                    <strong>Fund Cluster :</strong>&nbsp;
                                    @if(!empty(trim($header['fund_cluster'] ?? '')))
                                        {{ $header['fund_cluster'] }}
                                    @else
                                        <span class="blank-short">&nbsp;</span>
                                    @endif
                                </td>
                            </tr>
                        </table>

                        <table class="property-table">
                            <colgroup>
                                <col style="width:7%">    {{-- Date --}}
                                <col style="width:9%">    {{-- Reference --}}
                                <col style="width:4.5%">  {{-- Receipt Qty --}}
                                <col style="width:8.5%">  {{-- Unit Cost --}}
                                <col style="width:8.5%">  {{-- Total Cost --}}
                                <col style="width:5%">    {{-- Item No --}}
                                <col style="width:4.5%">  {{-- Issue Qty --}}
                                <col style="width:14%">   {{-- Office/Officer --}}
                                <col style="width:5%">    {{-- Balance Qty --}}
                                <col style="width:8.5%">  {{-- Amount --}}
                                <col style="width:26%">   {{-- Remarks --}}
                            </colgroup>
                            <thead>
                                {{-- INFO ROW 1 --}}
                                <tr>
                                    <td class="info-td" colspan="8">
                                        <span class="info-bold">Semi-expendable Property:</span>&nbsp;{{ $article ?: '' }}
                                    </td>
                                    <td class="prop-num-td" colspan="3">
                                        @if(!empty($equipment->property_number))
                                            <span class="info-bold">Semi-expendable Property Number:</span>&nbsp;{{ $equipment->property_number }}
                                        @else
                                            <table class="prop-num-inner">
                                                <tr>
                                                    <td class="prop-num-label">Semi-expendable Property Number:</td>
                                                    <td class="prop-num-line">&nbsp;</td>
                                                </tr>
                                            </table>
                                        @endif
                                    </td>
                                </tr>
                                {{-- INFO ROW 2 --}}
                                <tr>
                                    <td class="info-td" colspan="11">
                                        <span class="info-bold">Description :</span>&nbsp;{{ $equipment->description ?: '' }}
                                    </td>
                                </tr>
                                {{-- HEADER ROW 1 --}}
                                <tr>
                                    <th class="header-th" rowspan="2">Date</th>
                                    <th class="header-th" rowspan="2">Reference</th>
                                    <th class="header-th" colspan="3">Receipt</th>
                                    <th class="header-th" colspan="3">Issue/Transfer/ Disposal</th>
                                    <th class="header-th">Balance</th>
                                    <th class="header-th" rowspan="2">Amount</th>
                                    <th class="header-th" rowspan="2">Remarks</th>
                                </tr>
                                {{-- HEADER ROW 2 --}}
                                <tr>
                                    <th class="header-th">Qty.</th>
                                    <th class="header-th">Unit Cost</th>
                                    <th class="header-th">Total Cost</th>
                                    <th class="header-th">Item No.</th>
                                    <th class="header-th">Qty.</th>
                                    <th class="header-th">Office/Officer</th>
                                    <th class="header-th">Qty.</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Data row --}}
                                <tr>
                                    <td class="data-td text-center">{{ $equipment->acquisition_date ? $equipment->acquisition_date->format('M d, Y') : '' }}</td>
                                    <td class="data-td"></td>
                                    <td class="data-td text-center">1</td>
                                    <td class="data-td text-right">{{ number_format($equipment->unit_value, 2) }}</td>
                                    <td class="data-td text-right">{{ number_format($equipment->unit_value, 2) }}</td>
                                    <td class="data-td"></td>
                                    <td class="data-td"></td>
                                    <td class="data-td"></td>
                                    <td class="data-td text-center">1</td>
                                    <td class="data-td text-right">{{ number_format($equipment->unit_value, 2) }}</td>
                                    <td class="data-td text-center">{{ $equipment->condition ?: 'Serviceable' }}</td>
                                </tr>
                                {{-- 16 blank rows --}}
                                @for($i = 0; $i < 16; $i++)
                                <tr>
                                    <td class="data-td"></td><td class="data-td"></td>
                                    <td class="data-td"></td><td class="data-td"></td>
                                    <td class="data-td"></td><td class="data-td"></td>
                                    <td class="data-td"></td><td class="data-td"></td>
                                    <td class="data-td"></td><td class="data-td"></td>
                                    <td class="data-td"></td>
                                </tr>
                                @endfor
                            </tbody>
                        </table>

                    </div>{{-- /.property-card --}}

                @endforeach
            @endforeach
        @endforeach
    @endif

</body>
</html>