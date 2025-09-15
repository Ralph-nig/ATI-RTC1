<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Supply Item</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/users/styles.css') }}">
    <!-- Ionicons CDN -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- jQuery for AJAX -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
        .form-container {
            background-color: #296218;
            border-radius: 15px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }
        
        .form-header {
            background: rgba(255,255,255,0.1);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            backdrop-filter: blur(10px);
        }
        
        .form-title {
            color: white;
            font-size: 24px;
            font-weight: bold;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-content {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #495057;
        }
        
        .form-label.required::after {
            content: " *";
            color: #dc3545;
        }
        
        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
            box-sizing: border-box;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #296218;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
        }
        
        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .form-select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            background: white;
            cursor: pointer;
            box-sizing: border-box;
        }
        
        .form-select:focus {
            outline: none;
            border-color: #296218;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
        }
        
        .input-group {
            position: relative;
        }
        
        .input-group .form-input {
            padding-left: 45px;
        }
        
        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            font-size: 14px;
        }
        
        .btn-success {
            background: #296218;
            color: white;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }
        
        .error-message {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
        }
        
        .alert {
            padding: 12px 16px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-weight: 500;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .back-button {
            display: flex;
            align-items: center;
            gap: 8px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            margin-bottom: 15px;
            transition: color 0.3s ease;
        }
        
        .back-button:hover {
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        @include('layouts.core.sidebar')
        <div class="details">
            @include('layouts.core.header')
            
            <div class="form-container">
                <div class="form-header">
                    <a href="{{ route('supplies.index') }}" class="back-button">
                        <i class="fas fa-arrow-left"></i>
                        Back to Supplies
                    </a>
                    <h1 class="form-title">
                        <i class="fas fa-edit"></i>
                        Edit Supply Item
                    </h1>
                </div>
                
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
                    
                    <!-- FIXED: Changed form action to use update route with supply ID and added method spoofing -->
                    <form method="POST" action="{{ route('supplies.update', $supply->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-grid">
                            <!-- Item Name -->
                            <div class="form-group">
                                <label for="name" class="form-label required">Item Name</label>
                                <div class="input-group">
                                    <i class="fas fa-box"></i>
                                    <!-- FIXED: Use supply data with old() fallback -->
                                    <input type="text" id="name" name="name" class="form-input" 
                                           value="{{ old('name', $supply->name) }}" required placeholder="Enter item name">
                                </div>
                                @error('name')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Category -->
                            <div class="form-group">
                                <label for="category" class="form-label">Category</label>
                                <div class="input-group">
                                    <i class="fas fa-tags"></i>
                                    <input type="text" id="category" name="category" class="form-input" 
                                           value="{{ old('category', $supply->category) }}" placeholder="e.g., Office Supplies, Equipment" 
                                           list="categories">
                                    <datalist id="categories">
                                        @foreach($categories ?? [] as $cat)
                                            <option value="{{ $cat }}">
                                        @endforeach
                                    </datalist>
                                </div>
                                @error('category')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Quantity -->
                            <div class="form-group">
                                <label for="quantity" class="form-label required">Quantity</label>
                                <div class="input-group">
                                    <i class="fas fa-calculator"></i>
                                    <input type="number" id="quantity" name="quantity" class="form-input" 
                                           value="{{ old('quantity', $supply->quantity) }}" min="0" required placeholder="0">
                                </div>
                                @error('quantity')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Unit -->
                            <div class="form-group">
                                <label for="unit" class="form-label required">Unit</label>
                                <select id="unit" name="unit" class="form-select" required>
                                    <option value="">Select Unit</option>
                                    <option value="pcs" {{ old('unit', $supply->unit) == 'pcs' ? 'selected' : '' }}>Pieces (pcs)</option>
                                    <option value="kg" {{ old('unit', $supply->unit) == 'kg' ? 'selected' : '' }}>Kilograms (kg)</option>
                                    <option value="g" {{ old('unit', $supply->unit) == 'g' ? 'selected' : '' }}>Grams (g)</option>
                                    <option value="liters" {{ old('unit', $supply->unit) == 'liters' ? 'selected' : '' }}>Liters</option>
                                    <option value="ml" {{ old('unit', $supply->unit) == 'ml' ? 'selected' : '' }}>Milliliters (ml)</option>
                                    <option value="boxes" {{ old('unit', $supply->unit) == 'boxes' ? 'selected' : '' }}>Boxes</option>
                                    <option value="packs" {{ old('unit', $supply->unit) == 'packs' ? 'selected' : '' }}>Packs</option>
                                    <option value="meters" {{ old('unit', $supply->unit) == 'meters' ? 'selected' : '' }}>Meters</option>
                                    <option value="rolls" {{ old('unit', $supply->unit) == 'rolls' ? 'selected' : '' }}>Rolls</option>
                                </select>
                                @error('unit')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="unit_price" class="form-label required">Unit Price</label>
                                <div class="input-group">
                                    <i class="fas fa-peso-sign"></i>
                                    <input type="number" id="unit_price" name="unit_price" class="form-input" 
                                           value="{{ old('unit_price', number_format($supply->unit_price, 2, '.', '')) }}" step="0.01" min="0" required placeholder="0.00">
                                </div>
                                @error('unit_price')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="minimum_stock" class="form-label required">Minimum Stock Level</label>
                                <div class="input-group">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <input type="number" id="minimum_stock" name="minimum_stock" class="form-input" 
                                           value="{{ old('minimum_stock', $supply->minimum_stock) }}" min="0" required placeholder="5">
                                </div>
                                <small style="color: #6c757d; font-size: 12px;">Alert when stock falls below this level</small>
                                @error('minimum_stock')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Supplier -->
                            <div class="form-group">
                                <label for="supplier" class="form-label">Supplier</label>
                                <div class="input-group">
                                    <i class="fas fa-truck"></i>
                                    <input type="text" id="supplier" name="supplier" class="form-input" 
                                           value="{{ old('supplier', $supply->supplier) }}" placeholder="Supplier name" 
                                           list="suppliers">
                                    <datalist id="suppliers">
                                        @foreach($suppliers ?? [] as $supplier)
                                            <option value="{{ $supplier }}">
                                        @endforeach
                                    </datalist>
                                </div>
                                @error('supplier')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Purchase Date -->
                            <div class="form-group">
                                <label for="purchase_date" class="form-label">Purchase Date</label>
                                <div class="input-group">
                                    <i class="fas fa-calendar"></i>
                                    <input type="date" id="purchase_date" name="purchase_date" class="form-input" 
                                           value="{{ old('purchase_date', $supply->purchase_date ? $supply->purchase_date->format('Y-m-d') : '') }}">
                                </div>
                                @error('purchase_date')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Description -->
                            <div class="form-group full-width">
                                <label for="description" class="form-label">Description</label>
                                <textarea id="description" name="description" class="form-input form-textarea" 
                                          placeholder="Enter item description, specifications, or other details">{{ old('description', $supply->description) }}</textarea>
                                @error('description')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Notes -->
                            <div class="form-group full-width">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea id="notes" name="notes" class="form-input form-textarea" 
                                          placeholder="Additional notes or comments">{{ old('notes', $supply->notes) }}</textarea>
                                @error('notes')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <a href="{{ route('supplies.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i>
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i>
                                Update Supply Item
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Auto-calculate total value
        function calculateTotal() {
            const quantity = parseFloat(document.getElementById('quantity').value) || 0;
            const unitPrice = parseFloat(document.getElementById('unit_price').value) || 0;
            const total = quantity * unitPrice;
            
            console.log('Total Value:', total.toFixed(2));
        }
        
        document.getElementById('quantity').addEventListener('input', calculateTotal);
        document.getElementById('unit_price').addEventListener('input', calculateTotal);
        
        // Calculate initial total on page load
        document.addEventListener('DOMContentLoaded', function() {
            calculateTotal();
        });
    </script>
</body>
</html>