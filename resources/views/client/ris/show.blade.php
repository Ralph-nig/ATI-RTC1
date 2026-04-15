{{-- filepath: resources/views/client/ris/show.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View RIS Request</title>
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

            <div class="ris-page-header">
                <a href="{{ route('client.ris.index') }}" class="ris-back-btn">
                    <i class="fas fa-arrow-left"></i>
                    @if(auth()->user()->isAdmin()) All RIS Requests @else My Requests @endif
                </a>
                <div style="display:flex;gap:8px;align-items:center">
                    @if($ris->status === 'approved')
                    <a href="{{ route('client.ris.print', $ris->id) }}" target="_blank"
                       class="ris-btn" style="background:#17a2b8;color:#fff">
                        <i class="fas fa-print"></i> Print RIS
                    </a>
                    @endif
                    @if(auth()->user()->isAdmin() && $ris->status === 'pending')
                    <button class="ris-btn ris-btn-success" onclick="approveRis()">
                        <i class="fas fa-check"></i> Approve & Issue Stock
                    </button>
                    <button class="ris-btn ris-btn-danger" onclick="openModal('rejectModal')">
                        <i class="fas fa-times"></i> Reject
                    </button>
                    @endif
                </div>
            </div>

            @if(session('success'))
                <div class="ris-alert ris-alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="ris-alert ris-alert-danger"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
            @endif

            <!-- @if(!empty($autoRejected))
            <div class="ris-alert ris-alert-auto-reject">
                <i class="fas fa-exclamation-triangle"></i>
                <div>
                    <strong>Insufficient Stock — Items Auto-Rejected:</strong>
                    {{ implode(', ', $autoRejected) }}.
                    These items did not have enough stock to fulfil the requested quantity and have been automatically marked as <strong>Rejected</strong>.
                    You may still approve the remaining items or reject the entire request.
                </div>
            </div>
            @endif -->

            <div class="ris-show-layout">

                {{-- Main card --}}
                <div class="ris-card ris-show-main">

                    <div class="ris-show-title-row">
                        <div>
                            <p class="ris-show-ref">{{ $ris->reference }}</p>
                            <h2 class="ris-show-purpose">{{ $ris->purpose }}</h2>
                        </div>
                        <span class="ris-badge ris-badge-{{ $ris->status }}">
                            <i class="fas fa-circle" style="font-size:7px"></i> {{ ucfirst($ris->status) }}
                        </span>
                    </div>

                    <div class="ris-meta-grid">
                        <div class="ris-meta-item">
                            <span class="ris-meta-label"><i class="fas fa-user"></i> Requested by</span>
                            <span class="ris-meta-value">{{ $ris->requester->name }}</span>
                        </div>
                        <div class="ris-meta-item">
                            <span class="ris-meta-label"><i class="fas fa-calendar-plus"></i> Date Filed</span>
                            <span class="ris-meta-value">{{ $ris->created_at->format('F d, Y g:i A') }}</span>
                        </div>
                        @if($ris->date_needed)
                        <div class="ris-meta-item">
                            <span class="ris-meta-label"><i class="fas fa-calendar-day"></i> Date Needed</span>
                            <span class="ris-meta-value">{{ $ris->date_needed->format('F d, Y') }}</span>
                        </div>
                        @endif
                        @if($ris->approver)
                        <div class="ris-meta-item">
                            <span class="ris-meta-label">
                                <i class="fas fa-user-check"></i>
                                {{ $ris->status === 'approved' ? 'Approved' : 'Rejected' }} by
                            </span>
                            <span class="ris-meta-value">
                                {{ $ris->approver->name }}
                                <span class="ris-meta-muted">on {{ $ris->approved_at->format('M d, Y') }}</span>
                            </span>
                        </div>
                        @endif
                    </div>

                    @if($ris->notes)
                    <div class="ris-notes-box">
                        <div class="ris-notes-label"><i class="fas fa-sticky-note"></i> Notes</div>
                        <p class="ris-notes-text">{{ $ris->notes }}</p>
                    </div>
                    @endif

                    @if($ris->status === 'rejected' && $ris->rejection_reason)
                    <div class="ris-rejection-box">
                        <div class="ris-rejection-label"><i class="fas fa-ban"></i> Rejection Reason</div>
                        <p class="ris-rejection-text">{{ $ris->rejection_reason }}</p>
                    </div>
                    @endif

                    {{-- Supplies table --}}
                    <div class="ris-supplies-section">
                        <h3 class="ris-section-title"><i class="fas fa-boxes"></i> Requested Supplies</h3>
                        <div class="ris-table-wrap">
                            <table class="ris-table">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th style="width:120px;text-align:center">Qty Requested</th>
                                        @if(auth()->user()->isAdmin())
                                        <th style="width:120px;text-align:center">In Stock</th>
                                        @endif
                                        @if($ris->status === 'approved')
                                        <th style="width:110px;text-align:center">Qty Issued</th>
                                        @endif
                                        <th style="width:100px;text-align:center">Status</th>
                                        @if(auth()->user()->isAdmin() && $ris->status === 'pending')
                                        <th style="width:100px;text-align:center">Action</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ris->supplies as $supply)
                                    @php $low = $supply->quantity < $supply->pivot->quantity_requested; @endphp
                                    <tr id="supply-row-{{ $supply->id }}">
                                        <td>
                                            <strong>{{ $supply->name }}</strong>
                                            <br><small class="ris-meta-muted">{{ $supply->category }}</small>
                                        </td>
                                        <td style="text-align:center">
                                            <strong style="color:#296218">{{ $supply->pivot->quantity_requested }} {{ $supply->unit }}</strong>
                                        </td>
                                        @if(auth()->user()->isAdmin())
                                        <td style="text-align:center">
                                            <span class="{{ $low ? 'ris-text-danger' : 'ris-text-success' }}">
                                                {{ $supply->quantity }} {{ $supply->unit }}
                                            </span>
                                        </td>
                                        @endif
                                        @if($ris->status === 'approved')
                                        <td style="text-align:center">
                                            <strong>{{ $supply->pivot->quantity_issued ?? '—' }} {{ $supply->unit }}</strong>
                                        </td>
                                        @endif
                                        <td style="text-align:center">
                                            <span class="ris-badge ris-badge-{{ $supply->pivot->status }}" id="supply-status-{{ $supply->id }}">
                                                {{ ucfirst($supply->pivot->status) }}
                                            </span>
                                        </td>
                                        @if(auth()->user()->isAdmin() && $ris->status === 'pending')
                                        <td style="text-align:center">
                                            @if($supply->pivot->status === 'pending')
                                            <div style="display:flex;gap:6px;justify-content:center;" id="supply-actions-{{ $supply->id }}">
                                                <button class="ris-item-btn ris-item-approve"
                                                        onclick="approveItem({{ $ris->id }}, {{ $supply->id }})"
                                                        title="Approve this item">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button class="ris-item-btn ris-item-reject"
                                                        onclick="rejectItem({{ $ris->id }}, {{ $supply->id }})"
                                                        title="Reject this item">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            @else
                                            <span style="font-size:12px;color:#6c757d;">—</span>
                                            @endif
                                        </td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- @if($ris->status === 'pending')
                        <div class="ris-stock-banner {{ $ris->hasAvailableStock() ? 'ris-stock-ok' : 'ris-stock-warn' }}">
                            @if($ris->hasAvailableStock())
                                <i class="fas fa-check-circle"></i>
                                <span>All requested supplies are currently in stock.</span>
                            @else
                                <i class="fas fa-exclamation-triangle"></i>
                                <span>Some items have insufficient stock. Approval may be delayed.</span>
                            @endif
                        </div>
                        @endif -->

                        <!-- {{-- Admin: approve/reject all remaining + finalize --}}
                        @if(auth()->user()->isAdmin() && $ris->status === 'pending')
                        <div class="ris-bulk-actions">
                            <button class="ris-btn" style="background:#e9f5e9;color:#296218;border:1px solid #c3e6cb;"
                                    onclick="approveAllItems({{ $ris->id }})">
                                <i class="fas fa-check-double"></i> Approve All
                            </button>
                            <button class="ris-btn" style="background:#fdf0f0;color:#dc3545;border:1px solid #f5c6cb;"
                                    onclick="rejectAllItems({{ $ris->id }})">
                                <i class="fas fa-times"></i> Reject All
                            </button>
                            <button class="ris-btn ris-btn-success" id="finalizeBtn" onclick="finalizeRis({{ $ris->id }})" style="display:none;">
                                <i class="fas fa-paper-plane"></i> Finalize & Issue Stock
                            </button>
                        </div>
                        @endif -->
                    </div>

                </div>

                {{-- Status sidebar --}}
                <div class="ris-show-sidebar">
                    <div class="ris-status-card">
                        <div class="ris-status-head">Request Progress</div>
                        <div class="ris-status-body">
                            <div class="ris-status-step done">
                                <div class="ris-step-dot"></div>
                                <div>
                                    <p class="ris-step-label">Submitted</p>
                                    <p class="ris-step-date">{{ $ris->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                            <div class="ris-status-line"></div>
                            <div class="ris-status-step {{ $ris->status === 'approved' ? 'done' : ($ris->status === 'rejected' ? 'rejected' : 'pending-step') }}">
                                <div class="ris-step-dot"></div>
                                <div>
                                    <p class="ris-step-label">
                                        @if($ris->status === 'approved') Approved
                                        @elseif($ris->status === 'rejected') Rejected
                                        @else Under Review
                                        @endif
                                    </p>
                                    @if($ris->approved_at)
                                    <p class="ris-step-date">{{ $ris->approved_at->format('M d, Y') }}</p>
                                    @endif
                                </div>
                            </div>
                            @if($ris->status === 'approved')
                            <div class="ris-status-line"></div>
                            <div class="ris-status-step done">
                                <div class="ris-step-dot"></div>
                                <div>
                                    <p class="ris-step-label">Supplies Issued</p>
                                    <p class="ris-step-date">{{ $ris->approved_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Reject modal --}}
@if(auth()->user()->isAdmin())
<div id="rejectModal" class="ris-modal">
    <div class="ris-modal-box">
        <div class="ris-modal-head ris-modal-danger">
            <i class="fas fa-times-circle"></i><h3>Reject Request</h3>
        </div>
        <div class="ris-modal-body">
            <p>Provide a reason for rejecting this request (optional):</p>
            <textarea id="rejectReason" class="ris-input ris-textarea" rows="3"
                      placeholder="e.g. Item not available, please re-submit next quarter."></textarea>
        </div>
        <div class="ris-modal-foot">
            <button class="ris-btn ris-btn-secondary" onclick="closeModal('rejectModal')">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button class="ris-btn ris-btn-danger" onclick="proceedReject()">
                <i class="fas fa-ban"></i> Reject
            </button>
        </div>
    </div>
</div>
@endif

@include('layouts.core.footer')

<style>
.ris-wrap{padding:24px;display:flex;flex-direction:column;gap:16px}
.ris-page-header{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px}
.ris-back-btn{display:inline-flex;align-items:center;gap:8px;color:#296218;font-size:14px;font-weight:500;text-decoration:none;transition:gap .15s}
.ris-back-btn:hover{gap:13px}
.ris-alert{display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:8px;font-size:14px;font-weight:500}
.ris-alert-success{background:#d4edda;border:1px solid #c3e6cb;color:#155724}
.ris-alert-danger{background:#f8d7da;border:1px solid #f5c6cb;color:#721c24}
.ris-alert-auto-reject{background:#fff3cd;border:1px solid #ffc107;color:#856404;align-items:flex-start;gap:12px;line-height:1.6}
.ris-show-layout{display:grid;grid-template-columns:1fr 230px;gap:20px;align-items:start}
.ris-card{background:#fff;border:1px solid #e9ecef;border-radius:12px;overflow:hidden}
.ris-show-main{padding:26px;display:flex;flex-direction:column;gap:22px}
.ris-show-title-row{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;padding-bottom:18px;border-bottom:1.5px solid #e9ecef}
.ris-show-ref{font-size:12px;color:#296218;font-family:monospace;font-weight:600;margin:0 0 4px;text-transform:uppercase;letter-spacing:.5px}
.ris-show-purpose{font-size:22px;font-weight:600;color:#212529;margin:0}
.ris-meta-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px}
.ris-meta-item{display:flex;flex-direction:column;gap:4px}
.ris-meta-label{display:flex;align-items:center;gap:6px;font-size:12px;color:#6c757d;font-weight:600}
.ris-meta-value{font-size:14px;color:#495057}
.ris-meta-muted{color:#6c757d;font-size:12px}
.ris-notes-box{background:#f8f9fa;border-left:4px solid #296218;border-radius:0 8px 8px 0;padding:14px 18px}
.ris-notes-label{display:flex;align-items:center;gap:7px;font-weight:600;color:#495057;margin-bottom:8px;font-size:13px}
.ris-notes-text{margin:0;color:#495057;font-size:14px;line-height:1.7}
.ris-rejection-box{background:#fff5f5;border-left:4px solid #dc3545;border-radius:0 8px 8px 0;padding:14px 18px}
.ris-rejection-label{display:flex;align-items:center;gap:7px;font-weight:600;color:#dc3545;margin-bottom:8px;font-size:13px}
.ris-rejection-text{margin:0;color:#721c24;font-size:14px;line-height:1.7}
.ris-section-title{font-size:16px;font-weight:600;color:#296218;margin:0;display:flex;align-items:center;gap:8px}
.ris-supplies-section{display:flex;flex-direction:column;gap:14px}
.ris-table-wrap{overflow-x:auto;border:1px solid #e9ecef;border-radius:8px}
.ris-table{width:100%;border-collapse:collapse;font-size:14px}
.ris-table thead tr{background:#296218}
.ris-table thead th{padding:11px 14px;color:#fff;font-weight:500;text-align:left;white-space:nowrap}
.ris-table tbody tr{border-bottom:1px solid #f0f0f0}
.ris-table tbody tr:last-child{border-bottom:none}
.ris-table td{padding:11px 14px;color:#495057;vertical-align:middle}
.ris-badge{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.3px}
.ris-badge-pending{background:#fff3cd;color:#856404;border:1px solid #ffeeba}
.ris-badge-approved{background:#d4edda;color:#155724;border:1px solid #c3e6cb}
.ris-badge-rejected{background:#f8d7da;color:#721c24;border:1px solid #f5c6cb}
.ris-stock-banner{display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:8px;font-weight:500;font-size:14px}
.ris-stock-ok{background:#d4edda;border:1px solid #c3e6cb;color:#155724}
.ris-stock-warn{background:#fff3cd;border:1px solid #ffeeba;color:#856404}
.ris-text-danger{color:#dc3545!important;font-weight:600}
.ris-text-success{color:#28a745!important;font-weight:600}
.ris-btn{display:inline-flex;align-items:center;gap:6px;padding:0 16px;height:38px;border-radius:8px;font-size:14px;font-weight:500;cursor:pointer;border:none;text-decoration:none;white-space:nowrap;transition:filter .15s}
.ris-btn:hover{filter:brightness(.9)}
.ris-btn-secondary{background:#6c757d;color:#fff}
.ris-btn-success{background:#28a745;color:#fff}
.ris-btn-danger{background:#dc3545;color:#fff}
.ris-show-sidebar{position:sticky;top:20px}
.ris-status-card{background:#fff;border:1px solid #e9ecef;border-radius:12px;overflow:hidden}
.ris-status-head{background:#296218;color:#fff;padding:14px 18px;font-size:14px;font-weight:600}
.ris-status-body{padding:18px;display:flex;flex-direction:column}
.ris-status-step{display:flex;align-items:flex-start;gap:12px}
.ris-step-dot{width:14px;height:14px;border-radius:50%;background:#dee2e6;border:2px solid #dee2e6;flex-shrink:0;margin-top:2px}
.ris-status-step.done .ris-step-dot{background:#296218;border-color:#296218}
.ris-status-step.rejected .ris-step-dot{background:#dc3545;border-color:#dc3545}
.ris-status-step.pending-step .ris-step-dot{background:#ffc107;border-color:#ffc107;animation:pulse-dot 1.4s ease-in-out infinite}
@keyframes pulse-dot{0%,100%{opacity:1}50%{opacity:.4}}
.ris-status-line{width:2px;background:#dee2e6;height:22px;margin-left:6px}
.ris-step-label{font-size:13px;font-weight:600;color:#495057;margin:0 0 2px}
.ris-step-date{font-size:11px;color:#6c757d;margin:0}
.ris-input{width:100%;box-sizing:border-box;padding:9px 14px;border:1px solid #dee2e6;border-radius:8px;font-size:14px;color:#495057;background:#fff;outline:none;transition:border-color .15s}
.ris-input:focus{border-color:#296218;box-shadow:0 0 0 3px rgba(41,98,24,.1)}
.ris-textarea{resize:vertical;min-height:80px}
.ris-modal{display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:9999;align-items:center;justify-content:center}
.ris-modal.active{display:flex}
.ris-modal-box{background:#fff;border-radius:14px;width:90%;max-width:440px;overflow:hidden;animation:ris-modal-in .25s ease}
@keyframes ris-modal-in{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:none}}
.ris-modal-head{padding:20px 22px;color:#fff;display:flex;align-items:center;gap:12px}
.ris-modal-head i{font-size:26px}
.ris-modal-head h3{margin:0;font-size:18px;font-weight:600}
.ris-modal-danger{background:#dc3545}
.ris-modal-body{padding:22px}
.ris-modal-body p{margin:0 0 12px;color:#495057;font-size:14px;line-height:1.6}
.ris-modal-foot{padding:14px 22px;background:#f8f9fa;display:flex;justify-content:flex-end;gap:8px;border-top:1px solid #e9ecef}
.ris-item-btn{width:30px;height:30px;border-radius:6px;border:none;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;font-size:13px;transition:filter .15s,transform .1s}
.ris-item-btn:hover{filter:brightness(.88);transform:scale(1.1)}
.ris-item-btn:active{transform:scale(.95)}
.ris-item-approve{background:#d4edda;color:#155724;}
.ris-item-reject{background:#f8d7da;color:#721c24;}
.ris-bulk-actions{display:flex;gap:10px;flex-wrap:wrap;align-items:center;padding-top:14px;border-top:1px solid #e9ecef;margin-top:4px}
@media(max-width:768px){.ris-show-layout{grid-template-columns:1fr}.ris-show-sidebar{position:static}}
</style>

<script>
const csrfToken = document.querySelector('meta[name=csrf-token]')?.content;

function openModal(id)  { document.getElementById(id)?.classList.add('active');    document.body.style.overflow='hidden'; }
function closeModal(id) { document.getElementById(id)?.classList.remove('active'); document.body.style.overflow=''; }
document.querySelectorAll('.ris-modal').forEach(m => m.addEventListener('click', e => { if(e.target===m) closeModal(m.id); }));
document.addEventListener('keydown', e => { if(e.key==='Escape') document.querySelectorAll('.ris-modal.active').forEach(m=>closeModal(m.id)); });

function approveRis() {
    // If any items have already been individually actioned (approved/rejected),
    // route through finalize() so we don't re-process them.
    const hasActionedItems = document.querySelectorAll('[id^="supply-actions-"]').length > 0
        && document.querySelectorAll('.ris-item-approve, .ris-item-reject').length === 0;

    if (hasActionedItems) {
        // All items already individually actioned — use finalize route
        finalizeRis({{ $ris->id }});
        return;
    }

    // Check if any items were individually rejected but not all actioned yet
    const rejectedBadges = document.querySelectorAll('.ris-badge-rejected');
    const pendingActions = document.querySelectorAll('.ris-item-approve, .ris-item-reject');
    if (rejectedBadges.length > 0 && pendingActions.length > 0) {
        alert('Some items are marked as rejected but others are still pending. Please action all items first, then use "Finalize & Issue Stock".');
        return;
    }

    const msg = rejectedBadges.length > 0
        ? `Approve this RIS? Note: ${rejectedBadges.length} item(s) already marked as rejected will be skipped. Only the remaining items will have stock deducted.`
        : 'Approve this RIS and deduct stock from inventory?';

    if (!confirm(msg)) return;
    fetch('/client/ris/{{ $ris->id }}/approve', {
        method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken}
    })
    .then(r=>r.json())
    .then(d=>{ d.success ? location.reload() : alert('Error: '+d.message); })
    .catch(()=>alert('Request failed.'));
}

function proceedReject() {
    const reason = document.getElementById('rejectReason')?.value || '';
    fetch('/client/ris/{{ $ris->id }}/reject', {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken},
        body:JSON.stringify({reason})
    })
    .then(r=>r.json())
    .then(d=>{ closeModal('rejectModal'); d.success ? location.reload() : alert('Error: '+d.message); })
    .catch(()=>alert('Request failed.'));
}

function approveItem(risId, supplyId) {
    sendItemAction(risId, supplyId, 'approve');
}

function rejectItem(risId, supplyId) {
    sendItemAction(risId, supplyId, 'reject');
}

function sendItemAction(risId, supplyId, action) {
    fetch(`/client/ris/${risId}/item/${supplyId}/${action}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }
    })
    .then(r => r.json())
    .then(d => {
        if (!d.success) { alert('Error: ' + d.message); return; }

        // Update badge
        const badge = document.getElementById('supply-status-' + supplyId);
        if (badge) {
            badge.className = 'ris-badge ris-badge-' + (action === 'approve' ? 'approved' : 'rejected');
            badge.innerHTML = action === 'approve'
                ? '<i class="fas fa-circle" style="font-size:7px"></i> Approved'
                : '<i class="fas fa-circle" style="font-size:7px"></i> Rejected';
        }

        // Hide action buttons for this row
        const actions = document.getElementById('supply-actions-' + supplyId);
        if (actions) actions.innerHTML = '<span style="font-size:12px;color:#6c757d;">Done</span>';

        // Show finalize button if all items actioned
        checkAllActioned();
    })
    .catch(() => alert('Request failed.'));
}

function approveAllItems(risId) {
    document.querySelectorAll('[id^="supply-actions-"]').forEach(el => {
        const supplyId = el.id.replace('supply-actions-', '');
        if (el.querySelector('.ris-item-approve')) {
            sendItemAction(risId, supplyId, 'approve');
        }
    });
}

function rejectAllItems(risId) {
    document.querySelectorAll('[id^="supply-actions-"]').forEach(el => {
        const supplyId = el.id.replace('supply-actions-', '');
        if (el.querySelector('.ris-item-reject')) {
            sendItemAction(risId, supplyId, 'reject');
        }
    });
}

function checkAllActioned() {
    const pending = document.querySelectorAll('.ris-item-approve, .ris-item-reject');
    const finalizeBtn = document.getElementById('finalizeBtn');
    if (finalizeBtn && pending.length === 0) {
        finalizeBtn.style.display = 'inline-flex';
    }
}

function finalizeRis(risId) {
    if (!confirm('Finalize this RIS? Approved items will have stock deducted immediately.')) return;
    fetch(`/client/ris/${risId}/finalize`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }
    })
    .then(r => r.json())
    .then(d => { d.success ? location.reload() : alert('Error: ' + d.message); })
    .catch(() => alert('Request failed.'));
}

setTimeout(()=>{
    document.querySelectorAll('.ris-alert').forEach(el=>{
        el.style.transition='opacity .4s'; el.style.opacity='0';
        setTimeout(()=>el.remove(),400);
    });
},5000);
</script>
</body>
</html>