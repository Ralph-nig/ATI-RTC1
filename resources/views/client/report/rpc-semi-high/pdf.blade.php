<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Semi-Expendable Property Card (High Value)</title>
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            src: url('file://{{ str_replace('\\', '/', public_path('fonts/DejaVuSans.ttf')) }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        @page { size: A4 landscape; margin: 0.5cm 0.7cm 0.5cm 0.7cm; }
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 7pt; line-height: 1.2;
            background-color: #ffffff; color: #000000;
        }

        /* ── Title ── */
        .report-title {
            text-align: center; font-size: 10pt; font-weight: bold;
            letter-spacing: 0.5pt; margin-bottom: 6pt;
        }

        /* ── Card ── */
        .property-card { margin-bottom: 8pt; page-break-inside: avoid; width: 100%; }

        /* Entity Name / Fund Cluster — real <table>, no borders */
        .meta-table { width: 100%; border-collapse: collapse; margin-bottom: 3pt; font-size: 7pt; }
        .meta-table td { border: none; padding: 0; vertical-align: bottom; }
        .meta-left  { text-align: left; }
        .meta-right { text-align: right; }
        .blank-long  { display: inline-block; width: 120pt; border-bottom: 0.5pt solid #000000; }
        .blank-short { display: inline-block; width: 70pt;  border-bottom: 0.5pt solid #000000; }

        /* ══════════════════════════════════════
           MAIN TABLE — 11 columns:
           [1:Date][2:Ref][3:Qty.R][4:UnitCost][5:TotalCost]
           [6:ItemNo][7:Qty.I][8:Officer][9:BalQty][10:Amount][11:Remarks]

           INFO ROW 1:
             colspan=8 → Semi-expendable Property:  (cols 1–8)
             colspan=3 → Semi-expendable Property Number:___ (cols 9–11)
                         underline stretches full cell width via inner table
        ══════════════════════════════════════ */
        .property-table { width: 100%; border-collapse: collapse; border: 1pt solid #000000; font-size: 7pt; }
        .property-table th,
        .property-table td { border: 0.5pt solid #000000; padding: 1pt 2pt; vertical-align: middle; background-color: #ffffff; color: #000000; }

        /* Info rows */
        .info-td { font-size: 7pt; font-weight: normal; height: 12pt; padding: 1pt 3pt; }
        .info-bold { font-weight: bold; }

        /* Property Number cell — inner table for DomPDF-compatible underline */
        .prop-num-td { font-size: 7pt; height: 12pt; padding: 1pt 3pt; }
        .prop-num-inner { width: 100%; border-collapse: collapse; }
        .prop-num-inner td { border: none; padding: 0; vertical-align: bottom; }
        .prop-num-label { white-space: nowrap; font-weight: bold; font-size: 7pt; }
        .prop-num-line  { width: 100%; border-bottom: 0.5pt solid #000000; font-size: 7pt; }

        /* Header rows */
        .header-th { text-align: center; font-weight: bold; font-size: 7pt; height: 11pt; white-space: nowrap; }

        /* Data rows */
        .data-td { height: 11pt; font-size: 7pt; }

        .text-center { text-align: center; }
        .text-right  { text-align: right; }

        /* ── Footer ── */
        .footer-section { margin-top: 10pt; font-size: 7pt; }
        .sig-line { display: inline-block; width: 180pt; border-top: 1pt solid #000000; margin-top: 24pt; margin-bottom: 2pt; }
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
                                <col style="width:9%">  <col style="width:10%"> <col style="width:7%">
                                <col style="width:9%">  <col style="width:8%">  <col style="width:7%">
                                <col style="width:7%">  <col style="width:10%"> <col style="width:8%">
                                <col style="width:10%"> <col style="width:15%">
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

(Good)