<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Help & Request Details</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/help.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container">
    @include('layouts.core.sidebar')
    <div class="details">
        @include('layouts.core.header')

        <div class="hr-wrap">

            <div class="hr-page-header">
                <a href="{{ route('client.help.index') }}" class="hr-back-btn">
                    <i class="fas fa-arrow-left"></i>
                    @if(auth()->user()->isAdmin()) All Requests @else My Requests @endif
                </a>
                <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                    @if(auth()->user()->isAdmin() || ($helpRequest->user_id === auth()->id() && $helpRequest->status === 'pending'))
                        <a href="{{ route('client.help.edit', $helpRequest->id) }}" class="hr-btn hr-btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    @endif
                    @if(auth()->user()->isAdmin() && $helpRequest->status === 'pending')
                        <button class="hr-btn hr-btn-success" onclick="openApproveModal()">
                            <i class="fas fa-check"></i> Approve
                        </button>
                        <button class="hr-btn hr-btn-danger" onclick="openRejectModal()">
                            <i class="fas fa-times"></i> Reject
                        </button>
                    @endif
                </div>
            </div>

            @if(session('success'))
                <div class="hr-alert hr-alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
            @endif

            <div class="hr-show-layout">

                {{-- Main content --}}
                <div class="hr-show-main">

                    {{-- Title row --}}
                    <div class="hr-show-title-row">
                        <div>
                            <div class="hr-show-badges">
                                <span class="hr-priority hr-priority-{{ $helpRequest->priority }}">
                                    <i class="fas fa-flag"></i> {{ ucfirst($helpRequest->priority) }} Priority
                                </span>
                                <span class="hr-status hr-status-{{ $helpRequest->status }}">
                                    <i class="fas fa-circle" style="font-size:7px"></i>
                                    {{ ucfirst(str_replace('_', ' ', $helpRequest->status)) }}
                                </span>
                            </div>
                            <h2 class="hr-show-subject">{{ $helpRequest->subject }}</h2>
                        </div>
                    </div>

                    {{-- Meta --}}
                    <div class="hr-meta-grid">
                        <div class="hr-meta-item">
                            <span class="hr-meta-label"><i class="fas fa-user"></i> Submitted by</span>
                            <div style="display:flex;align-items:center;gap:8px;margin-top:4px;">
                                <img src="{{ $helpRequest->user->avatar_url }}" alt=""
                                     style="width:28px;height:28px;border-radius:50%;object-fit:cover;border:2px solid #e9ecef;">
                                <span class="hr-meta-value">{{ $helpRequest->user->name }}</span>
                            </div>
                        </div>
                        <div class="hr-meta-item">
                            <span class="hr-meta-label"><i class="fas fa-calendar-plus"></i> Date Filed</span>
                            <span class="hr-meta-value">{{ $helpRequest->created_date }}</span>
                        </div>
                        @if($helpRequest->updated_at != $helpRequest->created_at)
                        <div class="hr-meta-item">
                            <span class="hr-meta-label"><i class="fas fa-clock"></i> Last Updated</span>
                            <span class="hr-meta-value">{{ $helpRequest->updated_date }}</span>
                        </div>
                        @endif
                        @if($helpRequest->assignedTo)
                        <div class="hr-meta-item">
                            <span class="hr-meta-label"><i class="fas fa-user-check"></i> Assigned To</span>
                            <span class="hr-meta-value">{{ $helpRequest->assignedTo->name }}</span>
                        </div>
                        @endif
                    </div>

                    {{-- Description --}}
                    <div class="hr-section">
                        <div class="hr-section-label"><i class="fas fa-file-alt"></i> Description</div>
                        <div class="hr-section-body">{{ $helpRequest->description }}</div>
                    </div>

                    {{-- Admin response (if any) --}}
                    @if($helpRequest->admin_response)
                    <div class="hr-section hr-section-response">
                        <div class="hr-section-label">
                            <i class="fas fa-reply"></i>
                            @if($helpRequest->status === 'closed') Rejection Reason @else Admin Response @endif
                        </div>
                        <div class="hr-section-body">{{ $helpRequest->admin_response }}</div>
                        @if($helpRequest->resolved_at)
                        <div style="font-size:12px;color:#6c757d;margin-top:8px;">
                            <i class="fas fa-check"></i> {{ $helpRequest->status === 'closed' ? 'Closed' : 'Resolved' }}
                            on {{ $helpRequest->resolved_at->format('F d, Y h:i A') }}
                        </div>
                        @endif
                    </div>
                    @endif

                </div>

                {{-- Sidebar: status timeline + admin action panel --}}
                <div class="hr-show-sidebar">

                    {{-- Status timeline --}}
                    <div class="hr-status-card">
                        <div class="hr-status-head"><i class="fas fa-stream"></i> Request Status</div>
                        <div class="hr-status-body">
                            @php
                                $steps = [
                                    ['key' => 'submitted', 'label' => 'Submitted',   'icon' => 'fa-paper-plane', 'date' => $helpRequest->created_at->format('M d, Y')],
                                    ['key' => 'reviewing', 'label' => 'Under Review','icon' => 'fa-search',       'date' => null],
                                    ['key' => 'actioned',  'label' => $helpRequest->status === 'closed' ? 'Rejected' : 'Approved / In Progress', 'icon' => $helpRequest->status === 'closed' ? 'fa-times' : 'fa-check', 'date' => $helpRequest->approved_at?->format('M d, Y') ?? null],
                                    ['key' => 'resolved',  'label' => 'Resolved',   'icon' => 'fa-check-double', 'date' => $helpRequest->resolved_at?->format('M d, Y') ?? null],
                                ];
                                $currentStep = match($helpRequest->status) {
                                    'pending'     => 1,
                                    'in_progress' => 2,
                                    'resolved'    => 3,
                                    'closed'      => 3,
                                    default       => 1,
                                };
                            @endphp
                            @foreach($steps as $i => $step)
                            <div class="hr-step {{ $i <= $currentStep ? ($helpRequest->status === 'closed' && $i === $currentStep ? 'hr-step-rejected' : 'hr-step-done') : '' }}">
                                <div class="hr-step-left">
                                    <div class="hr-step-dot">
                                        <i class="fas {{ $step['icon'] }}" style="font-size:9px;"></i>
                                    </div>
                                    @if(!$loop->last)<div class="hr-step-line"></div>@endif
                                </div>
                                <div class="hr-step-content">
                                    <div class="hr-step-label">{{ $step['label'] }}</div>
                                    @if($step['date'])
                                        <div class="hr-step-date">{{ $step['date'] }}</div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Admin quick-action panel --}}
                    @if(auth()->user()->isAdmin() && $helpRequest->status === 'pending')
                    <div class="hr-action-panel">
                        <div class="hr-action-panel-head">
                            <i class="fas fa-gavel"></i> Take Action
                        </div>
                        <div class="hr-action-panel-body">
                            <p style="font-size:13px;color:#6c757d;margin:0 0 14px;">
                                This request is awaiting your decision.
                            </p>
                            <button class="hr-btn hr-btn-success hr-btn-full" onclick="openApproveModal()">
                                <i class="fas fa-check"></i> Approve Request
                            </button>
                            <button class="hr-btn hr-btn-danger hr-btn-full" style="margin-top:8px;" onclick="openRejectModal()">
                                <i class="fas fa-times"></i> Reject Request
                            </button>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

