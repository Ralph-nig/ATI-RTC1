<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Out</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/supplies.css') }}">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="container">
        @include('layouts.core.sidebar')
        <div class="details">
            @include('layouts.core.header')
            
            <div class="supplies-container">
                <!-- Header Section -->
                <div class="supplies-header">
                    <h1 class="supplies-title">
                        <i class="fas fa-arrow-up"></i>
                        Stock Out
                    </h1>
                    
                    <div class="action-buttons">
                        <a href="{{ route('client.stockcard.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                            Back to Stock Card
                        </a>
                    </div>
                </div>
                
                <!-- Form Section -->
                <div class="form-container">
                    <form action="{{ route('client.stockcard.stock-out.process') }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label for="supply_id">Select Supply Item</label>
                            <select name="supply_id" id="supply_id" class="form-control" required>
                                <option value="">Choose a supply item...</option>
                                @foreach($supplies as $supply)
                                    <option value="{{ $supply->id }}" 
                                            data-current-stock="{{ $supply->quantity }}"
                                            data-unit="{{ $supply->unit }}"
                                            {{ old('supply_id') == $supply->id ? 'selected' : '' }}>
                                        #{{ str_pad($supply->id, 4, '0', STR_PAD_LEFT) }} - {{ $supply->name }} 
                                        (Current: {{ $supply->quantity }} {{ $supply->unit }})
                                    </option>
                                @endforeach
                            </select>
                            @error('supply_id')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Stock Information Display - Always Visible -->
                        <div id="stock-display" class="stock-info-card">
                            <div class="stock-info-row">
                                <div class="stock-item">
                                    <label>Current Stock:</label>
                                    <span id="current-stock-display" class="stock-value current disabled">-- --</span>
                                </div>
                                <div class="stock-item">
                                    <label>Removing:</label>
                                    <span id="removing-quantity" class="stock-value removing disabled">-- --</span>
                                </div>
                                <div class="stock-item">
                                    <label>Remaining:</label>
                                    <span id="remaining-total" class="stock-value remaining disabled">-- --</span>
                                </div>
                            </div>
                            <div id="warning-message" class="stock-warning" style="display: none;">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span>Warning: Not enough stock available!</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="quantity">Quantity to Remove</label>
                            <input type="number" name="quantity" id="quantity" class="form-control" 
                                   value="{{ old('quantity') }}" min="1" required>
                            @error('quantity')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="notes">Notes (Optional)</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-actions">
                            <button type="submit" id="submit-btn" class="btn btn-danger">
                                <i class="fas fa-minus"></i>
                                Remove Stock
                            </button>
                            <a href="{{ route('client.stockcard.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>   
    </div>

    <script>
        $(document).ready(function() {
            let currentStock = 0;
            let unit = '';

            // Initialize display
            updateStockDisplay();

            // Show current stock when supply is selected
            $('#supply_id').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                currentStock = parseInt(selectedOption.data('current-stock')) || 0;
                unit = selectedOption.data('unit') || '';
                
                if (selectedOption.val()) {
                    $('#quantity').attr('max', currentStock);
                    $('#no-item-selected').hide();
                    $('.stock-value').removeClass('disabled');
                    updateStockDisplay();
                    validateQuantity();
                } else {
                    $('#no-item-selected').show();
                    $('.stock-value').addClass('disabled');
                    $('#quantity').removeAttr('max');
                    resetStockDisplay();
                    $('#warning-message').hide();
                    $('#submit-btn').prop('disabled', false);
                }
            });

            // Update calculations when quantity changes
            $('#quantity').on('input', function() {
                if ($('#supply_id').val()) {
                    updateStockDisplay();
                    validateQuantity();
                }
            });

            function updateStockDisplay() {
                const removingQuantity = parseInt($('#quantity').val()) || 0;
                const remainingTotal = currentStock - removingQuantity;

                if ($('#supply_id').val()) {
                    $('#current-stock-display').text(currentStock + ' ' + unit);
                    $('#removing-quantity').text('-' + removingQuantity + ' ' + unit);
                    $('#remaining-total').text(remainingTotal + ' ' + unit);

                    // Add visual feedback based on remaining stock
                    $('#removing-quantity').removeClass('negative warning normal');
                    $('#remaining-total').removeClass('negative zero positive');

                    if (remainingTotal < 0) {
                        $('#remaining-total').addClass('negative');
                        $('#removing-quantity').addClass('negative');
                    } else if (remainingTotal === 0) {
                        $('#remaining-total').addClass('zero');
                        $('#removing-quantity').addClass('warning');
                    } else {
                        $('#remaining-total').addClass('positive');
                        $('#removing-quantity').addClass('normal');
                    }
                }
            }

            function resetStockDisplay() {
                $('#current-stock-display').text('-- --');
                $('#removing-quantity').text('-- --');
                $('#remaining-total').text('-- --');
                $('#removing-quantity').removeClass('negative warning normal');
                $('#remaining-total').removeClass('negative zero positive');
                $('#quantity').removeClass('error');
            }

            function validateQuantity() {
                const removingQuantity = parseInt($('#quantity').val()) || 0;
                
                if (removingQuantity > currentStock) {
                    $('#quantity').addClass('error');
                    $('#submit-btn').prop('disabled', true);
                    $('#warning-message').show();
                } else {
                    $('#quantity').removeClass('error');
                    $('#submit-btn').prop('disabled', false);
                    $('#warning-message').hide();
                }
            }

            // Initialize display if form has old values
            if ($('#supply_id').val()) {
                $('#supply_id').trigger('change');
            }
        });
    </script>

    <style>
        .stock-info-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 2px solid #dee2e6;
            border-radius: 12px;
            padding: 20px;
            margin: 15px 0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        }

        .stock-info-header {
            text-align: center;
            margin-bottom: 15px;
        }

        .stock-info-header h3 {
            margin: 0 0 5px 0;
            color: #495057;
            font-size: 18px;
            font-weight: 600;
        }

        .stock-info-header h3 i {
            margin-right: 8px;
            color: #dc3545;
        }

        .no-selection-text {
            margin: 0;
            color: #6c757d;
            font-style: italic;
            font-size: 14px;
        }

        .stock-info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }

        .stock-item {
            flex: 1;
            text-align: center;
        }

        .stock-item label {
            display: block;
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 8px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stock-value {
            display: block;
            font-size: 18px;
            font-weight: 700;
            padding: 12px 15px;
            border-radius: 8px;
            background: white;
            border: 2px solid #dee2e6;
            transition: all 0.3s ease;
            min-height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stock-value.disabled {
            background: #f8f9fa;
            color: #adb5bd;
            border-color: #e9ecef;
        }

        .stock-value.current {
            color: #495057;
            border-color: #6c757d;
        }

        .stock-value.removing.normal {
            color: #dc3545;
            border-color: #dc3545;
            background: rgba(220, 53, 69, 0.1);
            transform: scale(1.02);
        }

        .stock-value.removing.warning {
            color: #ffc107;
            border-color: #ffc107;
            background: rgba(255, 193, 7, 0.1);
            transform: scale(1.02);
        }

        .stock-value.removing.negative {
            color: #dc3545;
            border-color: #dc3545;
            background: rgba(220, 53, 69, 0.2);
            animation: pulse 1s infinite;
            transform: scale(1.02);
        }

        .stock-value.remaining.positive {
            color: #28a745;
            border-color: #28a745;
            background: rgba(40, 167, 69, 0.1);
            transform: scale(1.02);
        }

        .stock-value.remaining.zero {
            color: #ffc107;
            border-color: #ffc107;
            background: rgba(255, 193, 7, 0.1);
            transform: scale(1.02);
        }

        .stock-value.remaining.negative {
            color: #dc3545;
            border-color: #dc3545;
            background: rgba(220, 53, 69, 0.2);
            animation: pulse 1s infinite;
            transform: scale(1.02);
        }

        .stock-warning {
            margin-top: 15px;
            padding: 12px;
            background: rgba(220, 53, 69, 0.1);
            border: 2px solid #dc3545;
            border-radius: 8px;
            color: #dc3545;
            text-align: center;
            font-weight: 600;
            animation: pulse 1s infinite;
        }

        .stock-warning i {
            margin-right: 8px;
        }

        .form-control.error {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }

        @media (max-width: 768px) {
            .stock-info-row {
                flex-direction: column;
                gap: 10px;
            }
            
            .stock-item {
                width: 100%;
            }
        }
    </style>
</body>
</html>