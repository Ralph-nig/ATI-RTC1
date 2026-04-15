<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Card - {{ $supply->name }}</title>
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
            @include('layouts.core.footer')
            
            <div class="supplies-container">

                <!-- Header Section -->
                <div class="form-header">
                    <div class="header-top">
                        <a href="{{ route('client.stockcard.index') }}" class="back-button">
                            <i class="fas fa-arrow-left"></i>
                            Back to Stock Card
                        </a>
                        <!-- <div class="action-buttons">
                            <a href="{{ route('client.stockcard.export.excel', $supply->id) }}" class="btn btn-primary">
                                <i class="fas fa-download"></i>
                                Export
                            </a>
                        </div> -->
                    </div>

                    <div class="supply-info-header">
                        <div class="supply-title">
                            <h2>#{{ str_pad($supply->id, 4, '0', STR_PAD_LEFT) }} - {{ $supply->name }}</h2>
                            @if($supply->description)
                                <p class="supply-description">{{ $supply->description }}</p>
                            @endif
                        </div>
                        <div class="current-stock">
                            <div class="stock-label">Current Stock</div>
                            <div class="stock-value {{ $supply->quantity <= $supply->minimum_stock ? 'low-stock' : 'normal-stock' }}">
                                {{ $supply->quantity }} {{ $supply->unit }}
                            </div>
                            @if($supply->quantity <= $supply->minimum_stock)
                                <div class="low-stock-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Low Stock Alert
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="supply-details">
                    <div class="detail-item">
                        <label>Category:</label>
                        <span>{{ $supply->category ?: 'Uncategorized' }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Unit of Measurement:</label>
                        <span>{{ $supply->unit }}</span>
                    </div>
                    @if($supply->minimum_stock)
                    <div class="detail-item">
                        <label>Minimum Stock:</label>
                        <span>{{ $supply->minimum_stock }} {{ $supply->unit }}</span>
                    </div>
                    @endif
                </div>

                <!-- Alerts -->
                @if(session('success'))
                    <div class="alert alert-success" style="margin-top: 16px;">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger" style="margin-top: 16px;">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                @endif

                <!-- ── Inline Stock Movement Form ── -->
                @if(auth()->user()->hasPermission('stock_in') || auth()->user()->hasPermission('stock_out'))
                <div class="inline-stock-form">
                    <form id="stockMovementForm" method="POST" action="{{ route('client.stockcard.stock-in.process') }}">
                        @csrf
                        <input type="hidden" name="supply_id" value="{{ $supply->id }}">
                        {{-- action URL is swapped by JS --}}

                        <div class="inline-form-row">

                            {{-- 1. Type toggle --}}
                            <div class="inline-field type-field">
                                <label class="inline-label">Type</label>
                                <div class="type-toggle">
                                    <button type="button" class="toggle-btn active" id="btnStockIn"
                                            @if(!auth()->user()->hasPermission('stock_in')) disabled @endif>
                                        <i class="fas fa-arrow-down"></i> Stock In
                                    </button>
                                    <button type="button" class="toggle-btn" id="btnStockOut"
                                            @if(!auth()->user()->hasPermission('stock_out')) disabled @endif>
                                        <i class="fas fa-arrow-up"></i> Stock Out
                                    </button>
                                </div>
                                <input type="hidden" name="_movement_type" id="movementType" value="in">
                            </div>

                            {{-- 2. Quantity --}}
                            <div class="inline-field qty-field">
                                <label class="inline-label" for="inlineQty">Quantity</label>
                                <input type="number" name="quantity" id="inlineQty" class="form-input"
                                       min="1" placeholder="Enter qty" required
                                       value="{{ old('quantity') }}">
                                <span id="stockWarning" class="inline-warning" style="display:none;">
                                    <i class="fas fa-exclamation-triangle"></i> Exceeds available stock
                                </span>
                                @error('quantity')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- 3. Purpose --}}
                            <div class="inline-field purpose-field">
                                <label class="inline-label" for="inlinePurpose">Purpose</label>
                                <input type="text" name="notes" id="inlinePurpose" class="form-input"
                                       placeholder="Enter purpose or description (optional)"
                                       value="{{ old('notes') }}">
                                @error('notes')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Submit --}}
                            <div class="inline-field submit-field">
                                <label class="inline-label">&nbsp;</label>
                                <button type="submit" id="submitBtn" class="btn btn-success">
                                    <i class="fas fa-plus" id="submitIcon"></i>
                                    <span id="submitLabel">Add Stock</span>
                                </button>
                            </div>

                        </div>
                    </form>
                </div>
                @endif

                <!-- Stock Movements Table -->
                <div class="supplies-table-container">
                    @if($movements->count() > 0)
                        <table class="stock-card-table">
                            <thead>
                                <tr>
                                    <th rowspan="2" style="width: 12%;">Date</th>
                                    <th rowspan="2" style="width: 14%;">Reference</th>
                                    <th rowspan="2" style="width: 10%;">Receipt<br>Qty.</th>
                                    <th colspan="2" style="width: 50%;">Issue</th>
                                    <th rowspan="2" style="width: 14%;">Balance<br>Qty.</th>
                                </tr>
                                <tr>
                                    <th style="width: 10%;">Qty.</th>
                                    <th style="width: 40%;">Purpose</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($movements as $movement)
                                    <tr class="movement-row {{ $movement->type === 'in' ? 'stock-in-row' : '' }}"
                                        data-id="{{ $movement->id }}"
                                        data-type="{{ $movement->type }}"
                                        data-quantity="{{ $movement->quantity }}"
                                        data-notes="{{ $movement->notes }}"
                                        data-reference="{{ $movement->reference }}"
                                        style="cursor: pointer;">
                                        <td>{{ $movement->created_at->format('F d, Y') }}</td>
                                        <td>{{ $movement->reference }}</td>
                                        @if($movement->type === 'in')
                                            <td style="text-align: center;" class="stock-in-cell">
                                                <span class="quantity-badge positive">+{{ $movement->quantity }}</span>
                                            </td>
                                            <td style="text-align: center;"></td>
                                            <td>{{ $movement->notes ?: 'Balance as of ' . $movement->created_at->format('F Y') }}</td>
                                        @else
                                            <td style="text-align: center;"></td>
                                            <td style="text-align: center;" class="stock-out-cell">
                                                <span class="quantity-badge negative">-{{ $movement->quantity }}</span>
                                            </td>
                                            <td>{{ $movement->notes ?: 'Issued' }}</td>
                                        @endif
                                        <td style="text-align: center;">{{ $movement->balance_after }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        @if($movements->hasPages())
                            <div class="pagination">
                                {{ $movements->links() }}
                            </div>
                        @endif
                    @else
                        <div class="empty-state">
                            <i class="fas fa-history"></i>
                            <h3>No stock movements found</h3>
                            <p>No stock movements have been recorded for this item yet.</p>
                        </div>
                    @endif
                </div>

            </div>
        </div>   
    </div>

    {{-- ── Floating Row Action Bar ── --}}
    <div id="rowActionBar" class="row-action-bar" style="display:none;">
        <div class="row-action-inner">
            <span id="rowActionLabel" class="row-action-label"></span>
            <div class="row-action-buttons">
                @if(auth()->user()->hasPermission('stock_in') || auth()->user()->hasPermission('stock_out'))
                <button id="btnEditRow" class="btn-row-action btn-row-edit">
                    <i class="fas fa-pen"></i> Edit
                </button>
                @endif
                @if(auth()->user()->hasPermission('delete_stock'))
                <button id="btnDeleteRow" class="btn-row-action btn-row-delete">
                    <i class="fas fa-trash"></i> Delete
                </button>
                @endif
                <button id="btnDeselectRow" class="btn-row-action btn-row-deselect">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- ── Edit Modal ── --}}
    <div id="editModal" class="modal-overlay" style="display:none;">
        <div class="modal-card">
            <div class="modal-header">
                <h3><i class="fas fa-pen"></i> Edit Movement</h3>
                <button class="modal-close" id="editModalClose">&times;</button>
            </div>
            <form id="editMovementForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="modal-field">
                        <label>Type</label>
                        <div class="type-card-group">
                            <button type="button" class="type-card" id="editBtnIn">
                                <span class="type-card-icon type-card-icon--in">
                                    <i class="fas fa-arrow-down"></i>
                                </span>
                                <span class="type-card-label">Stock In</span>
                                <span class="type-card-desc">Add to inventory</span>
                            </button>
                            <button type="button" class="type-card" id="editBtnOut">
                                <span class="type-card-icon type-card-icon--out">
                                    <i class="fas fa-arrow-up"></i>
                                </span>
                                <span class="type-card-label">Stock Out</span>
                                <span class="type-card-desc">Remove from inventory</span>
                            </button>
                        </div>
                        <input type="hidden" name="type" id="editType">
                    </div>
                    <div class="modal-field">
                        <label for="editQty">Quantity</label>
                        <input type="number" id="editQty" name="quantity" class="form-input" min="1" required>
                    </div>
                    <div class="modal-field">
                        <label for="editNotes">Purpose</label>
                        <input type="text" id="editNotes" name="notes" class="form-input" placeholder="Optional">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="editModalCancel">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Delete Confirm Modal ── --}}
    <div id="deleteModal" class="modal-overlay" style="display:none;">
        <div class="modal-card modal-card-sm">
            <div class="modal-header modal-header-danger">
                <h3><i class="fas fa-trash"></i> Delete Movement</h3>
                <button class="modal-close" id="deleteModalClose">&times;</button>
            </div>
            <div class="modal-body">
                <p id="deleteModalText" style="margin:0; color:#333; font-size:14px;"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="deleteModalCancel">Cancel</button>
                <form id="deleteMovementForm" method="POST" action="" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Yes, Delete</button>
                </form>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function () {
        const currentStock = {{ $supply->quantity }};
        const stockInUrl  = "{{ route('client.stockcard.stock-in.process') }}";
        const stockOutUrl = "{{ route('client.stockcard.stock-out.process') }}";

        let movementType = 'in';

        // Toggle between Stock In / Stock Out
        $('#btnStockIn').on('click', function () {
            if ($(this).is(':disabled')) return;
            movementType = 'in';
            $('#movementType').val('in');
            $('#stockMovementForm').attr('action', stockInUrl);

            $(this).addClass('active');
            $('#btnStockOut').removeClass('active');

            $('#submitBtn').removeClass('btn-danger').addClass('btn-success');
            $('#submitIcon').removeClass('fa-minus').addClass('fa-plus');
            $('#submitLabel').text('Add Stock');
            validateQty();
        });

        $('#btnStockOut').on('click', function () {
            if ($(this).is(':disabled')) return;
            movementType = 'out';
            $('#movementType').val('out');
            $('#stockMovementForm').attr('action', stockOutUrl);

            $(this).addClass('active');
            $('#btnStockIn').removeClass('active');

            $('#submitBtn').removeClass('btn-success').addClass('btn-danger');
            $('#submitIcon').removeClass('fa-plus').addClass('fa-minus');
            $('#submitLabel').text('Remove Stock');
            validateQty();
        });

        // Live validation
        $('#inlineQty').on('input', validateQty);

        function validateQty() {
            const qty = parseInt($('#inlineQty').val()) || 0;
            if (movementType === 'out' && qty > currentStock) {
                $('#stockWarning').show();
                $('#submitBtn').prop('disabled', true);
            } else {
                $('#stockWarning').hide();
                $('#submitBtn').prop('disabled', false);
            }
        }

        // Override form action before submit (in case JS sets it)
        $('#stockMovementForm').on('submit', function () {
            $(this).attr('action', movementType === 'in' ? stockInUrl : stockOutUrl);
        });

        // ── Row selection ──
        let selectedRow = null;

        $(document).on('click', '.movement-row', function () {
            if (selectedRow && selectedRow[0] === this) {
                deselectRow();
                return;
            }
            if (selectedRow) selectedRow.removeClass('row-selected');
            selectedRow = $(this);
            selectedRow.addClass('row-selected');

            const ref   = selectedRow.data('reference');
            const type  = selectedRow.data('type') === 'in' ? 'Stock In' : 'Stock Out';
            $('#rowActionLabel').html(`<strong>${ref}</strong> &mdash; ${type}`);
            $('#rowActionBar').fadeIn(200);
        });

        function deselectRow() {
            if (selectedRow) selectedRow.removeClass('row-selected');
            selectedRow = null;
            $('#rowActionBar').fadeOut(200);
        }

        $('#btnDeselectRow').on('click', deselectRow);

        // Click outside table deselects
        $(document).on('click', function (e) {
            if (!$(e.target).closest('.movement-row, #rowActionBar').length) {
                deselectRow();
            }
        });

        // ── Edit ──
        $('#btnEditRow').on('click', function () {
            if (!selectedRow) return;
            const id  = selectedRow.data('id');
            const qty = selectedRow.data('quantity');
            const notes = selectedRow.data('notes') || '';
            const type  = selectedRow.data('type');

            $('#editQty').val(qty);
            $('#editNotes').val(notes);
            $('#editType').val(type);

            if (type === 'in') {
                $('#editBtnIn').addClass('type-card--active-in');
                $('#editBtnOut').removeClass('type-card--active-out');
            } else {
                $('#editBtnOut').addClass('type-card--active-out');
                $('#editBtnIn').removeClass('type-card--active-in');
            }

        const editUrl = "{{ route('client.stockcard.movement.update', ['id' => '__ID__']) }}".replace('__ID__', id);
            $('#editMovementForm').attr('action', editUrl);
            $('#editModal').fadeIn(200);
        });

        $('#editBtnIn').on('click', function () {
            $('#editType').val('in');
            $(this).addClass('type-card--active-in');
            $('#editBtnOut').removeClass('type-card--active-out');
        });
        $('#editBtnOut').on('click', function () {
            $('#editType').val('out');
            $(this).addClass('type-card--active-out');
            $('#editBtnIn').removeClass('type-card--active-in');
        });

        $('#editModalClose, #editModalCancel').on('click', function () {
            $('#editModal').fadeOut(200);
        });

        // ── Delete ──
        $('#btnDeleteRow').on('click', function () {
            if (!selectedRow) return;
            const id  = selectedRow.data('id');
            const ref = selectedRow.data('reference');
            const type = selectedRow.data('type') === 'in' ? 'Stock In' : 'Stock Out';

            $('#deleteModalText').html(
                `Are you sure you want to delete the <strong>${type}</strong> movement with reference <strong>${ref}</strong>? This action cannot be undone.`
            );
            const deleteUrl = "{{ route('client.stockcard.movement.destroy', ['id' => '__ID__']) }}".replace('__ID__', id);
            $('#deleteMovementForm').attr('action', deleteUrl);
            $('#deleteModal').fadeIn(200);
        });

        $('#deleteModalClose, #deleteModalCancel').on('click', function () {
            $('#deleteModal').fadeOut(200);
        });

        // Close modals on overlay click
        $('.modal-overlay').on('click', function (e) {
            if ($(e.target).hasClass('modal-overlay')) $(this).fadeOut(200);
        });
    });
    </script>

    <style>
        /* ── Back button ── */
        .back-button {
            display: flex;
            align-items: center;
            gap: 8px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            margin-bottom: 15px;
            transition: color 0.3s ease;
        }
        .back-button:hover { color: white; }

        /* ── Supply info header ── */
        .supply-info-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            gap: 20px;
        }
        .supply-title h2 {
            margin: 0 0 8px 0;
            color: #dee2e6;
            font-size: 24px;
            font-weight: 700;
        }
        .supply-description {
            margin: 0;
            color: #f0f4f9;
            font-size: 16px;
            line-height: 1.5;
        }
        .current-stock { text-align: center; min-width: 180px; }
        .stock-label {
            font-size: 12px;
            color: #dee2e6;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            font-weight: 600;
        }
        .stock-value {
            font-size: 28px;
            font-weight: 800;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 8px;
        }
        .stock-value.normal-stock {
            color: #28a745;
            background: rgba(40,167,69,0.1);
            border: 2px solid #28a745;
        }
        .stock-value.low-stock {
            color: #dc3545;
            background: rgba(220,53,69,0.1);
            border: 2px solid #dc3545;
        }
        .low-stock-warning {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            color: #dc3545;
            font-size: 12px;
            font-weight: 600;
            animation: pulse 2s infinite;
        }

        /* ── Supply details strip ── */
        .supply-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }
        .detail-item { display: flex; flex-direction: column; gap: 5px; }
        .detail-item label {
            font-size: 12px;
            color: #dee2e6;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .detail-item span { color: #dee2e6; font-weight: 500; font-size: 16px; }

        /* ── Inline stock form ── */
        .inline-stock-form {
            background: white;
            border-radius: 12px;
            padding: 24px 28px;
            margin-top: 20px;
            margin-bottom: 24px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            border: 1px solid #e9ecef;
        }

        .inline-form-row {
            display: flex;
            align-items: flex-end;
            gap: 24px;
            flex-wrap: wrap;
        }

        .inline-field { display: flex; flex-direction: column; gap: 6px; }
        .inline-label {
            font-size: 12px;
            font-weight: 700;
            color: #495057;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        /* Sizing */
        .type-field   { flex: 0 0 auto; }
        .qty-field    { flex: 0 0 130px; }
        .purpose-field { flex: 1 1 220px; }
        .submit-field { flex: 0 0 auto; }

        /* Type toggle */
        .type-toggle {
            display: flex;
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid #dee2e6;
        }
        .toggle-btn {
            padding: 10px 18px;
            border: none;
            background: #f8f9fa;
            color: #6c757d;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .toggle-btn:first-child { border-right: 1px solid #dee2e6; }
        .toggle-btn.active {
            background: #296218;
            color: white;
        }
        .toggle-btn:disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }

        /* Inputs */
        .inline-field .form-input {
            background: #fafafa;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 14px;
            transition: all 0.25s ease;
            height: 44px;
            box-sizing: border-box;
        }
        .inline-field .form-input:focus {
            background: white;
            border-color: #296218;
            box-shadow: 0 0 0 3px rgba(41,98,24,0.1);
            outline: none;
        }
        .inline-field .form-input.error {
            border-color: #dc3545;
            box-shadow: 0 0 0 3px rgba(220,53,69,0.1);
        }

        /* Submit button */
        .submit-field .btn {
            height: 44px;
            padding: 0 22px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 7px;
            transition: all 0.2s ease;
        }

        /* Warning */
        .inline-warning {
            font-size: 11px;
            color: #dc3545;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* ── Stock Card Table ── */
        .stock-card-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            font-size: 13px;
        }
        .stock-card-table thead tr:first-child th {
            background: #f8f9fa;
            padding: 12px 8px;
            text-align: center;
            font-weight: 700;
            color: #333;
            border: 1px solid #dee2e6;
            font-size: 13px;
        }
        .stock-card-table thead tr:last-child th {
            background: #f8f9fa;
            padding: 10px 8px;
            text-align: center;
            font-weight: 600;
            color: #495057;
            border: 1px solid #dee2e6;
            font-size: 12px;
        }
        .stock-card-table tbody td {
            padding: 10px 12px;
            border: 1px solid #dee2e6;
            color: #333;
            vertical-align: middle;
        }
        .stock-card-table tbody tr { background: white; transition: background-color 0.2s ease; }
        .stock-card-table tbody tr.stock-in-row { background: #ffeb3b; }
        .stock-card-table tbody tr.stock-in-row td { border-color: #fdd835; }
        .stock-card-table tbody tr:hover { background: #f8f9fa; }
        .stock-card-table tbody tr.stock-in-row:hover { background: #ffe821; }

        /* ── Header top row ── */
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        @keyframes pulse {
            0%   { opacity: 1; }
            50%  { opacity: 0.5; }
            100% { opacity: 1; }
        }

        @media print {
            .back-button, .inline-stock-form, .sidebar, .header { display: none; }
            .supplies-container { box-shadow: none; border-radius: 0; }
            .stock-card-table { page-break-inside: avoid; }
        }

        @media (max-width: 768px) {
            .supply-info-header { flex-direction: column; text-align: center; }
            .current-stock { min-width: unset; }
            .supply-details { grid-template-columns: 1fr; }
            .inline-form-row { flex-direction: column; }
            .qty-field, .purpose-field, .type-field, .submit-field { flex: 1 1 100%; }
            .submit-field .btn { width: 100%; justify-content: center; }
            .stock-card-table { font-size: 11px; }
            .stock-card-table thead th, .stock-card-table tbody td { padding: 6px 4px; }
            .header-top { flex-direction: column; align-items: flex-start; gap: 10px; }
        }

        /* ── Row Selection ── */
        .movement-row.row-selected td {
            background-color: #d0e8ff !important;
            border-color: #74b9ff !important;
        }
        .movement-row.stock-in-row.row-selected td {
            background-color: #aee4b8 !important;
            border-color: #55c278 !important;
        }

        /* ── Floating Action Bar ── */
        .row-action-bar {
            position: fixed;
            bottom: 28px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
            filter: drop-shadow(0 8px 24px rgba(0,0,0,0.22));
        }
        .row-action-inner {
            display: flex;
            align-items: center;
            gap: 16px;
            background: #1e1e2e;
            color: #fff;
            border-radius: 50px;
            padding: 10px 20px;
            font-size: 13px;
        }
        .row-action-label {
            font-size: 13px;
            color: #ced4da;
            white-space: nowrap;
            max-width: 220px;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .row-action-buttons { display: flex; gap: 8px; align-items: center; }
        .btn-row-action {
            border: none;
            border-radius: 50px;
            padding: 7px 16px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }
        .btn-row-edit   { background: #296218; color: #fff; }
        .btn-row-edit:hover { background: #1e4a12; }
        .btn-row-delete { background: #dc3545; color: #fff; }
        .btn-row-delete:hover { background: #b02a37; }
        .btn-row-deselect { background: transparent; color: #adb5bd; padding: 7px 10px; font-size: 15px; }
        .btn-row-deselect:hover { color: #fff; }

        /* ── Modals ── */
        .modal-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.55);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-card {
            background: #fff;
            border-radius: 16px;
            width: 100%;
            max-width: 460px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.25);
            overflow: hidden;
            animation: modalIn 0.2s ease;
        }
        .modal-card-sm { max-width: 380px; }
        @keyframes modalIn {
            from { transform: translateY(20px); opacity: 0; }
            to   { transform: translateY(0);    opacity: 1; }
        }
        .modal-header {
            background: #296218;
            color: #fff;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .modal-header-danger { background: #dc3545; }
        .modal-header h3 { margin: 0; font-size: 16px; font-weight: 700; display: flex; align-items: center; gap: 8px; }
        .modal-close {
            background: transparent; border: none; color: rgba(255,255,255,0.75);
            font-size: 22px; cursor: pointer; line-height: 1; padding: 0;
        }
        .modal-close:hover { color: #fff; }
        .modal-body { padding: 22px 24px; display: flex; flex-direction: column; gap: 16px; }
        .modal-field { display: flex; flex-direction: column; gap: 6px; }
        .modal-field label {
            font-size: 12px; font-weight: 700; color: #495057;
            text-transform: uppercase; letter-spacing: 0.4px;
        }
        .modal-footer {
            padding: 14px 24px;
            border-top: 1px solid #e9ecef;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .btn-secondary {
            background: #e9ecef; color: #495057; border: none;
            border-radius: 8px; padding: 9px 18px; font-size: 13px;
            font-weight: 600; cursor: pointer;
        }
        .btn-secondary:hover { background: #dee2e6; }

        /* ── Type Card Selector (Edit Modal) ── */
        .type-card-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 6px;
        }
        .type-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            padding: 14px 10px;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            background: #f8f9fa;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
        }
        .type-card:hover {
            border-color: #adb5bd;
            background: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .type-card-icon {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            transition: all 0.2s ease;
        }
        .type-card-icon--in  { background: rgba(40,167,69,0.12); color: #28a745; }
        .type-card-icon--out { background: rgba(220,53,69,0.12);  color: #dc3545; }
        .type-card-label {
            font-size: 13px;
            font-weight: 700;
            color: #343a40;
            letter-spacing: 0.2px;
        }
        .type-card-desc {
            font-size: 11px;
            color: #868e96;
        }
        /* Active states */
        .type-card--active-in {
            border-color: #28a745;
            background: rgba(40,167,69,0.06);
            box-shadow: 0 0 0 3px rgba(40,167,69,0.15);
        }
        .type-card--active-in .type-card-icon--in {
            background: #28a745;
            color: #fff;
        }
        .type-card--active-in .type-card-label { color: #1a6630; }

        .type-card--active-out {
            border-color: #dc3545;
            background: rgba(220,53,69,0.06);
            box-shadow: 0 0 0 3px rgba(220,53,69,0.15);
        }
        .type-card--active-out .type-card-icon--out {
            background: #dc3545;
            color: #fff;
        }
        .type-card--active-out .type-card-label { color: #9b1c2a; }
    </style>
</body>
</html>