{{-- Approve Modal --}}
<div class="hr-modal" id="approveModal">
    <div class="hr-modal-box">
        <div class="hr-modal-head hr-modal-success">
            <i class="fas fa-check-circle"></i><h3>Approve Request</h3>
        </div>
        <div class="hr-modal-body">
            <p style="font-size:14px;color:#495057;margin-bottom:12px;">
                Approving <strong>"{{ $helpRequest->subject }}"</strong> will notify the requestor.
            </p>
            <label style="font-size:13px;font-weight:600;color:#495057;display:block;margin-bottom:6px;">
                Response / Notes <span style="font-weight:400;color:#6c757d;">(optional)</span>
            </label>
            <textarea id="approveResponse" rows="4" class="hr-textarea"
                      placeholder="Add a response or action plan..."></textarea>
        </div>
        <div class="hr-modal-foot">
            <button class="hr-btn hr-btn-ghost" onclick="closeModal('approveModal')">Cancel</button>
            <button class="hr-btn hr-btn-success" onclick="confirmApprove()">
                <i class="fas fa-check"></i> Confirm Approve
            </button>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div class="hr-modal" id="rejectModal">
    <div class="hr-modal-box">
        <div class="hr-modal-head hr-modal-danger">
            <i class="fas fa-times-circle"></i><h3>Reject Request</h3>
        </div>
        <div class="hr-modal-body">
            <p style="font-size:14px;color:#495057;margin-bottom:12px;">
                Rejecting <strong>"{{ $helpRequest->subject }}"</strong> will notify the requestor.
            </p>
            <label style="font-size:13px;font-weight:600;color:#495057;display:block;margin-bottom:6px;">
                Reason for rejection <span style="font-weight:400;color:#6c757d;">(optional)</span>
            </label>
            <textarea id="rejectReason" rows="4" class="hr-textarea"
                      placeholder="Provide a reason..."></textarea>
        </div>
        <div class="hr-modal-foot">
            <button class="hr-btn hr-btn-ghost" onclick="closeModal('rejectModal')">Cancel</button>
            <button class="hr-btn hr-btn-danger" onclick="confirmReject()">
                <i class="fas fa-times"></i> Confirm Reject
            </button>
        </div>
    </div>
