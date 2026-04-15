{{-- filepath: resources/views/client/ris/create.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New RIS Request</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
<div class="container">
    @include('layouts.core.sidebar')
    <div class="details">
        @include('layouts.core.header')

        <div class="ris-wrap">

            {{-- Header --}}
            <div class="ris-page-header">
                <a href="{{ route('client.ris.index') }}" class="ris-back-btn">
                    <i class="fas fa-arrow-left"></i> My Requests
                </a>
                <h1 class="ris-page-title">
                    <i class="fas fa-file-alt"></i> New Requisition for Issuance Slip
                </h1>
            </div>

            {{-- Alerts --}}
            @if(session('error'))
                <div class="ris-alert ris-alert-danger"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
            @endif

            <form action="{{ route('client.ris.store') }}" method="POST" id="risForm">
                @csrf

                <div class="ris-layout">

                    {{-- Left: main form --}}
                    <div class="ris-main">

                        @if($errors->any())
                        <div class="ris-alert ris-alert-danger">
                            <i class="fas fa-exclamation-circle"></i>
                            <div>
                                <strong>Please fix the following:</strong>
                                <ul style="margin:6px 0 0;padding-left:16px;font-size:13px">
                                    @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                                </ul>
                            </div>
                        </div>
                        @endif

                        {{-- Purpose --}}
                        <div class="ris-field">
                            <label class="ris-label ris-label-required" for="purpose">Purpose / Reason for Request</label>
                            <div class="ris-input-wrap">
                                <i class="fas fa-pencil-alt ris-input-icon"></i>
                                <input type="text" id="purpose" name="purpose"
                                       class="ris-input ris-input-icon-left @error('purpose') ris-input-error @enderror"
                                       value="{{ old('purpose') }}"
                                       placeholder="..."
                                       required>
                            </div>
                            @error('purpose') <span class="ris-field-error">{{ $message }}</span> @enderror
                        </div>
<!-- 
                        {{-- Date needed --}}
                        <div class="ris-field">
                            <label class="ris-label" for="date_needed">
                                Date Needed <span class="ris-optional">(Optional)</span>
                            </label>
                            <input type="date" id="date_needed" name="date_needed"
                                   class="ris-input @error('date_needed') ris-input-error @enderror"
                                   value="{{ old('date_needed') }}"
                                   min="{{ date('Y-m-d') }}">
                            @error('date_needed') <span class="ris-field-error">{{ $message }}</span> @enderror
                        </div> -->

                        <!-- {{-- Notes --}}
                        <div class="ris-field">
                            <label class="ris-label" for="notes">
                                Additional Notes <span class="ris-optional">(Optional)</span>
                            </label>
                            <textarea id="notes" name="notes" rows="3"
                                      class="ris-input ris-textarea @error('notes') ris-input-error @enderror"
                                      placeholder="Any special instructions or remarks…">{{ old('notes') }}</textarea>
                            @error('notes') <span class="ris-field-error">{{ $message }}</span> @enderror
                        </div> -->

                        {{-- Supplies --}}
                        <div class="ris-field">
                            <div class="ris-supplies-header">
                                <label class="ris-label ris-label-required">
                                    <i class="fas fa-boxes"></i> Supplies Requested
                                </label>
                                <button type="button" class="ris-btn ris-btn-primary ris-btn-sm" onclick="addSupplyRow()">
                                    <i class="fas fa-plus"></i> Add Item
                                </button>
                            </div>

                            <div id="suppliesContainer" class="ris-supplies-container">
                                <div class="ris-supplies-empty" id="suppliesEmpty">
                                    <i class="fas fa-box-open"></i>
                                    <p>No items added yet. Click <strong>Add Item</strong> to begin your request.</p>
                                </div>
                            </div>

                            @error('supplies')
                                <span class="ris-field-error">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>{{-- /ris-main --}}

                    {{-- Right: summary sidebar --}}
                    <div class="ris-sidebar">
                        <div class="ris-summary-card">
                            <div class="ris-summary-head">
                                <i class="fas fa-clipboard-list"></i> Request Summary
                            </div>
                            <div class="ris-summary-body">
                                <div class="ris-summary-row">
                                    <span>Requestor</span>
                                    <strong>{{ auth()->user()->name }}</strong>
                                </div>
                                <div class="ris-summary-row">
                                    <span>Date Filed</span>
                                    <strong>{{ now()->format('M d, Y') }}</strong>
                                </div>
                                <div class="ris-summary-row">
                                    <span>Items</span>
                                    <strong id="summaryItemCount">0</strong>
                                </div>
                                <div class="ris-summary-divider"></div>
                                <div class="ris-summary-note">
                                    <i class="fas fa-info-circle"></i>
                                    Your request will be reviewed by an administrator before supplies are issued.
                                </div>
                            </div>
                            <div class="ris-summary-foot">
                                <a href="{{ route('client.ris.index') }}" class="ris-btn ris-btn-secondary ris-btn-full">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                                <button type="submit" class="ris-btn ris-btn-success ris-btn-full">
                                    <i class="fas fa-paper-plane"></i> Submit Request
                                </button>
                            </div>
                        </div>
                    </div>

                </div>{{-- /ris-layout --}}
            </form>

        </div>
    </div>
</div>

@include('layouts.core.footer')

<style>
.ris-wrap{padding:24px;display:flex;flex-direction:column;gap:18px}
.ris-layout{display:grid;grid-template-columns:1fr 280px;gap:20px;align-items:start}
.ris-page-header{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px}
.ris-back-btn{display:inline-flex;align-items:center;gap:8px;color:#296218;font-size:14px;font-weight:500;text-decoration:none;transition:gap .15s}
.ris-back-btn:hover{gap:13px}
.ris-page-title{font-size:20px;font-weight:600;color:#296218;margin:0;display:flex;align-items:center;gap:8px}
.ris-alert{display:flex;align-items:flex-start;gap:10px;padding:12px 16px;border-radius:8px;font-size:14px;font-weight:500}
.ris-alert-danger{background:#f8d7da;border:1px solid #f5c6cb;color:#721c24}
.ris-alert-success{background:#d4edda;border:1px solid #c3e6cb;color:#155724}
.ris-main{background:#fff;border:1px solid #e9ecef;border-radius:12px;padding:24px;display:flex;flex-direction:column;gap:18px}
.ris-field{display:flex;flex-direction:column;gap:6px}
.ris-label{font-size:14px;font-weight:600;color:#495057}
.ris-label-required::after{content:" *";color:#dc3545}
.ris-optional{font-weight:400;color:#6c757d;font-size:12px}
.ris-field-error{font-size:12px;color:#dc3545}
.ris-input-wrap{position:relative}
.ris-input-icon{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#adb5bd;font-size:13px;pointer-events:none}
.ris-input{width:100%;box-sizing:border-box;padding:9px 14px;border:1px solid #dee2e6;border-radius:8px;font-size:14px;color:#495057;background:#fff;outline:none;transition:border-color .15s,box-shadow .15s}
.ris-input-icon-left{padding-left:36px}
.ris-input:focus{border-color:#296218;box-shadow:0 0 0 3px rgba(41,98,24,.1)}
.ris-input-error{border-color:#dc3545!important}
.ris-textarea{resize:vertical;min-height:80px}
.ris-supplies-header{display:flex;align-items:center;justify-content:space-between}
.ris-supplies-container{border:2px dashed #dee2e6;border-radius:10px;padding:14px;background:#f8f9fa;min-height:80px;display:flex;flex-direction:column;gap:10px}
.ris-supplies-empty{text-align:center;color:#6c757d;padding:16px;display:flex;flex-direction:column;align-items:center;gap:8px}
.ris-supplies-empty i{font-size:32px;color:#ced4da}
.ris-supplies-empty p{margin:0;font-size:13px}
.ris-supply-row{display:grid;grid-template-columns:1fr 120px 36px;gap:10px;align-items:center;background:#fff;border:1px solid #e9ecef;border-radius:8px;padding:12px}
.ris-supply-stock{font-size:11px;color:#6c757d;margin-top:3px;display:flex;align-items:center;gap:4px}
.ris-supply-stock.low{color:#dc3545}
.ris-select{width:100%;box-sizing:border-box;padding:9px 32px 9px 14px;border:1px solid #dee2e6;border-radius:8px;font-size:14px;color:#495057;background:#fff;outline:none;cursor:pointer;appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23adb5bd' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 10px center;transition:border-color .15s}
.ris-select:focus{border-color:#296218;box-shadow:0 0 0 3px rgba(41,98,24,.1);outline:none}
.ris-btn{display:inline-flex;align-items:center;gap:6px;padding:0 16px;height:38px;border-radius:8px;font-size:14px;font-weight:500;cursor:pointer;border:none;text-decoration:none;white-space:nowrap;transition:filter .15s}
.ris-btn:hover{filter:brightness(.9)}
.ris-btn-sm{height:32px;padding:0 12px;font-size:13px}
.ris-btn-full{width:100%;justify-content:center}
.ris-btn-primary{background:#296218;color:#fff}
.ris-btn-success{background:#28a745;color:#fff}
.ris-btn-secondary{background:#6c757d;color:#fff}
.ris-del-btn{width:34px;height:34px;border-radius:7px;border:none;cursor:pointer;background:#dc3545;color:#fff;display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0;transition:filter .15s}
.ris-del-btn:hover{filter:brightness(.88)}
.ris-summary-card{background:#fff;border:1px solid #e9ecef;border-radius:12px;overflow:hidden;position:sticky;top:20px}
.ris-summary-head{background:#296218;color:#fff;padding:16px 18px;font-size:15px;font-weight:600;display:flex;align-items:center;gap:8px}
.ris-summary-body{padding:18px;display:flex;flex-direction:column;gap:12px}
.ris-summary-row{display:flex;justify-content:space-between;align-items:center;font-size:13px;color:#495057}
.ris-summary-row strong{color:#212529;font-weight:600}
.ris-summary-divider{height:1px;background:#e9ecef}
.ris-summary-note{font-size:12px;color:#6c757d;background:#f8f9fa;border-radius:7px;padding:10px 12px;display:flex;gap:8px;align-items:flex-start;line-height:1.5}
.ris-summary-note i{color:#296218;margin-top:1px;flex-shrink:0}
.ris-summary-foot{padding:14px 18px;border-top:1px solid #e9ecef;display:flex;flex-direction:column;gap:8px}
@media(max-width:768px){.ris-layout{grid-template-columns:1fr}.ris-summary-card{position:static}.ris-supply-row{grid-template-columns:1fr 90px 34px}}
</style>

<script>
let supplyIndex = 0;
const supplies = @json($supplies);

function addSupplyRow() {
    const container = document.getElementById('suppliesContainer');
    const empty = document.getElementById('suppliesEmpty');
    if (empty) empty.remove();

    let opts = '<option value="">Select a supply…</option>';
    supplies.forEach(s => {
        opts += `<option value="${s.id}" data-stock="${s.quantity}" data-unit="${s.unit}" data-min="${s.minimum_stock ?? 0}">
            ${s.name}
        </option>`;
    });

    const row = document.createElement('div');
    row.className = 'ris-supply-row';
    row.dataset.index = supplyIndex;
    row.innerHTML = `
        <div>
            <select name="supplies[${supplyIndex}][supply_id]" class="ris-select ris-supply-select" required onchange="onSelectChange(this)">${opts}</select>
        </div>
        <input type="number" name="supplies[${supplyIndex}][quantity]" class="ris-input ris-supply-qty"
               placeholder="Qty" min="1" required oninput="validateQty(this)">
        <button type="button" class="ris-del-btn" onclick="removeRow(this)" title="Remove">
            <i class="fas fa-trash"></i>
        </button>
    `;
    container.appendChild(row);
    supplyIndex++;
    updateSummary();
}

function onSelectChange(sel) {
    updateSummary();
}

function validateQty(input) {
    input.setCustomValidity('');
    input.classList.remove('ris-input-error');
    updateSummary();
}

function removeRow(btn) {
    btn.closest('.ris-supply-row').remove();
    const container = document.getElementById('suppliesContainer');
    if (!container.querySelector('.ris-supply-row')) {
        container.innerHTML = `
            <div class="ris-supplies-empty" id="suppliesEmpty">
                <i class="fas fa-box-open"></i>
                <p>No items added yet. Click <strong>Add Item</strong> to begin your request.</p>
            </div>`;
    }
    updateSummary();
}

function updateSummary() {
    document.getElementById('summaryItemCount').textContent =
        document.querySelectorAll('.ris-supply-row').length;
}
</script>
</body>
</html>