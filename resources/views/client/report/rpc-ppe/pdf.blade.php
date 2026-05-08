<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>RPC-PPE Report</title>
    <style>
        /*
         * DomPDF rendering notes:
         * - col/colgroup widths are IGNORED — use inline style="width:X%" on <th> only
         * - nth-child NOT supported — use explicit classes or inline styles
         * - table-layout:fixed + th widths is the ONLY reliable column sizing method
         * - Images must use file:// absolute path
         * - % widths on th work when table is width:100%
         */

        @page {
            size: 297mm 210mm;
            margin: 10mm 40mm 15mm 40mm;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, sans-serif;
            font-size: 8pt;
            line-height: 1.3;
            color: #000;
            background: white;
            margin: 10mm 15mm 15mm 15mm;
        }

        /* ── ATI Header ─────────────────────────────────────────────── */
        .ati-header { width: 100%; margin-bottom: 3pt; }
        .ati-header table { width: 100%; border-collapse: collapse; }
        .ati-header td { padding: 0; border: none; vertical-align: top; }
        .ati-header .ati-left  { width: 28%; }
        .ati-header .ati-img   { width: 44%; }
        .ati-header .ati-right { width: 28%; }
        .ati-header img { display: block; width: 100%; height: auto; }
        .ati-divider { border-top: 2pt solid #2d6a2d; margin-bottom: 4pt; }

        /* ── Report title ───────────────────────────────────────────── */
        .report-title {
            text-align: center; font-size: 10pt;
            font-weight: bold; text-transform: uppercase;
            margin-bottom: 1pt; line-height: 1.2;
        }
        .report-asof { text-align: center; font-size: 8pt; margin-bottom: 3pt; }

        /* ── Entity name box ────────────────────────────────────────── */
        .entity-name-row {
            border: 1.5pt solid #000;
            text-align: center; font-weight: bold; font-size: 8pt;
            padding: 4pt; margin-bottom: 3pt; min-height: 18pt;
        }

        /* ── Main table ─────────────────────────────────────────────── */
        /*
         * Columns tuned to match Image 2 proportions (sum = 100%):
         *  A(Article)=9%    B(Desc)=20%    C(PropNo)=7.5%  D(UOM)=5.5%
         *  E(UnitVal)=6.5%  F(AcqDate)=6%  G(QtyCard)=6.5% H(QtyCount)=6.5%
         *  I(ShtgQty)=3.5%  J(ShtgVal)=3.5% K(Person)=9%  L(Center)=9%
         *  M(Condition)=8%
         */
        table.main {
            width: 100%; border-collapse: collapse;
            table-layout: fixed; font-size: 6pt; margin-bottom: 5pt;
        }
        table.main th, table.main td {
            border: 0.75pt solid #000; padding: 2pt 2pt;
            vertical-align: middle; word-wrap: break-word;
        }
        table.main thead th {
            font-weight: bold; text-align: center;
            font-size: 6pt; background-color: #ffffff;
        }
        table.main tbody tr { height: 16pt; }

        /* Classification banner */
        tr.banner td {
            background-color: #ffffff; font-weight: bold;
            font-size: 6.5pt; text-align: center; padding: 2pt;
            border: 0.75pt solid #000;
        }

        td.art { font-weight: bold; text-align: center; vertical-align: middle; }
        td.c   { text-align: center; }
        td.r   { text-align: right; }
        td.l   { text-align: left; }

        /* ── Signature block ────────────────────────────────────────── */
        .sig-block { width: 100%; font-size: 7pt; margin-top: 8pt; }
        .sig-block > table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .sig-block > table > tr > td {
            vertical-align: top; padding: 0 4pt 0 0; border: none;
        }
        .sig-inner { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .sig-inner td { vertical-align: top; border: none; padding: 0; }
        .sn { font-weight: bold; font-size: 7pt; display: block; }
        .sr { font-size: 6.5pt; display: block; }
    </style>
</head>
<body>

{{-- ATI Header --}}
<div class="ati-header">
    <table>
        <tr>
            <td class="ati-left"></td>
            <td class="ati-img">
                @php
                    $imgPath = public_path('assets/img/ati_header.jpg');
                    $imgData = file_exists($imgPath)
                        ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($imgPath))
                        : '';
                @endphp
                @if($imgData)
                    <img src="{{ $imgData }}" alt="">
                @endif
            </td>
            <td class="ati-right"></td>
        </tr>
    </table>
</div>
<div class="ati-divider"></div>

<div class="report-title">REPORT ON THE PHYSICAL COUNT OF PROPERTY PLANT AND EQUIPMENT</div>
<div class="report-asof">as of {{ $header['as_of'] ?: '' }}</div>

@if(!empty($header['entity_name']))
<div class="entity-name-row">{{ strtoupper($header['entity_name']) }}</div>
@else
<div class="entity-name-row">&nbsp;</div>
@endif

@if($groupedEquipment->count() > 0)
<table class="main">
    <thead>
        <tr>
            <th rowspan="2" style="width:9%;">ARTICLE</th>
            <th rowspan="2" style="width:17%;">DESCRIPTION</th>
            <th rowspan="2" style="width:7%;">PROPERTY NUMBER</th>
            <th rowspan="2" style="width:5%;">UNIT OF MEASURE</th>
            <th rowspan="2" style="width:6%;">UNIT VALUE</th>
            <th rowspan="2" style="width:6%;">ACQUISITION DATE</th>
            <th rowspan="2" style="width:6%;">QUANTITY PER PROPERTY CARD</th>
            <th rowspan="2" style="width:6%;">QUANTITY PER PHYSICAL COUNT</th>
            <th colspan="2" style="width:7%;">SHORTAGE/OVERAGE</th>
            <th colspan="3" style="width:31%;">REMARKS</th>
        </tr>
        <tr>
            <th style="width:3.5%;">QUANTITY</th>
            <th style="width:3.5%;">VALUE</th>
            <th style="width:9%;">PERSON RESPONSIBLE</th>
            <th style="width:13%;">/RESPONSIBILITY CENTER</th>
            <th style="width:9%;">CONDITION OF PROPERTIES</th>
        </tr>
    </thead>
    <tbody>
        @foreach($groupedEquipment as $classification => $equipmentItems)
            <tr class="banner">
                <td colspan="13">{{ strtoupper($classification ?: 'UNCLASSIFIED EQUIPMENT') }}</td>
            </tr>
            @foreach($equipmentItems->groupBy('article') as $article => $items)
                @foreach($items as $index => $equipment)
                    @php
                        $hasQty = $equipment->property_number && $equipment->property_number !== '-';
                        $qty    = $hasQty ? 1 : '';
                    @endphp
                    <tr>
                        @if($index === 0)
                            <td class="art" rowspan="{{ $items->count() }}">{{ $article }}</td>
                        @endif
                        <td class="l">{{ $equipment->description ?: '' }}</td>
                        <td class="c">{{ $equipment->property_number ?: '-' }}</td>
                        <td class="c">{{ $equipment->unit_of_measurement ?: '' }}</td>
                        <td class="r">{{ $equipment->unit_value !== null ? number_format((float)$equipment->unit_value, 2) : '' }}</td>
                        <td class="r">{{ $equipment->acquisition_date ? $equipment->acquisition_date->format('M-d') : '' }}</td>
                        <td class="c">{{ $qty }}</td>
                        <td class="c">{{ $qty }}</td>
                        <td class="c"></td>
                        <td class="c"></td>
                        <td class="l">{{ $equipment->responsible_person ?: 'Unknown / Book of the Accountant' }}</td>
                        <td class="c">{{ $equipment->location ?: '' }}</td>
                        <td class="c">{{ $equipment->condition ?: '' }}</td>
                    </tr>
                @endforeach
            @endforeach
        @endforeach
    </tbody>
</table>
@endif

@php
    $cc1_name = $header['f_cc1_name'] ?? 'FRANKLIN A. SALCEDO';
    $cc1_role = $header['f_cc1_role'] ?? 'Inventory Committee - Chairman';
    $cc2_name = $header['f_cc2_name'] ?? 'ALYSSA MAE M. ESTRADA';
    $cc2_role = $header['f_cc2_role'] ?? 'Inventory Committee - Member';
    $cc3_name = $header['f_cc3_name'] ?? 'AMOR JOYCE M. MARCELO, CPA';
    $cc3_role = $header['f_cc3_role'] ?? 'Inventory Committee - Member';
    $cc4_name = $header['f_cc4_name'] ?? 'ANGELIQUE I. PENALBA, CPA';
    $cc4_role = $header['f_cc4_role'] ?? 'Inventory Committee - Member';
    $ab_name  = $header['f_ab_name']  ?? 'JOSEPHINE K. ABEN, Ph.D.';
    $ab_role  = $header['f_ab_role']  ?? 'Assistant Center Director / Authorized Representative';
    $vb_name  = $header['f_vb_name']  ?? 'JELANIE S. WANAWAN';
    $vb_role  = $header['f_vb_role']  ?? 'State Auditor II';
    $vb_role2 = $header['f_vb_role2'] ?? 'OIC - Audit Team Leader';
@endphp

<div class="sig-block">
    <table>
        {{-- Labels row --}}
        <tr>
            <td style="width:47%; font-size:7pt; padding-bottom:10pt;">Certified Correct by:</td>
            <td style="width:25%; font-size:7pt; padding-bottom:10pt;">Approved by:</td>
            <td style="width:28%; font-size:7pt; padding-bottom:10pt;">Verified by:</td>
        </tr>
        {{-- Names + roles row --}}
        <tr>
            {{-- Left: cc1/cc2 side-by-side, then cc3/cc4 below --}}
            <td style="vertical-align:top;">
                <table class="sig-inner">
                    <tr>
                        <td style="width:50%;">
                            <span class="sn">{{ $cc1_name }}</span>
                            <span class="sr">{{ $cc1_role }}</span>
                        </td>
                        <td style="width:50%;">
                            <span class="sn">{{ $cc2_name }}</span>
                            <span class="sr">{{ $cc2_role }}</span>
                        </td>
                    </tr>
                    @if($cc3_name)
                    <tr>
                        <td style="padding-top:8pt;">
                            <span class="sn">{{ $cc3_name }}</span>
                            <span class="sr">{{ $cc3_role }}</span>
                        </td>
                        @if($cc4_name)
                        <td style="padding-top:8pt;">
                            <span class="sn">{{ $cc4_name }}</span>
                            <span class="sr">{{ $cc4_role }}</span>
                        </td>
                        @endif
                    </tr>
                    @endif
                </table>
            </td>

            {{-- Middle: Approved by --}}
            <td style="vertical-align:top;">
                <span class="sn">{{ $ab_name }}</span>
                <span class="sr">{{ $ab_role }}</span>
            </td>

            {{-- Right: Verified by --}}
            <td style="vertical-align:top;">
                <span class="sn">{{ $vb_name }}</span>
                <span class="sr">{{ $vb_role }}</span>
                @if($vb_role2)
                <span class="sr">{{ $vb_role2 }}</span>
                @endif
            </td>
        </tr>
    </table>
</div>

</body>
</html>