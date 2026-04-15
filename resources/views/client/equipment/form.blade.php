{{-- 
    Equipment Form
    Usage: 
    @include('client.equipment.form', ['equipment' => $equipment, 'action' => route('equipment.update', $equipment), 'method' => 'PUT'])
    @include('client.equipment.form', ['action' => route('equipment.store')])
--}}

@php
    $isEdit = isset($equipment) && $equipment->exists;
    $formMethod = $method ?? 'POST';
@endphp

<div class="form-content">
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Please fix the following errors:</strong>
            <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <form method="POST" action="{{ $action }}" id="equipment-form">
        @csrf
        @if($formMethod !== 'POST')
            @method($formMethod)
        @endif
        {{-- Hidden fields populated by create_blade.php / edit_blade.php header controls --}}
        <input type="hidden" id="document_type_submit"   name="document_type"   value="{{ old('document_type', $isEdit ? ($equipment->document_type ?? '') : '') }}">
        <input type="hidden" id="document_number_submit" name="document_number" value="{{ old('document_number', $isEdit ? ($equipment->document_number ?? '') : '') }}">
        
        {{-- ── ICS / PAR mismatch error banner (shown/hidden by JS) ── --}}
        <div id="docTypeMismatchBanner" style="display:none; margin-bottom: 16px;">
            <div style="display:flex; align-items:flex-start; gap:10px; background:rgba(220,38,38,0.1); border:1.5px solid rgba(220,38,38,0.45); border-radius:8px; padding:12px 16px;">
                <i class="fas fa-ban" style="color:#dc2626; font-size:15px; margin-top:2px; flex-shrink:0;"></i>
                <div>
                    <strong style="display:block; color:#dc2626; font-size:13px; margin-bottom:2px;">Cannot save — document type mismatch</strong>
                    <span id="docTypeMismatchMsg" style="font-size:13px; color:#b91c1c;"></span>
                </div>
            </div>
        </div>

        <div class="form-grid">
            <!-- Property Number -->
            <div class="form-group">
                <label for="property_number" class="form-label required">Property Number</label>
                <div class="input-group">
                    <i class="fas fa-barcode"></i>
                    <input type="text" id="property_number" name="property_number" class="form-input" 
                           value="{{ old('property_number', $isEdit ? $equipment->property_number : '') }}" 
                           required placeholder="Enter property number">
                </div>
                @error('property_number')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Article -->
            <div class="form-group" style="position:relative;">
                <label for="article" class="form-label required">Article</label>
                <div class="input-group">
                    <i class="fas fa-tag"></i>
                    <input type="text" id="article" name="article" class="form-input"
                           value="{{ old('article', $isEdit ? $equipment->article : '') }}"
                           required placeholder="Enter article name"
                           autocomplete="off">
                    <div id="article-dropdown" class="autocomplete-dropdown" style="display:none;"></div>
                </div>
                @error('article')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Classification with Autocomplete -->
            <div class="form-group">
                <label for="classification" class="form-label">Classification</label>
                <div class="input-group" style="position: relative;">
                    <i class="fas fa-layer-group"></i>
                    <input type="text" id="classification" name="classification" class="form-input" 
                           value="{{ old('classification', $isEdit ? $equipment->classification : '') }}" 
                           placeholder="Enter Classification"
                           autocomplete="off">
                    <div id="classification-dropdown" class="autocomplete-dropdown" style="display: none;"></div>
                </div>
                @error('classification')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Unit of Measurement -->
            <div class="form-group">
                <label for="unit_of_measurement" class="form-label required">Unit of Measurement</label>
                <div class="input-group">
                    <i class="fas fa-ruler"></i>
                    <input type="text" id="unit_of_measurement" name="unit_of_measurement" class="form-input" 
                           value="{{ old('unit_of_measurement', $isEdit ? $equipment->unit_of_measurement : '') }}" 
                           required placeholder="Enter unit of measurement">
                </div>
                @error('unit_of_measurement')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Unit Value -->
            <div class="form-group">
                <label for="unit_value" class="form-label required">Unit Value</label>
                <div class="input-group">
                    <i class="fas fa-peso-sign"></i>
                    <input type="number" id="unit_value" name="unit_value" class="form-input" 
                           value="{{ old('unit_value', $isEdit ? number_format($equipment->unit_value, 2, '.', '') : '0.00') }}" 
                           step="0.01" min="0" required placeholder="0.00">
                </div>
                @error('unit_value')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- Remarks (Condition) -->
            <div class="form-group">
                <label for="condition" class="form-label required">Remarks</label>

                @php
                    $currentCondition = old('condition', $isEdit ? $equipment->condition : 'Serviceable');
                @endphp

                <select id="condition" name="condition" class="form-select" required>
                    <option value="">Select Remarks</option>
                    <option value="Serviceable"   @selected($currentCondition === 'Serviceable')>Serviceable</option>
                    <option value="Unserviceable" @selected($currentCondition === 'Unserviceable')>Unserviceable</option>
                </select>

                @error('condition')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Disposal Method (Conditional Field) -->
            <div class="form-group" id="disposal-method-group" style="display: none;">
                <label for="disposal_method" class="form-label required">Disposal Method</label>

                @php
                    $currentDisposalMethod = old('disposal_method', $isEdit ? ($equipment->disposal_method ?? '') : '');
                @endphp

                <select id="disposal_method" name="disposal_method" class="form-select">
                    <option value="">Select Disposal Method</option>
                    <option value="sale"        @selected($currentDisposalMethod === 'sale')>Sale</option>
                    <option value="transfer"    @selected($currentDisposalMethod === 'transfer')>Transfer</option>
                    <option value="destruction" @selected($currentDisposalMethod === 'destruction')>Destruction</option>
                    <option value="others"      @selected($currentDisposalMethod === 'others')>Others (Specify)</option>
                </select>

                @error('disposal_method')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Disposal Details (Conditional Field for "Others") -->
            <div class="form-group" id="disposal-details-group" style="display: none;">
                <label for="disposal_details" class="form-label required">Specify Disposal Details</label>
                <div class="input-group">
                    <i class="fas fa-info-circle"></i>
                    <input type="text" id="disposal_details" name="disposal_details" class="form-input" 
                           value="{{ old('disposal_details', $isEdit ? ($equipment->disposal_details ?? '') : '') }}" 
                           placeholder="Please specify the disposal method">
                </div>
                @error('disposal_details')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Acquisition Date -->
            <div class="form-group">
                <label for="acquisition_date" class="form-label">Acquisition Date</label>
                <div class="input-group">
                    <i class="fas fa-calendar"></i>
                    <input type="date" id="acquisition_date" name="acquisition_date" class="form-input" 
                           value="{{ old('acquisition_date', $isEdit && $equipment->acquisition_date ? $equipment->acquisition_date->format('Y-m-d') : date('Y-m-d')) }}">
                </div>
                @error('acquisition_date')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- Responsibility Center — department dropdown -->
            <div class="form-group" style="position:relative; z-index:10;">
                <label for="responsibility_center" class="form-label">Responsibility Center</label>
                @php
                    $currentRC   = old('responsibility_center', $isEdit ? ($equipment->responsibility_center ?? '') : '');
                    $departments = ['ISS', 'AFU', 'CDMS', 'PAS', 'PMEU', 'OCD', 'DORM'];
                @endphp
                <div class="rc-select-wrap">
                    <i class="fas fa-sitemap rc-icon"></i>
                    <select id="responsibility_center" name="responsibility_center" class="rc-select">
                        <option value="">— Select —</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept }}" @selected($currentRC === $dept)>
                                {{ $dept }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('responsibility_center')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Responsible Person — searchable user dropdown -->
            <div class="form-group">
                <label for="responsible_person_search" class="form-label">Responsible Person</label>
                <div class="input-group" style="position: relative;">
                    <i class="fas fa-user"></i>
                    <input type="text" id="responsible_person_search" class="form-input"
                           placeholder="Search user by name..."
                           value="{{ old('responsible_person', $isEdit ? $equipment->responsible_person : '') }}"
                           autocomplete="off">
                    <button type="button" id="rpClearBtn" class="rp-clear-btn"
                            style="{{ old('responsible_person', $isEdit ? $equipment->responsible_person : '') ? '' : 'display:none;' }}">
                        <i class="fas fa-times"></i>
                    </button>
                    <div id="rp-dropdown" class="autocomplete-dropdown" style="display:none;"></div>
                </div>
                <input type="hidden" name="responsible_person" id="responsible_person"
                       value="{{ old('responsible_person', $isEdit ? $equipment->responsible_person : '') }}">
                <div id="rpSelectedPreview" class="rp-selected-preview"
                     style="{{ old('responsible_person', $isEdit ? $equipment->responsible_person : '') ? '' : 'display:none;' }}">
                    <i class="fas fa-user-check"></i>
                    <span id="rpSelectedName">{{ old('responsible_person', $isEdit ? $equipment->responsible_person : '') }}</span>
                    <span class="rp-selected-tag">Assigned</span>
                </div>
                @error('responsible_person')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Description (left column, spans 2 cols) -->
            <div class="form-group desc-left">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-input form-textarea" 
                          placeholder="Enter detailed description, specifications, or features">{{ old('description', $isEdit ? $equipment->description : '') }}</textarea>
                @error('description')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- Quantity Stepper (right column, beside description) -->
            <div class="form-group qty-right">
                <label for="quantity" class="form-label required">Quantity</label>
                <div class="qty-stepper-wrap">
                    <button type="button" class="qty-btn qty-minus" aria-label="Decrease">
                        <i class="fas fa-minus"></i>
                    </button>
                    <input type="number" id="quantity" name="quantity" class="qty-input"
                           value="{{ old('quantity', $isEdit ? ($equipment->quantity ?? 1) : 1) }}"
                           min="1" max="9999" required readonly>
                    <button type="button" class="qty-btn qty-plus" aria-label="Increase">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                <span class="form-text">Number of identical equipment items to add</span>
                @error('quantity')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="form-actions">
            <a href="{{ route('client.equipment.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i>
                Cancel
            </a>
            <button type="submit" id="form-submit-btn" class="btn btn-success">
                <i class="fas fa-save"></i>
                {{ $isEdit ? 'Update' : 'Save' }} Equipment
            </button>
        </div>
    </form>
</div>

<style>
/* ── Classification autocomplete (unchanged) ── */
.autocomplete-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1000;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-top: 2px;
}
.autocomplete-item {
    padding: 10px 15px;
    cursor: pointer;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    align-items: center;
    gap: 8px;
}
.autocomplete-item:hover { background: #f8f9fa; }
.autocomplete-item:last-child { border-bottom: none; }
.autocomplete-item i { color: #6c757d; font-size: 12px; }
.autocomplete-empty {
    padding: 10px 15px;
    color: #6c757d;
    font-size: 14px;
    text-align: center;
}
/* ── Responsibility Center clean select ── */
.rc-select-wrap {
    position: relative;
    display: flex;
    align-items: center;
}
.rc-icon {
    position: absolute;
    left: 12px;
    color: #6c757d;
    font-size: 14px;
    pointer-events: none;
    z-index: 1;
}
.rc-select {
    width: 100%;
    height: 44px;
    padding: 0 36px 0 36px;
    border: 1.5px solid #d1d5db;
    border-radius: 10px;
    background: #fff;
    font-size: 14px;
    color: #333;
    cursor: pointer;
    outline: none;
    appearance: none;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%236c757d' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    transition: border-color .2s, box-shadow .2s;
}
.rc-select:hover,
.rc-select:focus {
    border-color: #296218;
    box-shadow: 0 0 0 3px rgba(41,98,24,0.1);
}

/* ── Description + Quantity side-by-side layout ── */
.desc-left {
    grid-column: 1 / span 2;
}
.qty-right {
    grid-column: 3;
    align-self: start;
}

@media (max-width: 900px) {
    .desc-left  { grid-column: 1 / -1; }
    .qty-right  { grid-column: 1 / -1; }
}

/* ── Quantity Stepper ── */
.qty-stepper-wrap {
    display: flex;
    align-items: center;
    gap: 0;
    height: 44px;
    border: 1.5px solid #d1d5db;
    border-radius: 10px;
    overflow: hidden;
    background: #fff;
    width: 160px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    transition: border-color .2s;
}
.qty-stepper-wrap:focus-within {
    border-color: #296218;
    box-shadow: 0 0 0 3px rgba(41,98,24,0.12);
}
.qty-btn {
    width: 44px;
    height: 100%;
    border: none;
    background: #f3f4f6;
    color: #374151;
    font-size: 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background .15s, color .15s;
    flex-shrink: 0;
    user-select: none;
}
.qty-btn:hover { background: #296218; color: #fff; }
.qty-btn:active { background: #1e4a12; color: #fff; }
.qty-btn:disabled { background: #f9fafb; color: #9ca3af; cursor: not-allowed; }
.qty-input {
    flex: 1;
    border: none;
    text-align: center;
    font-size: 16px;
    font-weight: 700;
    color: #1f2937;
    background: transparent;
    outline: none;
    -moz-appearance: textfield;
    padding: 0;
    min-width: 0;
}
.qty-input::-webkit-outer-spin-button,
.qty-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }

.form-group { transition: all 0.3s ease; }
.form-text { display: block; margin-top: 5px; font-size: 12px; color: #6c757d; }
.form-hint { display: block; margin-top: 4px; font-size: 11px; color: #6c757d; font-style: italic; }
#disposal-method-group,
#disposal-details-group { animation: slideDown 0.3s ease; }
@keyframes slideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ── Responsible Person extras ── */
.rp-clear-btn {
    position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
    background: transparent; border: none; cursor: pointer;
    color: #adb5bd; font-size: 13px; display: flex; align-items: center;
    padding: 4px; z-index: 2;
}
.rp-clear-btn:hover { color: #dc3545; }
.rp-selected-preview {
    display: flex; align-items: center; gap: 8px; margin-top: 6px;
    padding: 7px 12px; background: #f0faf0; border: 1.5px solid #a3d9a5;
    border-radius: 6px; font-size: 13px; color: #296218; font-weight: 600;
}
.rp-selected-tag {
    margin-left: auto; background: #296218; color: #fff;
    font-size: 11px; padding: 2px 8px; border-radius: 10px; font-weight: 700;
}

/* ── Submit button disabled state ── */
#form-submit-btn:disabled {
    opacity: 0.45;
    cursor: not-allowed;
    pointer-events: none;
    filter: grayscale(0.4);
}
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const conditionSelect      = document.getElementById('condition');
        const disposalMethodGroup  = document.getElementById('disposal-method-group');
        const disposalMethodSelect = document.getElementById('disposal_method');
        const disposalDetailsGroup = document.getElementById('disposal-details-group');
        const disposalDetailsInput = document.getElementById('disposal_details');
        const acquisitionDateInput = document.getElementById('acquisition_date');
        
        @if(!$isEdit)
        if (!acquisitionDateInput.value) {
            acquisitionDateInput.value = new Date().toISOString().split('T')[0];
        }
        @endif

        function toggleDisposalMethod() {
            if (conditionSelect.value === 'Unserviceable') {
                disposalMethodGroup.style.display = 'block';
                disposalMethodSelect.setAttribute('required', 'required');
            } else {
                disposalMethodGroup.style.display = 'none';
                disposalMethodSelect.removeAttribute('required');
                disposalMethodSelect.value = '';
                disposalDetailsGroup.style.display = 'none';
                disposalDetailsInput.removeAttribute('required');
                disposalDetailsInput.value = '';
            }
        }

        function toggleDisposalDetails() {
            if (disposalMethodSelect.value === 'others') {
                disposalDetailsGroup.style.display = 'block';
                disposalDetailsInput.setAttribute('required', 'required');
            } else {
                disposalDetailsGroup.style.display = 'none';
                disposalDetailsInput.removeAttribute('required');
                disposalDetailsInput.value = '';
            }
        }

        toggleDisposalMethod();
        toggleDisposalDetails();
        conditionSelect.addEventListener('change', toggleDisposalMethod);
        disposalMethodSelect.addEventListener('change', toggleDisposalDetails);

        // ── Quantity Stepper ──
        const qtyInput = document.getElementById('quantity');
        const qtyMinus = document.querySelector('.qty-minus');
        const qtyPlus  = document.querySelector('.qty-plus');

        function updateQtyBtns() {
            const val = parseInt(qtyInput.value) || 1;
            qtyMinus.disabled = val <= 1;
            qtyPlus.disabled  = val >= 9999;
        }

        qtyMinus.addEventListener('click', function () {
            let val = parseInt(qtyInput.value) || 1;
            if (val > 1) { qtyInput.value = val - 1; updateQtyBtns(); }
        });
        qtyPlus.addEventListener('click', function () {
            let val = parseInt(qtyInput.value) || 1;
            if (val < 9999) { qtyInput.value = val + 1; updateQtyBtns(); }
        });
        updateQtyBtns();

        // ════════════════════════════════════════════════════════════
        //  ICS / PAR strict validation
        //  - ICS  → unit value must be BELOW ₱50,000
        //  - PAR  → unit value must be ₱50,000 OR ABOVE
        //  Blocks the submit button and shows an error banner.
        //  Also intercepts the form's submit event as a safety net.
        // ════════════════════════════════════════════════════════════
        const unitValueInput   = document.getElementById('unit_value');
        const docTypeHidden    = document.getElementById('document_type_submit');
        const submitBtn        = document.getElementById('form-submit-btn');
        const theForm          = document.getElementById('equipment-form');
        const mismatchBanner   = document.getElementById('docTypeMismatchBanner');
        const mismatchMsg      = document.getElementById('docTypeMismatchMsg');

        /**
         * Returns an error string when the doc-type / unit-value combination
         * is invalid, or null when everything is fine.
         */
        function getDocTypeMismatch() {
            const docType   = (docTypeHidden.value || '').trim().toUpperCase();
            const unitValue = parseFloat(unitValueInput.value) || 0;

            if (!docType || unitValue <= 0) return null; // nothing to validate yet

            if (docType === 'ICS' && unitValue >= 50000) {
                return 'ICS cannot be used for items worth ₱'
                    + unitValue.toLocaleString('en-PH', { minimumFractionDigits: 2 })
                    + ' — that is ₱50,000 or above. Please change the document type to PAR.';
            }

            if (docType === 'PAR' && unitValue < 50000) {
                return 'PAR cannot be used for items worth ₱'
                    + unitValue.toLocaleString('en-PH', { minimumFractionDigits: 2 })
                    + ' — that is below ₱50,000. Please change the document type to ICS.';
            }

            return null;
        }

        /** Refresh the banner + submit button state. */
        function validateDocType() {
            const error = getDocTypeMismatch();

            if (error) {
                mismatchMsg.textContent      = error;
                mismatchBanner.style.display = 'block';
                submitBtn.disabled           = true;
                submitBtn.title              = error;
            } else {
                mismatchBanner.style.display = 'none';
                submitBtn.disabled           = false;
                submitBtn.title              = '';
            }
        }

        // Re-validate whenever unit value changes.
        // The doc type hidden field is updated by create_blade.php / edit_blade.php
        // via the MutationObserver below.
        unitValueInput.addEventListener('input',  validateDocType);
        unitValueInput.addEventListener('change', validateDocType);

        // Watch the hidden document_type field for changes driven by the header
        // dropdown (create/edit blade sets it via JS).
        new MutationObserver(validateDocType).observe(docTypeHidden, { attributes: true, attributeFilter: ['value'] });

        // Also poll the hidden field value every 300 ms as a fallback for
        // cases where the value is set via direct assignment (no attribute change).
        let _lastDocType = docTypeHidden.value;
        setInterval(function () {
            if (docTypeHidden.value !== _lastDocType) {
                _lastDocType = docTypeHidden.value;
                validateDocType();
            }
        }, 300);

        // Hard block on form submit — catches any edge-case bypass.
        theForm.addEventListener('submit', function (e) {
            const error = getDocTypeMismatch();
            if (error) {
                e.preventDefault();
                e.stopImmediatePropagation();
                mismatchMsg.textContent      = error;
                mismatchBanner.style.display = 'block';
                mismatchBanner.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });

        // Run once on page load (handles old() repopulation after a back()->withInput()).
        validateDocType();

        // ── Article autocomplete ──
        const articleInput    = document.getElementById('article');
        const articleDropdown = document.getElementById('article-dropdown');
        const articleSuggestions = [
            // 'Desktop Computer', 'Laptop', 'Printer', 'Scanner', 'Projector',
            // 'Air Conditioner', 'Electric Fan', 'Refrigerator', 'Microwave Oven',
            // 'Office Chair', 'Office Table', 'Filing Cabinet', 'Whiteboard',
            // 'CCTV Camera', 'UPS Battery', 'Network Switch', 'WiFi Router',
            // 'Photocopier', 'Fax Machine', 'Telephone', 'Fire Extinguisher',
            // 'Generator Set', 'Water Dispenser', 'Television', 'Monitor'
        ];

        articleInput.addEventListener('focus', function () { showArticleDropdown(this.value); });
        articleInput.addEventListener('input', function () { showArticleDropdown(this.value); });
        document.addEventListener('click', function (e) {
            if (!articleInput.contains(e.target) && !articleDropdown.contains(e.target)) {
                articleDropdown.style.display = 'none';
            }
        });

        function showArticleDropdown(query) {
            const filtered = query.trim()
                ? articleSuggestions.filter(s => s.toLowerCase().includes(query.toLowerCase()))
                : articleSuggestions;
            if (filtered.length === 0) {
                articleDropdown.style.display = 'none';
                return;
            }
            articleDropdown.innerHTML = filtered.map(item => `
                <div class="autocomplete-item" data-value="${item}">
                    <i class="fas fa-tag"></i>${item}
                </div>`).join('');
            articleDropdown.style.display = 'block';
            articleDropdown.querySelectorAll('.autocomplete-item').forEach(item => {
                item.addEventListener('mousedown', function (e) {
                    e.preventDefault();
                    articleInput.value = this.dataset.value;
                    articleDropdown.style.display = 'none';
                });
            });
        }

        // ── Classification autocomplete ──
        const classificationInput = document.getElementById('classification');
        const classDropdown       = document.getElementById('classification-dropdown');
        let classifications = [];

        fetch('/client/equipment/api/classifications')
            .then(response => response.json())
            .then(data => { classifications = data; })
            .catch(error => console.error('Error fetching classifications:', error));

        classificationInput.addEventListener('focus', function() {
            if (classifications.length > 0) showClassDropdown(classifications);
        });
        classificationInput.addEventListener('input', function() {
            const value = this.value.toLowerCase().trim();
            showClassDropdown(value === '' ? classifications : classifications.filter(i => i.toLowerCase().includes(value)));
        });
        document.addEventListener('click', function(e) {
            if (!classificationInput.contains(e.target) && !classDropdown.contains(e.target)) {
                classDropdown.style.display = 'none';
            }
        });
        function showClassDropdown(items) {
            if (items.length === 0) {
                classDropdown.innerHTML = '<div class="autocomplete-empty">No classifications found. Type to create a new one.</div>';
                classDropdown.style.display = 'block';
                return;
            }
            classDropdown.innerHTML = items.map(item => `
                <div class="autocomplete-item" data-value="${item}">
                    <i class="fas fa-layer-group"></i>${item}
                </div>`).join('');
            classDropdown.style.display = 'block';
            classDropdown.querySelectorAll('.autocomplete-item').forEach(item => {
                item.addEventListener('click', function() {
                    classificationInput.value = this.dataset.value;
                    classDropdown.style.display = 'none';
                });
            });
        }

        // ── Responsible Person searchable dropdown ──
        const users      = @json($users ?? []);
        const rpSearch   = document.getElementById('responsible_person_search');
        const rpHidden   = document.getElementById('responsible_person');
        const rpDropdown = document.getElementById('rp-dropdown');
        const rpClearBtn = document.getElementById('rpClearBtn');
        const rpPreview  = document.getElementById('rpSelectedPreview');
        const rpName     = document.getElementById('rpSelectedName');

        function rpInitials(n) {
            return n.split(' ').map(w => w[0]).join('').substring(0, 2).toUpperCase();
        }

        function showRpDropdown(query) {
            const list = query
                ? users.filter(u => u.name.toLowerCase().includes(query.toLowerCase()))
                : users;
            if (list.length === 0) {
                rpDropdown.innerHTML = '<div class="autocomplete-empty">No users found.</div>';
            } else {
                rpDropdown.innerHTML = list.map(u => `
                    <div class="autocomplete-item" data-name="${u.name}">
                        <span style="width:26px;height:26px;border-radius:50%;background:#296218;color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;">
                            ${rpInitials(u.name)}
                        </span>
                        ${u.name}
                    </div>`).join('');
                rpDropdown.querySelectorAll('.autocomplete-item').forEach(item => {
                    item.addEventListener('mousedown', function(e) {
                        e.preventDefault();
                        selectUser(this.dataset.name);
                    });
                });
            }
            rpDropdown.style.display = 'block';
        }

        function selectUser(name) {
            rpHidden.value           = name;
            rpSearch.value           = name;
            rpName.textContent       = name;
            rpPreview.style.display  = 'flex';
            rpClearBtn.style.display = 'flex';
            rpDropdown.style.display = 'none';
        }

        rpSearch.addEventListener('input', function() {
            rpClearBtn.style.display = this.value ? 'flex' : 'none';
            if (this.value.trim()) {
                showRpDropdown(this.value.trim());
            } else {
                rpHidden.value           = '';
                rpPreview.style.display  = 'none';
                rpDropdown.style.display = 'none';
            }
        });
        rpSearch.addEventListener('focus', function() { showRpDropdown(this.value.trim()); });
        rpSearch.addEventListener('blur',  function() {
            setTimeout(() => { rpDropdown.style.display = 'none'; }, 150);
        });
        rpClearBtn.addEventListener('click', function() {
            rpHidden.value           = '';
            rpSearch.value           = '';
            rpPreview.style.display  = 'none';
            rpClearBtn.style.display = 'none';
            rpDropdown.style.display = 'none';
            rpSearch.focus();
        });
        document.addEventListener('click', function(e) {
            if (!rpSearch.contains(e.target) && !rpDropdown.contains(e.target)) {
                rpDropdown.style.display = 'none';
            }
        });
    });
</script>