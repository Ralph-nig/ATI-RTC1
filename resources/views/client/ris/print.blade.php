{{-- filepath: resources/views/client/ris/print.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>RIS {{ $ris->reference }}</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: 'Times New Roman', Times, serif;
    font-size: 12pt;
    color: #000;
    background: #fff;
}

/* ══ SCREEN WRAPPER ══ */
@media screen {
    body { background: #9e9e9e; padding: 20px 20px 60px; }
    .page { background: #fff; width: 794px; margin: 0 auto; padding: 10mm; box-shadow: 0 6px 32px rgba(0,0,0,.4); }
    .toolbar { width: 794px; margin: 0 auto 10px; display: flex; align-items: center; gap: 8px; }
    .btn { display: inline-flex; align-items: center; gap: 5px; padding: 7px 16px; border-radius: 5px;
           font-size: 13px; font-weight: 600; cursor: pointer; border: none;
           font-family: Arial, sans-serif; text-decoration: none; }
    .btn-green { background: #296218; color: #fff; }
    .btn-gray  { background: #616161; color: #fff; }
}

/* ══ PRINT ══ */
@media print {
    body { background: #fff; }
    .page { width: 100%; padding: 0; box-shadow: none; }
    .toolbar { display: none !important; }
    @page { size: A4 portrait; margin: 10mm; }
}

/* ══ SHARED TABLE RESET ══ */
table { border-collapse: collapse; width: 100%; table-layout: fixed; }
td, th { padding: 0; font-family: 'Times New Roman', Times, serif; font-size: 12pt; vertical-align: middle; }

/* ══ BORDER HELPERS ══ */
/* thin=1px solid, medium=2px solid */
.bt1 { border-top:    1px solid #000; }
.bt2 { border-top:    2px solid #000; }
.bb1 { border-bottom: 1px solid #000; }
.bb2 { border-bottom: 2px solid #000; }
.bl1 { border-left:   1px solid #000; }
.bl2 { border-left:   2px solid #000; }
.br1 { border-right:  1px solid #000; }
.br2 { border-right:  2px solid #000; }

/* ══ LETTERHEAD ══ */
.lh-wrap { display: flex; align-items: center; gap: 8px; margin-bottom: 2px; }
.lh-logos { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
.lh-logos img { height: 80px; width: auto; object-fit: contain; vertical-align: middle; }
.lh-logos img:first-child { width: 80px; object-fit: contain; object-position: center; }
.lh-appendix { font-size: 16pt; font-style: italic; margin-left: auto; white-space: nowrap; align-self: flex-start; padding-top: 2px; }

/* ══ COLUMN WIDTHS (proportional from xlrd: 3035,2011,7131,3510,3620,4096,4425,7716 = total 35524) ══ */
/* As % of total: 8.54, 5.66, 20.07, 9.88, 10.19, 11.53, 12.46, 21.72 */
col.c1 { width: 8.54%; }
col.c2 { width: 5.66%; }
col.c3 { width: 20.07%; }
col.c4 { width: 9.88%; }
col.c5 { width: 10.19%; }
col.c6 { width: 11.53%; }
col.c7 { width: 12.46%; }
col.c8 { width: 21.72%; }

/* ══ ROW HEIGHTS (xlrd twips / 20 = pt; 1pt ≈ 1.333px at 96dpi) ══
   Using pt values directly since CSS pt = print pt
   Row heights from XLS (pt): 9.8, 18, 24.8×3, 12.8, 26.2, 1.5, 22.5, 4.5, 22.5, 18.8,
   26.1, 26.1, 23.1, 20.25×9, 20.25, 10.9, 15.6, 24, 22.5, 22.7, 27.75, 22.7, 15
══ */
.rh-title   { height: 26.2pt; }  /* row 7 */
.rh-gap1    { height: 1.5pt;  }  /* row 8 */
.rh-entity  { height: 22.5pt; }  /* row 9 */
.rh-gap2    { height: 4.5pt;  }  /* row 10 */
.rh-div     { height: 22.5pt; }  /* row 11 */
.rh-office  { height: 18.75pt;}  /* row 12 */
.rh-grp     { height: 26.1pt; }  /* row 13 */
.rh-hdr     { height: 26.1pt; }  /* row 14 */
.rh-data1   { height: 23.1pt; }  /* row 15 (first data row slightly taller) */
.rh-data    { height: 20.25pt;}  /* rows 16-24 */
.rh-purp1   { height: 20.25pt;}  /* purpose row 25 (part of merged) */
.rh-purp2   { height: 10.9pt; }  /* row 26 */
.rh-purp3   { height: 15.6pt; }  /* row 27 */
.rh-sig0    { height: 24pt;   }  /* row 28 label */
.rh-sig1    { height: 22.5pt; }  /* row 29 signature */
.rh-sig2    { height: 22.7pt; }  /* row 30 printed name */
.rh-sig3    { height: 27.75pt;}  /* row 31 designation */
.rh-sig4    { height: 22.7pt; }  /* row 32 date */
.rh-ao      { height: 15pt;   }  /* row 33 */

/* ══ TEXT STYLES ══ */
.tc  { text-align: center; }
.tl  { text-align: left;   }
.tr  { text-align: right;  }
.bold   { font-weight: bold; }
.italic { font-style: italic; }
.sz10   { font-size: 10pt; }
.sz11   { font-size: 11pt; }
.sz12   { font-size: 12pt; }
.sz14   { font-size: 14pt; }
.sz16   { font-size: 16pt; }
.px3 { padding-left: 3px; padding-right: 3px; }
.pl6 { padding-left: 6px; }
.pl3 { padding-left: 3px; }
</style>
</head>
<body>

<!-- ══ TOOLBAR (screen only) ══ -->
<div class="toolbar">
    <a href="{{ route('client.ris.show', $ris->id) }}" class="btn btn-gray">&#8592; Back</a>
    <button class="btn btn-green" onclick="window.print()">&#128438; Print / Save PDF</button>
</div>

<div class="page">

    <!-- ══ LETTERHEAD ══ -->
    <div class="lh-wrap">
        <div class="lh-logos">
            <img src="{{ asset('assets/img/Bagong_Pilipinas_logo.png') }}" alt="Bagong Pilipinas">
            <img src="{{ asset('assets/img/ati-header.png') }}" alt="ATI">
        </div>
        <div class="lh-appendix">Appendix 63</div>
    </div>

    <!-- ══ MAIN TABLE (all rows in one table for accurate col widths) ══ -->
    <table>
        <colgroup>
            <col class="c1"><col class="c2"><col class="c3"><col class="c4">
            <col class="c5"><col class="c6"><col class="c7"><col class="c8">
        </colgroup>

        {{-- ── ROW 7: Title ── --}}
        <tr class="rh-title">
            <td colspan="8" class="tc bold sz14" style="padding: 1px 0;">REQUISITION AND ISSUE SLIP</td>
        </tr>

        {{-- ── ROW 8: tiny gap ── --}}
        <tr class="rh-gap1"><td colspan="8"></td></tr>

        {{-- ── ROW 9: Entity Name / Fund Cluster ── --}}
        <tr class="rh-entity">
            <td colspan="6" class="tc bold sz12" style="padding: 2px 4px; align: left;">Entity Name: ATI-RTC I</td>
            <td colspan="2" class="tl bold sz12" style="padding: 2px 4px;">Fund Cluster: __________</td>
        </tr>

        {{-- ── ROW 10: tiny gap ── --}}
        <tr class="rh-gap2"><td colspan="8"></td></tr>

        {{-- ── ROW 11: Division / Responsibility Center Code ── --}}
        <tr class="rh-div">
            <td class="tl sz12 bl2 bt2" style="padding: 2px 3px;">Division :</td>
            <td colspan="4" class="tl sz10 bt2 br2" style="padding: 2px 3px;">{{ $ris->division ?? 'Agricultural Training Institute-Regional Training Center I' }}</td>
            <td colspan="3" class="tl sz12 bl2 bt2 br2" style="padding: 2px 3px;">Responsibility Center Code: ____________</td>
        </tr>

        {{-- ── ROW 12: Office / RIS No. ── --}}
        <tr class="rh-office">
            <td colspan="5" class="tl sz12 bl2 br2" style="padding: 2px 3px;">Office: <span style="text-decoration:underline;">{{ $ris->office ?? 'ATI-RTC I, Tebag East, Sta. Barbara, Pangasinan' }}</span></td>
            <td colspan="3" class="tl sz12 bl2 br2" style="padding: 2px 3px;">RIS No. : <span style="text-decoration:underline;">{{ $ris->reference }}</span></td>
        </tr>

        {{-- ── ROW 13: Section group headers ── --}}
        <tr class="rh-grp">
            <td colspan="4" class="tc bold italic sz14 bl2 bt2 bb1 br1" style="padding: 2px 0;"><em>Requisition</em></td>
            <td colspan="2" class="tc bold italic sz14 bl1 bt2 bb1 br1" style="padding: 2px 0;">Stock Available?</td>
            <td colspan="2" class="tc bold italic sz14 bl1 bt2 bb1 br2" style="padding: 2px 0;"><em>Issue</em></td>
        </tr>

        {{-- ── ROW 14: Column headers ── --}}
        <tr class="rh-hdr">
            <td class="tc sz12 bl2 bt1 bb2 br1" style="padding: 2px 2px;">Stock<br>No.</td>
            <td class="tc sz12 bl1 bt1 br1"      style="padding: 2px 2px;">Unit</td>
            <td class="tc sz12 bl1 bt1 br1"      style="padding: 2px 2px;">Description</td>
            <td class="tc sz12 bl1 bt1 br1"      style="padding: 2px 2px;">Quantity</td>
            <td class="tc sz12 bl1 bt1 br1"      style="padding: 2px 2px;">Yes</td>
            <td class="tc sz12 bl1 bt1 br1"      style="padding: 2px 2px;">No</td>
            <td class="tc sz12 bl1 bt1 br1"      style="padding: 2px 2px;">Quantity</td>
            <td class="tc sz12 bl1 bt1 br2"      style="padding: 2px 2px;">Remarks</td>
        </tr>

        {{-- ── ROWS 15-24: Data rows ── --}}
        @php
            $supplies  = $ris->supplies;
            $itemCount = $supplies->count();
            $minRows   = max(10, $itemCount);
        @endphp

        @for ($i = 0; $i < $minRows; $i++)
            @php $supply = $supplies[$i] ?? null; $rh = $i === 0 ? 'rh-data1' : 'rh-data'; @endphp
            <tr class="{{ $rh }}">
                {{-- Stock No. --}}
                <td class="tc sz12 bl2 bb1" style="padding: 1px 2px;">{{ $supply ? $i + 1 : '' }}</td>
                {{-- Unit --}}
                <td class="tc sz10 bl1 bt1 bb1 br1" style="padding: 1px 2px;">{{ $supply?->unit ?? '' }}</td>
                {{-- Description --}}
                <td class="tl sz11 bl1 bt1 bb1 br1" style="padding: 1px 3px;">{{ $supply?->name ?? '' }}</td>
                {{-- Quantity Requested --}}
                <td class="tc sz10 bl1 bt1 bb1 br1" style="padding: 1px 2px;">{{ $supply?->pivot->quantity_requested ?? '' }}</td>
                {{-- Yes --}}
                <td class="tc sz12 bl1 bt1 bb1 br1" style="padding: 1px 2px;">
                    @if ($supply && $supply->pivot->status === 'approved') &#10003; @endif
                </td>
                {{-- No --}}
                <td class="tc sz12 bl1 bt1 bb1 br1" style="padding: 1px 2px;">
                    @if ($supply && $supply->pivot->status === 'rejected') &#10003; @endif
                </td>
                {{-- Quantity Issued --}}
                <td class="tc sz12 bl1 bt1 bb1 br1" style="padding: 1px 2px;">
                    @if ($supply && !empty($supply->pivot->quantity_issued)){{ $supply->pivot->quantity_issued }}@endif
                </td>
                {{-- Remarks --}}
                <td class="tl sz12 bl1 bt1 bb1 br2" style="padding: 1px 3px;">
                    @if ($supply && $supply->pivot->status === 'rejected') Insufficient stock @endif
                </td>
            </tr>
        @endfor

        {{-- ── ROWS 25-27: Purpose (col A × 3 rows; cols B-H merged 3 rows) ── --}}
        <tr class="rh-purp1">
            <td class="sz12 bl2" style="padding: 2px 3px;">Purpose:</td>
            <td colspan="7" rowspan="3" class="tl sz11 bl1 bt1 bb2 br2" style="padding: 3px 5px; vertical-align: top;">
                {{ $ris->purpose }}{{ $ris->notes ? ' — ' . $ris->notes : '' }}
            </td>
        </tr>
        <tr class="rh-purp2">
            <td class="bl2"></td>
        </tr>
        <tr class="rh-purp3">
            <td class="bl2 bb2"></td>
        </tr>

        {{-- ── ROW 28: Signature role labels ── --}}
        <tr class="rh-sig0">
            <td class="bl2 bt2"></td>
            <td class="br1"></td>
            <td colspan="2" class="tc bold sz12 bl1 br1" style="padding: 2px 3px;">Requested by:</td>
            <td colspan="2" class="tl bold sz12 bl1 br1" style="padding: 2px 3px;">Approved by:</td>
            <td class="tl bold sz12 bl1 br1" style="padding: 2px 3px;">Issued by:</td>
            <td class="tl bold sz12 br2" style="padding: 2px 3px;">Received by:</td>
        </tr>

        {{-- ── ROW 29: Signature line ── --}}
        <tr class="rh-sig1">
            <td class="sz12 bl2 bb1" style="padding: 2px 3px;">Signature:</td>
            <td class="br1 bb1"></td>
            <td colspan="2" class="bl1 bb1 br1"></td>
            <td colspan="2" class="bl1 bb1 br1"></td>
            <td class="bl1 bb1 br1"></td>
            <td class="bb1 br2"></td>
        </tr>

        {{-- ── ROW 30: Printed Name ── --}}
        <tr class="rh-sig2">
            <td colspan="2" class="tl sz12 bl2 bt1 bb1 br1" style="padding: 2px 3px;">Printed Name:</td>
            <td colspan="2" class="tc bold sz10 bl1 bt1 bb1 br1" style="padding: 2px 3px;">
                {{ strtoupper($ris->requester->name ?? '') }}
            </td>
            <td colspan="2" class="tc bold sz9 bl1 bt1 bb1 br1" style="padding: 2px 3px;">
                {{ strtoupper($ris->approver->name ?? 'JAYVEE BRYAN G. CARILLO') }}
            </td>
            <td class="tc bold sz10 bl1 bt1 bb1 br1" style="padding: 2px 3px;">FRANKLIN A. SALCEDO</td>
            <td class="tc bold sz10 bl1 bt1 bb1 br2" style="padding: 2px 3px;">
                {{ strtoupper($ris->requester->name ?? '') }}
            </td>
        </tr>

        {{-- ── ROW 31: Designation ── --}}
        <tr class="rh-sig3">
            <td colspan="2" class="tc sz12 bl2 bt1 bb1 br1" style="padding: 2px 3px;">Designation:</td>
            <td colspan="2" class="tc bold sz10 bl1 bt1 bb1 br1" style="padding: 2px 3px; font-size:9pt;">
                {{ $ris->requester->designation ?? '' }}
            </td>
            <td colspan="2" class="tc bold sz10 bl1 bt1 bb1 br1" style="padding: 2px 3px;">
                {{ $ris->approver->designation ?? 'Center Director' }}
            </td>
            <td class="tc bold sz10 bl1 bt1 bb1 br1" style="padding: 2px 3px; line-height:1.2;">Supply and Property Officer</td>
            <td class="tc bold sz10 bl1 bt1 bb1 br2" style="padding: 2px 3px; font-size:9pt;">
                {{ $ris->requester->designation ?? '' }}
            </td>
        </tr>

        {{-- ── ROW 32: Date ── --}}
        <tr class="rh-sig4">
            <td colspan="2" class="tc sz12 bl2 bb2 br1" style="padding: 2px 3px;">Date:</td>
            <td colspan="2" class="tc sz10 bl1 bb2 br1" style="padding: 2px 3px;">
                {{ $ris->created_at ? $ris->created_at->format('M d, Y') : '' }}
            </td>
            <td colspan="2" class="tc sz10 bl1 bb2 br1" style="padding: 2px 3px;">
                {{ $ris->approved_at ? \Carbon\Carbon::parse($ris->approved_at)->format('M d, Y') : '' }}
            </td>
            <td class="tc sz10 bl1 bb2 br1" style="padding: 2px 3px;">
                {{ $ris->approved_at ? \Carbon\Carbon::parse($ris->approved_at)->format('M d, Y') : '' }}
            </td>
            <td class="bb2 br2"></td>
        </tr>

        {{-- ── ROW 33: AO note ── --}}
        <tr class="rh-ao">
            <td colspan="8" class="tl italic sz10" style="padding: 2px 0 0 3px;">AO 6/15/02</td>
        </tr>

    </table>

</div>{{-- /page --}}

<script>
    window.addEventListener('load', () => setTimeout(() => window.print(), 700));
</script>
</body>
</html>