<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock In</title>
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
            
                <div class="form-container">
                    <div class="form-header">
                        <a href="{{ route('client.stockcard.index') }}" class="back-button">
                            <i class="fas fa-arrow-left"></i>
                            Back to Stock Card
                        </a>
                        <h1 class="form-title">
                            <i class="fas fa-edit"></i>
                            Stock In
                        </h1>
                                                <!-- Stock Information Display - Always Visible -->
                        <div id="stock-display" class="stock-info-card">    
                            <div class="stock-info-row">
                                <div class="stock-item">
                                    <label>Current Stock:</label>
                                    <span id="current-stock-display" class="stock-value current disabled">-- --</span>
                                </div>
                                <div class="stock-item">
                                    <label>Adding:</label>
                                    <span id="adding-quantity" class="stock-value adding disabled">-- --</span>
                                </div>
                                <div class="stock-item">
                                    <label>New Total:</label>
                                    <span id="new-total" class="stock-value new-total disabled">-- --</span>
                                </div>
                            </div>
                        </div>
                    </div>
                <div class="form-container">
                    <form action="{{ route('client.stockcard.stock-in.process') }}" method="POST">
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

                        <div class="form-group">
                            <label for="quantity">Quantity to Add</label>
                            <input type="number" name="quantity" id="quantity" class="form-control" 
                                   value="{{ old('quantity') }}" min="1" required>
                            @error('quantity')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="notes">Description</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-plus"></i>
                                Add Stock
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
                    $('#no-item-selected').hide();
                    $('.stock-value').removeClass('disabled');
                    updateStockDisplay();
                } else {
                    $('#no-item-selected').show();
                    $('.stock-value').addClass('disabled');
                    resetStockDisplay();
                }
            });

            // Update calculations when quantity changes
            $('#quantity').on('input', function() {
                if ($('#supply_id').val()) {
                    updateStockDisplay();
                }
            });

            function updateStockDisplay() {
                const addingQuantity = parseInt($('#quantity').val()) || 0;
                const newTotal = currentStock + addingQuantity;

                if ($('#supply_id').val()) {
                    $('#current-stock-display').text(currentStock + ' ' + unit);
                    $('#adding-quantity').text('+' + addingQuantity + ' ' + unit);
                    $('#new-total').text(newTotal + ' ' + unit);

                    // Add visual feedback for positive change
                    if (addingQuantity > 0) {
                        $('#adding-quantity').addClass('positive');
                        $('#new-total').addClass('positive');
                    } else {
                        $('#adding-quantity').removeClass('positive');
                        $('#new-total').removeClass('positive');
                    }
                }
            }

            function resetStockDisplay() {
                $('#current-stock-display').text('-- --');
                $('#adding-quantity').text('-- --');
                $('#new-total').text('-- --');
                $('#adding-quantity').removeClass('positive');
                $('#new-total').removeClass('positive');
            }

            // Initialize display if form has old values
            if ($('#supply_id').val()) {
                $('#supply_id').trigger('change');
            }
        });
    </script>

    <style>
        .stock-info-card {
            background-color: #5a8a4e;
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
            color: #007bff;
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
            color: #dee2e6;
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

        .stock-value.adding {
            color: #28a745;
            border-color: #28a745;
        }



        .stock-value.new-total {
            color: #007bff;
            border-color: #007bff;
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