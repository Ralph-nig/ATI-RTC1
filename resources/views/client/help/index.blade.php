<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Help & Request</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/supplies.css') }}">
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
                <h1 class="hr-page-title">
                    <i class="fas fa-hands-helping"></i>
                    @if(auth()->user()->isAdmin()) All Help & Requests @else My Help & Requests @endif
                </h1>
                @if(!auth()->user()->isAdmin())
                    <a href="{{ route('client.help.create') }}" class="hr-btn hr-btn-primary">
                        <i class="fas fa-plus"></i> New Request
                    </a>
                @endif
            </div>

            @if(session('success'))
                <div class="hr-alert hr-alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="hr-alert hr-alert-danger"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
            @endif

            {{-- Filter bar --}}
            <div class="hr-filter-bar">
                <form method="GET" class="hr-filter-form" id="filterForm">
                    <div class="hr-search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" placeholder="Search by subject…" value="{{ request('search') }}"
                               onchange="document.getElementById('filterForm').submit()">
                    </div>
                    <select name="status" class="hr-select" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="pending"     {{ request('status')==='pending'     ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ request('status')==='in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="resolved"    {{ request('status')==='resolved'    ? 'selected' : '' }}>Resolved</option>
                        <option value="closed"      {{ request('status')==='closed'      ? 'selected' : '' }}>Closed</option>
                    </select>
                    <select name="priority" class="hr-select" onchange="this.form.submit()">
                        <option value="">All Priority</option>
                        <option value="high"   {{ request('priority')==='high'   ? 'selected' : '' }}>High</option>
                        <option value="medium" {{ request('priority')==='medium' ? 'selected' : '' }}>Medium</option>
                        <option value="low"    {{ request('priority')==='low'    ? 'selected' : '' }}>Low</option>
                    </select>
                </form>
            </div>

            {{-- Cards --}}
            <div class="hr-list">
                @forelse($helpRequests as $req)
                <div class="hr-card {{ $req->status === 'pending' && auth()->user()->isAdmin() ? 'hr-card-action' : '' }}">

                    <div class="hr-card-top">
                        <div class="hr-card-meta">
                            <div class="hr-card-badges">
                                <span class="hr-priority hr-priority-{{ $req->priority }}">
                                    <i class="fas fa-flag"></i> {{ ucfirst($req->priority) }}
                                </span>
                                <span class="hr-status hr-status-{{ $req->status }}">
                                    <i class="fas fa-circle" style="font-size:7px"></i>
                                    {{ ucfirst(str_replace('_', ' ', $req->status)) }}
                                </span>
                            </div>
                            @if(auth()->user()->isAdmin())
                                <span class="hr-requestor">
                                    <i class="fas fa-user"></i> {{ $req->user->name }}
                                </span>
                            @endif
                        </div>
                        <span class="hr-date"><i class="fas fa-calendar-alt"></i> {{ $req->created_date }}</span>
                    </div>

                    <h3 class="hr-card-subject">{{ $req->subject }}</h3>
                    <p class="hr-card-desc">{{ Str::limit($req->description, 160) }}</p>

                    @if($req->admin_response)
                        <div class="hr-admin-note">
                            <i class="fas fa-reply"></i>
                            <span>{{ Str::limit($req->admin_response, 120) }}</span>
                        </div>
                    @endif

                    <div class="hr-card-actions">
                        <a href="{{ route('client.help.show', $req->id) }}" class="hr-btn hr-btn-outline hr-btn-sm">
                            <i class="fas fa-eye"></i> View
                        </a>

                        @if(auth()->user()->isAdmin())
                            @if($req->status === 'pending')
                                <button class="hr-btn hr-btn-success hr-btn-sm"
                                        onclick="openApproveModal({{ $req->id }}, '{{ addslashes($req->subject) }}')">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                                <button class="hr-btn hr-btn-danger hr-btn-sm"
                                        onclick="openRejectModal({{ $req->id }}, '{{ addslashes($req->subject) }}')">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                            @endif
                            <a href="{{ route('client.help.edit', $req->id) }}" class="hr-btn hr-btn-warning hr-btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <button class="hr-btn hr-btn-ghost hr-btn-sm"
                                    onclick="deleteRequest({{ $req->id }})">
                                <i class="fas fa-trash"></i>
                            </button>
                        @else
                            @if($req->user_id === auth()->id() && $req->status === 'pending')
                                <a href="{{ route('client.help.edit', $req->id) }}" class="hr-btn hr-btn-warning hr-btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button class="hr-btn hr-btn-ghost hr-btn-sm"
                                        onclick="deleteRequest({{ $req->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @endif
                        @endif
                    </div>
                </div>
                @empty
                <div class="hr-empty">
                    <i class="fas fa-hands-helping"></i>
                    <h3>No requests found</h3>
                    @if(!auth()->user()->isAdmin())
                        <p>Submit your first help or supply request.</p>
                        <a href="{{ route('client.help.create') }}" class="hr-btn hr-btn-primary">
                            <i class="fas fa-plus"></i> New Request
                        </a>
                    @else
                        <p>No help requests have been submitted yet.</p>
                    @endif
                </div>
                @endforelse
            </div>

        </div>
    </div>
</div>

{{-- Approve Modal --}}
<div class="hr-modal" id="approveModal">
    <div class="hr-modal-box">
        <div class="hr-modal-head hr-modal-success">
            <i class="fas fa-check-circle"></i>
            <h3>Approve Request</h3>
        </div>
        <div class="hr-modal-body">
            <p id="approveModalSubject" style="font-weight:600;color:#212529;margin-bottom:12px;"></p>
            <label style="font-size:13px;font-weight:600;color:#495057;display:block;margin-bottom:6px;">
                Response / Notes <span style="font-weight:400;color:#6c757d;">(optional)</span>
            </label>
            <textarea id="approveResponse" rows="4" class="hr-textarea"
                      placeholder="Provide a response or action taken..."></textarea>
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
            <i class="fas fa-times-circle"></i>
            <h3>Reject Request</h3>
        </div>
        <div class="hr-modal-body">
            <p id="rejectModalSubject" style="font-weight:600;color:#212529;margin-bottom:12px;"></p>
            <label style="font-size:13px;font-weight:600;color:#495057;display:block;margin-bottom:6px;">
                Reason for rejection <span style="font-weight:400;color:#6c757d;">(optional)</span>
            </label>
            <textarea id="rejectReason" rows="4" class="hr-textarea"
                      placeholder="Provide a reason for rejection..."></textarea>
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
.hr-page-title{font-size:22px;font-weight:600;color:#296218;margin:0;display:flex;align-items:center;gap:10px}
.hr-alert{display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:8px;font-size:14px;font-weight:500;margin-bottom:4px}
.hr-alert-success{background:#d4edda;border:1px solid #c3e6cb;color:#155724}
.hr-alert-danger{background:#f8d7da;border:1px solid #f5c6cb;color:#721c24}

/* Filter */
.hr-filter-bar{background:#fff;border:1px solid #e9ecef;border-radius:10px;padding:12px 16px}
.hr-filter-form{display:flex;gap:10px;align-items:center;flex-wrap:wrap}
.hr-search-box{display:flex;align-items:center;gap:8px;border:1px solid #dee2e6;border-radius:8px;padding:0 12px;height:36px;flex:1;min-width:200px;background:#fff}
.hr-search-box i{color:#adb5bd;font-size:13px}
.hr-search-box input{border:none;outline:none;background:transparent;font-size:14px;color:#495057;width:100%}
.hr-select{padding:7px 32px 7px 12px;border:1px solid #dee2e6;border-radius:8px;font-size:14px;color:#495057;background:#fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23adb5bd' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E") no-repeat right 10px center;outline:none;appearance:none;cursor:pointer;height:36px}

/* Cards grid */
.hr-list{display:grid;grid-template-columns:repeat(auto-fill,minmax(360px,1fr));gap:16px}
.hr-card{background:#fff;border:1px solid #e9ecef;border-radius:12px;padding:20px;display:flex;flex-direction:column;gap:12px;transition:box-shadow .15s,border-color .15s}
.hr-card:hover{box-shadow:0 4px 16px rgba(0,0,0,.08);border-color:#d0d7de}
.hr-card-action{border-left:4px solid #ffc107}

.hr-card-top{display:flex;justify-content:space-between;align-items:flex-start;gap:8px;flex-wrap:wrap}
.hr-card-meta{display:flex;flex-direction:column;gap:6px}
.hr-card-badges{display:flex;gap:6px;flex-wrap:wrap}
.hr-date{font-size:12px;color:#6c757d;display:flex;align-items:center;gap:5px;white-space:nowrap}
.hr-requestor{font-size:12px;color:#296218;font-weight:600;display:flex;align-items:center;gap:5px}

/* Priority badges */
.hr-priority{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.3px}
.hr-priority-high{background:#f8d7da;color:#721c24;border:1px solid #f5c6cb}
.hr-priority-medium{background:#fff3cd;color:#856404;border:1px solid #ffeeba}
.hr-priority-low{background:#d4edda;color:#155724;border:1px solid #c3e6cb}

/* Status badges */
.hr-status{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.3px}
.hr-status-pending{background:#fff3cd;color:#856404;border:1px solid #ffeeba}
.hr-status-in_progress{background:#cce5ff;color:#004085;border:1px solid #b8daff}
.hr-status-resolved{background:#d4edda;color:#155724;border:1px solid #c3e6cb}
.hr-status-closed{background:#e2e3e5;color:#383d41;border:1px solid #d6d8db}

.hr-card-subject{font-size:16px;font-weight:600;color:#212529;margin:0}
.hr-card-desc{font-size:13px;color:#6c757d;margin:0;line-height:1.6}
.hr-admin-note{display:flex;align-items:flex-start;gap:8px;background:#f0f8f0;border-left:3px solid #296218;border-radius:0 6px 6px 0;padding:10px 12px;font-size:13px;color:#296218}
.hr-admin-note i{margin-top:1px;flex-shrink:0}

/* Action buttons */
.hr-card-actions{display:flex;gap:8px;flex-wrap:wrap;align-items:center;padding-top:4px;border-top:1px solid #f0f0f0}
.hr-btn{display:inline-flex;align-items:center;gap:6px;padding:0 14px;height:34px;border-radius:7px;font-size:13px;font-weight:500;cursor:pointer;border:none;text-decoration:none;white-space:nowrap;transition:filter .15s}
.hr-btn:hover{filter:brightness(.9)}
.hr-btn-sm{height:30px;padding:0 10px;font-size:12px}
.hr-btn-primary{background:#296218;color:#fff}
.hr-btn-success{background:#28a745;color:#fff}
.hr-btn-danger{background:#dc3545;color:#fff}
.hr-btn-warning{background:#ffc107;color:#212529}
.hr-btn-outline{background:transparent;color:#296218;border:1px solid #296218}
.hr-btn-ghost{background:#f8f9fa;color:#6c757d;border:1px solid #dee2e6}

/* Empty */
.hr-empty{text-align:center;padding:60px 20px;display:flex;flex-direction:column;align-items:center;gap:12px;background:#fff;border:1px solid #e9ecef;border-radius:12px}
.hr-empty i{font-size:52px;color:#dee2e6}
.hr-empty h3{font-size:18px;color:#495057;margin:0}
.hr-empty p{font-size:14px;color:#6c757d;margin:0}

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

@media(max-width:600px){.hr-list{grid-template-columns:1fr}}
</style>

<script>
let currentHelpId = null;

function openApproveModal(id, subject) {
    currentHelpId = id;
    document.getElementById('approveModalSubject').textContent = subject;
    document.getElementById('approveResponse').value = '';
    document.getElementById('approveModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function openRejectModal(id, subject) {
    currentHelpId = id;
    document.getElementById('rejectModalSubject').textContent = subject;
    document.getElementById('rejectReason').value = '';
    document.getElementById('rejectModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeModal(id) {
    document.getElementById(id).classList.remove('active');
    document.body.style.overflow = '';
    currentHelpId = null;
}

// Close on backdrop click
document.querySelectorAll('.hr-modal').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) closeModal(m.id); });
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') document.querySelectorAll('.hr-modal.active').forEach(m => closeModal(m.id));
});

function confirmApprove() {
    if (!currentHelpId) return;
    $.ajax({
        url: '/client/help/' + currentHelpId + '/approve',
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
    if (!currentHelpId) return;
    $.ajax({
        url: '/client/help/' + currentHelpId + '/reject',
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

function deleteRequest(id) {
    if (!confirm('Are you sure you want to delete this request?')) return;
    $.ajax({
        url: '/client/help/' + id,
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').val() },
        success: function(data) { if (data.success) location.reload(); },
        error: function() { alert('Error deleting request.'); }
    });
}

setTimeout(() => {
    document.querySelectorAll('.hr-alert').forEach(el => {
        el.style.transition = 'opacity .4s'; el.style.opacity = '0';
        setTimeout(() => el.remove(), 400);
    });
}, 5000);
</script>
</body>
</html>