</div>

@include('layouts.core.footer')

<style>
.hr-wrap{padding:24px;display:flex;flex-direction:column;gap:16px}
.hr-page-header{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px}
.hr-back-btn{display:inline-flex;align-items:center;gap:8px;color:#296218;font-size:14px;font-weight:500;text-decoration:none;transition:gap .15s}
.hr-back-btn:hover{gap:13px}
.hr-alert{display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:8px;font-size:14px;font-weight:500}
.hr-alert-success{background:#d4edda;border:1px solid #c3e6cb;color:#155724}

/* Layout */
.hr-show-layout{display:grid;grid-template-columns:1fr 240px;gap:20px;align-items:start}

/* Main card */
.hr-show-main{background:#fff;border:1px solid #e9ecef;border-radius:12px;padding:26px;display:flex;flex-direction:column;gap:22px}
.hr-show-title-row{padding-bottom:18px;border-bottom:1.5px solid #e9ecef}
.hr-show-badges{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:10px}
.hr-show-subject{font-size:22px;font-weight:600;color:#212529;margin:0}

/* Priority & Status badges */
.hr-priority{display:inline-flex;align-items:center;gap:4px;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.3px}
.hr-priority-high{background:#f8d7da;color:#721c24;border:1px solid #f5c6cb}
.hr-priority-medium{background:#fff3cd;color:#856404;border:1px solid #ffeeba}
.hr-priority-low{background:#d4edda;color:#155724;border:1px solid #c3e6cb}
.hr-status{display:inline-flex;align-items:center;gap:4px;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.3px}
.hr-status-pending{background:#fff3cd;color:#856404;border:1px solid #ffeeba}
.hr-status-in_progress{background:#cce5ff;color:#004085;border:1px solid #b8daff}
.hr-status-resolved{background:#d4edda;color:#155724;border:1px solid #c3e6cb}
.hr-status-closed{background:#e2e3e5;color:#383d41;border:1px solid #d6d8db}

/* Meta grid */
.hr-meta-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:14px}
.hr-meta-item{display:flex;flex-direction:column;gap:3px}
.hr-meta-label{font-size:11px;color:#6c757d;font-weight:700;text-transform:uppercase;letter-spacing:.5px;display:flex;align-items:center;gap:5px}
.hr-meta-value{font-size:14px;color:#495057;font-weight:500}

/* Sections */
.hr-section{display:flex;flex-direction:column;gap:8px}
.hr-section-label{font-size:13px;font-weight:700;color:#495057;display:flex;align-items:center;gap:7px;text-transform:uppercase;letter-spacing:.5px}
.hr-section-body{font-size:14px;color:#495057;line-height:1.7;background:#f8f9fa;border-radius:8px;padding:14px 16px;white-space:pre-wrap}
.hr-section-response .hr-section-body{background:#f0f8f0;border-left:4px solid #28a745}
.hr-section-response .hr-section-label{color:#28a745}

/* Buttons */
.hr-btn{display:inline-flex;align-items:center;gap:6px;padding:0 14px;height:36px;border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;border:none;text-decoration:none;white-space:nowrap;transition:filter .15s}
.hr-btn:hover{filter:brightness(.9)}
.hr-btn-full{width:100%;justify-content:center}
.hr-btn-success{background:#28a745;color:#fff}
.hr-btn-danger{background:#dc3545;color:#fff}
.hr-btn-warning{background:#ffc107;color:#212529}
.hr-btn-ghost{background:#f8f9fa;color:#6c757d;border:1px solid #dee2e6}

/* Sidebar */
.hr-show-sidebar{display:flex;flex-direction:column;gap:16px;position:sticky;top:20px}

/* Status timeline */
.hr-status-card{background:#fff;border:1px solid #e9ecef;border-radius:12px;overflow:hidden}
.hr-status-head{background:#296218;color:#fff;padding:14px 18px;font-size:14px;font-weight:600;display:flex;align-items:center;gap:8px}
.hr-status-body{padding:18px;display:flex;flex-direction:column}
.hr-step{display:flex;gap:12px;align-items:flex-start}
.hr-step-left{display:flex;flex-direction:column;align-items:center;width:22px;flex-shrink:0}
.hr-step-dot{width:22px;height:22px;border-radius:50%;background:#dee2e6;border:2px solid #dee2e6;display:flex;align-items:center;justify-content:center;color:#adb5bd;flex-shrink:0;transition:all .2s}
.hr-step-line{width:2px;background:#dee2e6;flex:1;min-height:22px;margin:2px 0}
.hr-step-done .hr-step-dot{background:#296218;border-color:#296218;color:#fff}
.hr-step-done .hr-step-line{background:#296218}
.hr-step-rejected .hr-step-dot{background:#dc3545;border-color:#dc3545;color:#fff}
.hr-step-content{padding-bottom:18px}
.hr-step-label{font-size:13px;font-weight:600;color:#495057;margin-bottom:2px}
.hr-step-date{font-size:11px;color:#6c757d}

/* Action panel */
.hr-action-panel{background:#fff;border:1px solid #ffc107;border-radius:12px;overflow:hidden}
.hr-action-panel-head{background:#ffc107;color:#212529;padding:14px 18px;font-size:14px;font-weight:600;display:flex;align-items:center;gap:8px}
.hr-action-panel-body{padding:18px}

/* Modal */
.hr-modal{display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:9999;align-items:center;justify-content:center;padding:16px}
.hr-modal.active{display:flex}
.hr-modal-box{background:#fff;border-radius:14px;width:100%;max-width:460px;overflow:hidden;animation:hrModalIn .22s ease}
@keyframes hrModalIn{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:none}}
.hr-modal-head{padding:20px 22px;color:#fff;display:flex;align-items:center;gap:12px}
.hr-modal-head i{font-size:22px}
.hr-modal-head h3{margin:0;font-size:17px;font-weight:600}
.hr-modal-success{background:#28a745}
.hr-modal-danger{background:#dc3545}
.hr-modal-body{padding:22px}
.hr-modal-foot{padding:14px 22px;background:#f8f9fa;display:flex;justify-content:flex-end;gap:8px;border-top:1px solid #e9ecef}
.hr-textarea{width:100%;box-sizing:border-box;padding:10px 14px;border:1px solid #dee2e6;border-radius:8px;font-size:14px;color:#495057;resize:vertical;min-height:90px;outline:none;font-family:inherit}
.hr-textarea:focus{border-color:#296218;box-shadow:0 0 0 3px rgba(41,98,24,.1)}

@media(max-width:768px){.hr-show-layout{grid-template-columns:1fr}.hr-show-sidebar{position:static}}
</style>

<script>
function openApproveModal() {
    document.getElementById('approveResponse').value = '';
    document.getElementById('approveModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function openRejectModal() {
    document.getElementById('rejectReason').value = '';
    document.getElementById('rejectModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeModal(id) {
    document.getElementById(id).classList.remove('active');
    document.body.style.overflow = '';
}

document.querySelectorAll('.hr-modal').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) closeModal(m.id); });
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') document.querySelectorAll('.hr-modal.active').forEach(m => closeModal(m.id));
});

function confirmApprove() {
    $.ajax({
        url: '/client/help/{{ $helpRequest->id }}/approve',
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').val() },
        data: { admin_response: document.getElementById('approveResponse').value },
        success: function(res) {
            if (res.success) { closeModal('approveModal'); location.reload(); }
            else alert(res.message);
        },
        error: function(xhr) { alert(xhr.responseJSON?.message || 'Failed to approve.'); }
    });
}

function confirmReject() {
    $.ajax({
        url: '/client/help/{{ $helpRequest->id }}/reject',
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').val() },
        data: { admin_response: document.getElementById('rejectReason').value },
        success: function(res) {
            if (res.success) { closeModal('rejectModal'); location.reload(); }
            else alert(res.message);
        },
        error: function(xhr) { alert(xhr.responseJSON?.message || 'Failed to reject.'); }
    });
}
</script>
</body>
</html>