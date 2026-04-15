<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Equipment</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/supplies.css') }}">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="container">
        @include('layouts.core.sidebar')
        <div class="details">
            @include('layouts.core.header')
            @include('layouts.core.footer')
            
            <div class="form-container">
                <div class="form-header">
                    <a href="{{ route('client.equipment.index') }}" class="back-button">
                        <i class="fas fa-arrow-left"></i>
                        Back to Equipment List
                    </a>
                    <div class="form-header-row">
                        <h1 class="form-title">
                            <i class="fas fa-plus-circle"></i>
                            Add New Equipment
                        </h1>

                        {{-- ── Document Type + Number in header ── --}}
                        @php
                            $oldDocType   = old('document_type', '');
                            $oldDocNumber = old('document_number', '');
                        @endphp
                        <div class="hdr-doc-wrap">
                            <label class="hdr-doc-label"><span class="hdr-req"></span></label>
                            <div class="hdr-split-control">
                                <div class="hdr-split-left">
                                    <select id="docTypeDropdown" class="hdr-split-select">
                                        <option value="">—</option>
                                        <option value="ICS" {{ $oldDocType === 'ICS' ? 'selected' : '' }}>ICS</option>
                                        <option value="PAR" {{ $oldDocType === 'PAR' ? 'selected' : '' }}>PAR</option>
                                    </select>
                                    <i class="fas fa-chevron-down hdr-split-arrow"></i>
                                </div>
                                <div class="hdr-split-divider"></div>
                                <input type="text" id="hdrDocNumber" class="hdr-split-number"
                                       value="{{ $oldDocNumber }}"
                                       placeholder="ICS-2026-04-0001">
                                <button type="button" id="hdrRegenBtn" class="hdr-split-regen" title="Re-generate">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                            <div id="docTypeWarning" style="display:none;margin-top:6px;">
                                <div style="display:flex;align-items:center;gap:7px;background:rgba(255,193,7,0.2);border:1.5px solid rgba(255,193,7,0.6);border-radius:7px;padding:7px 12px;font-size:12px;color:#fff;font-weight:500;">
                                    <i class="fas fa-exclamation-triangle" style="font-size:12px;flex-shrink:0;color:#ffd97d;"></i>
                                    <span id="docTypeWarningMsg"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                @include('client.equipment.form', [
                    'action' => route('equipment.store'),
                    'method' => 'POST'
                ])
            </div>
        </div>
    </div>

<style>
.form-header-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
}
.hdr-doc-wrap {
    display: flex;
    flex-direction: column;
    gap: 5px;
}
.hdr-doc-label {
    color: rgba(255,255,255,0.85);
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .4px;
    white-space: nowrap;
}
.hdr-req { color: #ffd97d; }

/* ── Split control: [ICS▾ | ICS-2026-04-0001      ] [↻] ── */
.hdr-split-control {
    display: flex;
    align-items: stretch;
    height: 42px;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.3);
}
.hdr-split-left {
    position: relative;
    display: flex;
    align-items: center;
    flex-shrink: 0;
}
.hdr-split-select {
    height: 100%;
    padding: 0 28px 0 14px;
    background: rgba(0,0,0,0.3);
    color: #fff;
    font-size: 13px;
    font-weight: 800;
    letter-spacing: .5px;
    border: none;
    outline: none;
    appearance: none;
    -webkit-appearance: none;
    cursor: pointer;
    min-width: 75px;
}
.hdr-split-select option { background: #1a3d10; color: #fff; font-weight: 700; }
.hdr-split-arrow {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 10px;
    color: rgba(255,255,255,0.6);
    pointer-events: none;
}
.hdr-split-divider {
    width: 1px;
    background: rgba(255,255,255,0.2);
    flex-shrink: 0;
}
.hdr-split-number {
    flex: 1;
    height: 100%;
    padding: 0 14px;
    background: rgba(255,255,255,0.15);
    color: #fff;
    font-size: 13px;
    font-family: monospace;
    font-weight: 600;
    border: none;
    outline: none;
    min-width: 180px;
}
.hdr-split-number::placeholder { color: rgba(255,255,255,0.4); font-style: italic; }
.hdr-split-number:focus { background: rgba(255,255,255,0.22); }
.hdr-split-regen {
    width: 42px;
    background: rgba(0,0,0,0.2);
    border: none;
    border-left: 1px solid rgba(255,255,255,0.2);
    color: rgba(255,255,255,0.8);
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px;
    transition: background .2s;
    flex-shrink: 0;
}
.hdr-split-regen:hover { background: rgba(0,0,0,0.35); color: #fff; }
.hdr-split-regen.spinning i { animation: hdrSpin .6s linear infinite; }
@keyframes hdrSpin { to { transform: rotate(360deg); } }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const hdrSelect  = document.getElementById('docTypeDropdown');
    const hdrNumber  = document.getElementById('hdrDocNumber');
    const hdrRegen   = document.getElementById('hdrRegenBtn');

    // Hidden submit fields inside the form (injected by form_blade.php)
    const typeSubmit = document.getElementById('document_type_submit');
    const numSubmit  = document.getElementById('document_number_submit');

    function fetchNumber(type) {
        if (!type) return;
        hdrRegen.classList.add('spinning');
        fetch(`/client/equipment/api/next-document-number?type=${type}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            hdrNumber.value = data.number;
            syncToForm();
        })
        .catch(() => {})
        .finally(() => hdrRegen.classList.remove('spinning'));
    }

    function syncToForm() {
        if (typeSubmit) typeSubmit.value = hdrSelect.value;
        if (numSubmit)  numSubmit.value  = hdrNumber.value;
    }

    hdrSelect.addEventListener('change', function () {
        syncToForm();
        fetchNumber(this.value);
    });

    hdrNumber.addEventListener('input', syncToForm);
    hdrRegen.addEventListener('click', function () { fetchNumber(hdrSelect.value); });

    // Warn when selected type doesn't match unit value — never auto-switch
    const unitVal = document.getElementById('unit_value');
    const warning = document.getElementById('docTypeWarning');
    const warningMsg = document.getElementById('docTypeWarningMsg');

    function checkDocTypeWarning() {
        const val      = parseFloat(unitVal ? unitVal.value : 0) || 0;
        const selected = hdrSelect.value;
        if (!selected || !val) { warning.style.display = 'none'; return; }

        if (selected === 'ICS' && val >= 50000) {
            warningMsg.textContent = 'Unit value is ₱' + val.toLocaleString('en-PH', {minimumFractionDigits:2}) + ' — PAR is recommended for items ₱50,000 and above. You can still save as ICS.';
            warning.style.display = 'block';
        } else if (selected === 'PAR' && val < 50000) {
            warningMsg.textContent = 'Unit value is ₱' + val.toLocaleString('en-PH', {minimumFractionDigits:2}) + ' — ICS is recommended for items below ₱50,000. You can still save as PAR.';
            warning.style.display = 'block';
        } else {
            warning.style.display = 'none';
        }
    }

    if (unitVal) {
        unitVal.addEventListener('change', checkDocTypeWarning);
        unitVal.addEventListener('input',  checkDocTypeWarning);
    }
    hdrSelect.addEventListener('change', checkDocTypeWarning);

    // On load — default to ICS if nothing selected
    if (!hdrSelect.value) {
        hdrSelect.value = 'ICS';
    }
    if (!hdrNumber.value) {
        fetchNumber(hdrSelect.value);
    } else {
        syncToForm();
    }
});
</script>
</body>
</html>