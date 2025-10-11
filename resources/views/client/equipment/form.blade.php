{{-- 
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
    
    <form method="POST" action="{{ $action }}">
        @csrf
        @if($formMethod !== 'POST')
            @method($formMethod)
        @endif
        
        <div class="form-grid">
            <!-- Property Number -->
            <div class="form-group">
                <label for="property_number" class="form-label required">Property Number</label>
                <div class="input-group">
                    <i class="fas fa-barcode"></i>
                    <input type="text" id="property_number" name="property_number" class="form-input" 
                           value="{{ old('property_number', $isEdit ? $equipment->property_number : '') }}" 
                           required placeholder="e.g., EQP-2025-001 or 2025-ABC-123">
                </div>
                @error('property_number')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Article -->
            <div class="form-group">
                <label for="article" class="form-label required">Article</label>
                <div class="input-group">
                    <i class="fas fa-tag"></i>
                    <input type="text" id="article" name="article" class="form-input" 
                           value="{{ old('article', $isEdit ? $equipment->article : '') }}" 
                           required placeholder="Enter article name">
                </div>
                @error('article')
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
                           required placeholder="e.g., pcs, unit, set, lot, pair">
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
            
            <!-- Remarks (previously Condition) -->
            <div class="form-group">
                <label for="condition" class="form-label required">Remarks</label>
                <select id="condition" name="condition" class="form-select" required>
                    <option value="">Select Remarks</option>
                    @php
                        $currentCondition = old('condition', $isEdit ? $equipment->condition : 'Serviceable');
                    @endphp
                    <option value="Serviceable" {{ $currentCondition == 'Serviceable' ? 'selected' : '' }}>
                        Serviceable
                    </option>
                    <option value="Unserviceable" {{ $currentCondition == 'Unserviceable' ? 'selected' : '' }}>
                        Unserviceable
                    </option>
                </select>
                @error('condition')
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
            
            <!-- Responsibility Center (Location) -->
            <div class="form-group">
                <label for="location" class="form-label">Responsibility Center</label>
                <div class="input-group">
                    <i class="fas fa-building"></i>
                    <input type="text" id="location" name="location" class="form-input" 
                           value="{{ old('location', $isEdit ? $equipment->location : '') }}" 
                           placeholder="Department, office, or responsible unit">
                </div>
                @error('location')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Responsible Person -->
            <div class="form-group">
                <label for="responsible_person" class="form-label">Responsible Person</label>
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" id="responsible_person" name="responsible_person" class="form-input" 
                           value="{{ old('responsible_person', $isEdit ? $equipment->responsible_person : '') }}" 
                           placeholder="Name of person responsible for this equipment">
                </div>
                @error('responsible_person')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Description -->
            <div class="form-group full-width">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-input form-textarea" 
                          placeholder="Enter detailed description, specifications, or features">{{ old('description', $isEdit ? $equipment->description : '') }}</textarea>
                @error('description')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="form-actions">
            <a href="{{ route('client.equipment.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i>
                Cancel
            </a>
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i>
                {{ $isEdit ? 'Update' : 'Save' }} Equipment
            </button>
        </div>
    </form>
</div>

<script>
    // Set today's date as default for acquisition date (only for create form)
    document.addEventListener('DOMContentLoaded', function() {
        const acquisitionDateInput = document.getElementById('acquisition_date');
        @if(!$isEdit)
        if (!acquisitionDateInput.value) {
            acquisitionDateInput.value = new Date().toISOString().split('T')[0];
        }
        @endif
    });
</script>