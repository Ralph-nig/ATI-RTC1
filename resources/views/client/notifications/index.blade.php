{{-- filepath: resources/views/client/notifications/index.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/notifications.css') }}">
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

        <div class="notif-wrap">

            {{-- Header --}}
            <div class="notif-page-header">
                <h1 class="notif-page-title">
                    <ion-icon name="notifications-outline"></ion-icon> All Notifications
                </h1>
                <button class="notif-btn notif-btn-secondary" onclick="markAllAsRead()">
                    <ion-icon name="checkmark-done-outline"></ion-icon> Mark All as Read
                </button>
            </div>

            {{-- Alerts --}}
            @if(session('success'))
                <div class="notif-alert notif-alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="notif-alert notif-alert-danger"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
            @endif

            {{-- List --}}
            @if($notifications->count() > 0)
            <div class="notif-list">
                @foreach($notifications as $notification)

                @php
                    $isRis     = $notification->type === 'ris_request';
                    $risId     = $notification->data['ris_id'] ?? null;
                    $isPending = false;
                    if ($isRis && $risId) {
                        $ris = \App\Models\Ris::find($risId);
                        $isPending = $ris && $ris->status === 'pending';
                    }
                @endphp

                <div class="notif-card {{ !$notification->is_read ? 'notif-card-unread' : '' }}"
                     id="notif-card-{{ $notification->id }}">

                    {{-- Icon --}}
                    <div class="notif-icon notif-icon-{{ $notification->type }}">
                        @switch($notification->type)
                            @case('ris_request')
                                <i class="fas fa-file-alt"></i>
                                @break
                            @case('ris_approved')
                                <i class="fas fa-check-circle"></i>
                                @break
                            @case('ris_rejected')
                                <i class="fas fa-times-circle"></i>
                                @break
                            @case('help_request')
                                <i class="fas fa-question-circle"></i>
                                @break
                            @case('help_response')
                                <i class="fas fa-reply"></i>
                                @break
                            @default
                                <i class="fas fa-bell"></i>
                        @endswitch
                    </div>

                    {{-- Body --}}
                    <div class="notif-body">
                        <div class="notif-top-row">
                            <div class="notif-title-group">
                                <span class="notif-title">{{ $notification->title }}</span>
                                @if(!$notification->is_read)
                                    <span class="notif-dot"></span>
                                @endif
                            </div>
                            <span class="notif-time">{{ $notification->created_date }}</span>
                        </div>

                        <p class="notif-message">{{ $notification->message }}</p>

                        {{-- RIS meta info --}}
                        @if($isRis && isset($notification->data['ris_reference']))
                        <div class="notif-meta">
                            <span class="notif-ref">
                                <i class="fas fa-hashtag" style="font-size:10px"></i>
                                {{ $notification->data['ris_reference'] }}
                            </span>
                            <span class="notif-items">
                                <i class="fas fa-boxes" style="font-size:10px"></i>
                                {{ $notification->data['item_count'] ?? 0 }} item(s)
                            </span>
                            @if($isPending)
                                <span class="notif-status-badge notif-pending">Pending Review</span>
                            @elseif($ris)
                                <span class="notif-status-badge notif-{{ $ris->status }}">{{ ucfirst($ris->status) }}</span>
                            @endif
                        </div>
                        @endif

                        {{-- RIS result badges --}}
                        @if(in_array($notification->type, ['ris_approved', 'ris_rejected']))
                        <div class="notif-meta">
                            <span class="notif-ref">
                                <i class="fas fa-hashtag" style="font-size:10px"></i>
                                {{ $notification->data['ris_reference'] ?? '' }}
                            </span>
                            <span class="notif-status-badge notif-{{ $notification->type === 'ris_approved' ? 'approved' : 'rejected' }}">
                                {{ $notification->type === 'ris_approved' ? 'Approved' : 'Rejected' }}
                            </span>
                        </div>
                        @endif

                        {{-- Action buttons --}}
                        <div class="notif-actions">

                            {{-- View RIS link --}}
                            @if($isRis && $risId)
                            <a href="{{ route('client.ris.show', $risId) }}"
                               class="notif-btn notif-btn-view"
                               onclick="markRead({{ $notification->id }})">
                                <i class="fas fa-eye"></i> View Request
                            </a>
                            @endif

                            {{-- RIS result: view link --}}
                            @if(in_array($notification->type, ['ris_approved','ris_rejected']) && isset($notification->data['ris_id']))
                            <a href="{{ route('client.ris.show', $notification->data['ris_id']) }}"
                               class="notif-btn notif-btn-view"
                               onclick="markRead({{ $notification->id }})">
                                <i class="fas fa-eye"></i> View RIS
                            </a>
                            @endif

                            <!-- {{-- Admin: Approve / Reject (only when still pending) --}}
                            @if($isRis && $isPending && auth()->user()->isAdmin())
                            <button class="notif-btn notif-btn-approve"
                                    onclick="approveRis({{ $risId }}, {{ $notification->id }})">
                                <i class="fas fa-check"></i> Approve
                            </button>
                            <button class="notif-btn notif-btn-reject"
                                    onclick="openReject({{ $risId }}, {{ $notification->id }})">
                                <i class="fas fa-times"></i> Reject
                            </button>
                            @endif -->

                            {{-- Help notification link --}}
                            @if(in_array($notification->type, ['help_request','help_response']) && isset($notification->data['help_request_id']))
                            <a href="{{ route('client.help.show', $notification->data['help_request_id']) }}"
                               class="notif-btn notif-btn-view"
                               onclick="markRead({{ $notification->id }})">
                                <i class="fas fa-eye"></i> View Request
                            </a>
                            @endif

                            {{-- Mark as read (if unread and no specific action) --}}
                            @if(!$notification->is_read && !$isRis && !in_array($notification->type, ['ris_approved','ris_rejected','help_request','help_response']))
                            <button class="notif-btn notif-btn-read"
                                    onclick="markRead({{ $notification->id }})">
                                <i class="fas fa-check"></i> Mark as Read
                            </button>
                            @endif

                        </div>
                    </div>

                </div>
                @endforeach
            </div>

            <div class="notif-pagination">
                {{ $notifications->links() }}
            </div>

            @else
            <div class="notif-empty">
                <ion-icon name="notifications-off-outline"></ion-icon>
                <h3>No notifications yet</h3>
                <p>You'll see notifications here when there are updates.</p>
            </div>
            @endif

        </div>
    </div>
</div>

{{-- Reject modal --}}
<div id="rejectModal" class="notif-modal">
    <div class="notif-modal-box">
        <div class="notif-modal-head">
            <i class="fas fa-times-circle"></i>
            <h3>Reject RIS Request</h3>
        </div>
        <div class="notif-modal-body">
            <p>Provide a reason for rejecting this request (optional):</p>
            <textarea id="rejectReason" class="notif-textarea"
                      placeholder="e.g. Insufficient budget, please re-submit next quarter."></textarea>
        </div>
        <div class="notif-modal-foot">
            <button class="notif-btn notif-btn-secondary" onclick="closeReject()">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button class="notif-btn notif-btn-reject-confirm" onclick="proceedReject()">
                <i class="fas fa-ban"></i> Reject
            </button>
        </div>
    </div>
</div>

@include('layouts.core.footer')

<style>
/* ── Layout ── */
.notif-wrap { padding:24px; display:flex; flex-direction:column; gap:16px; }

/* Page header */
.notif-page-header { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; }
.notif-page-title { font-size:22px; font-weight:600; color:#296218; margin:0; display:flex; align-items:center; gap:10px; }

/* Alerts */
.notif-alert { display:flex; align-items:center; gap:10px; padding:12px 16px; border-radius:8px; font-size:14px; font-weight:500; }
.notif-alert-success { background:#d4edda; border:1px solid #c3e6cb; color:#155724; }
.notif-alert-danger  { background:#f8d7da; border:1px solid #f5c6cb; color:#721c24; }

/* List */
.notif-list { display:flex; flex-direction:column; gap:10px; }

/* Card */
.notif-card {
    background:#fff; border:1px solid #e9ecef; border-radius:12px;
    padding:16px 18px; display:flex; gap:14px; align-items:flex-start;
    transition:box-shadow .15s;
}
.notif-card:hover { box-shadow:0 2px 12px rgba(0,0,0,.08); }
.notif-card-unread {
    border-left:4px solid #296218;
    background:#f8fdf6;
}

/* Icon */
.notif-icon {
    width:42px; height:42px; border-radius:10px; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    font-size:18px;
}
.notif-icon-ris_request  { background:#e8f4fd; color:#17a2b8; }
.notif-icon-ris_approved { background:#d4edda; color:#28a745; }
.notif-icon-ris_rejected { background:#f8d7da; color:#dc3545; }
.notif-icon-help_request { background:#fff3cd; color:#ffc107; }
.notif-icon-help_response{ background:#d4edda; color:#28a745; }
.notif-icon-             { background:#e9ecef; color:#6c757d; }

/* Body */
.notif-body { flex:1; display:flex; flex-direction:column; gap:6px; }
.notif-top-row { display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap; }
.notif-title-group { display:flex; align-items:center; gap:8px; }
.notif-title { font-size:14px; font-weight:600; color:#212529; }
.notif-dot { width:8px; height:8px; background:#296218; border-radius:50%; flex-shrink:0; }
.notif-time { font-size:12px; color:#6c757d; white-space:nowrap; }
.notif-message { font-size:13px; color:#495057; line-height:1.5; margin:0; }

/* Meta row */
.notif-meta { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
.notif-ref { font-size:12px; font-family:monospace; color:#296218; font-weight:600; }
.notif-items { font-size:12px; color:#6c757d; }
.notif-status-badge {
    font-size:11px; padding:2px 9px; border-radius:20px;
    font-weight:600; text-transform:uppercase; letter-spacing:.3px;
}
.notif-pending  { background:#fff3cd; color:#856404; border:1px solid #ffeeba; }
.notif-approved { background:#d4edda; color:#155724; border:1px solid #c3e6cb; }
.notif-rejected { background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; }

/* Actions */
.notif-actions { display:flex; gap:8px; flex-wrap:wrap; margin-top:4px; }

/* Buttons */
.notif-btn {
    display:inline-flex; align-items:center; gap:5px;
    padding:0 13px; height:32px; border-radius:7px;
    font-size:13px; font-weight:500; cursor:pointer;
    border:none; text-decoration:none; white-space:nowrap; transition:filter .15s;
}
.notif-btn:hover { filter:brightness(.9); }
.notif-btn-view         { background:#17a2b8; color:#fff; }
.notif-btn-approve      { background:#28a745; color:#fff; }
.notif-btn-reject       { background:#dc3545; color:#fff; }
.notif-btn-reject-confirm{ background:#dc3545; color:#fff; }
.notif-btn-read         { background:#e9ecef; color:#495057; }
.notif-btn-secondary    { background:#6c757d; color:#fff; }

/* Empty */
.notif-empty { text-align:center; padding:60px 20px; display:flex; flex-direction:column; align-items:center; gap:10px; }
.notif-empty ion-icon { font-size:52px; color:#dee2e6; }
.notif-empty h3 { font-size:18px; color:#495057; margin:0; }
.notif-empty p  { font-size:14px; color:#6c757d; margin:0; }

/* Pagination */
.notif-pagination { padding:4px 0; }

/* Modal */
.notif-modal { display:none; position:fixed; inset:0; background:rgba(0,0,0,.55); z-index:9999; align-items:center; justify-content:center; }
.notif-modal.active { display:flex; }
.notif-modal-box { background:#fff; border-radius:14px; width:90%; max-width:420px; overflow:hidden; animation:notif-modal-in .2s ease; }
@keyframes notif-modal-in { from{opacity:0;transform:translateY(18px)} to{opacity:1;transform:none} }
.notif-modal-head { background:#dc3545; color:#fff; padding:18px 22px; display:flex; align-items:center; gap:12px; }
.notif-modal-head i { font-size:24px; }
.notif-modal-head h3 { margin:0; font-size:18px; font-weight:600; }
.notif-modal-body { padding:20px 22px; }
.notif-modal-body p { margin:0 0 10px; font-size:14px; color:#495057; }
.notif-textarea { width:100%; box-sizing:border-box; padding:9px 12px; border:1px solid #dee2e6; border-radius:8px; font-size:13px; color:#495057; outline:none; resize:vertical; min-height:80px; }
.notif-textarea:focus { border-color:#dc3545; box-shadow:0 0 0 3px rgba(220,53,69,.1); }
.notif-modal-foot { padding:14px 22px; background:#f8f9fa; display:flex; justify-content:flex-end; gap:8px; border-top:1px solid #e9ecef; }
</style>

<script>
const csrf = document.querySelector('meta[name=csrf-token]').content;

/* ── Mark single as read ── */
function markRead(id) {
    fetch(`/client/notifications/${id}/mark-as-read`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
    }).then(r => r.json()).then(d => {
        if (d.success) {
            const card = document.getElementById('notif-card-' + id);
            if (card) {
                card.classList.remove('notif-card-unread');
                card.querySelector('.notif-dot')?.remove();
            }
        }
    });
}

/* ── Mark all as read ── */
function markAllAsRead() {
    if (!confirm('Mark all notifications as read?')) return;
    fetch('/client/notifications/mark-all-read', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
    }).then(r => r.json()).then(d => { if (d.success) location.reload(); });
}

/* ── Approve RIS ── */
function approveRis(risId, notifId) {
    if (!confirm('Approve this RIS and deduct stock from inventory?')) return;
    fetch(`/client/ris/${risId}/approve`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf }
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            markRead(notifId);
            location.reload();
        } else {
            alert('Error: ' + d.message);
        }
    })
    .catch(() => alert('Request failed.'));
}

/* ── Reject RIS modal ── */
let pendingRisId = null;
let pendingNotifId = null;

function openReject(risId, notifId) {
    pendingRisId   = risId;
    pendingNotifId = notifId;
    document.getElementById('rejectReason').value = '';
    document.getElementById('rejectModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeReject() {
    document.getElementById('rejectModal').classList.remove('active');
    document.body.style.overflow = '';
    pendingRisId = pendingNotifId = null;
}

function proceedReject() {
    const reason = document.getElementById('rejectReason').value || '';
    fetch(`/client/ris/${pendingRisId}/reject`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify({ reason })
    })
    .then(r => r.json())
    .then(d => {
        closeReject();
        if (d.success) {
            markRead(pendingNotifId);
            location.reload();
        } else {
            alert('Error: ' + d.message);
        }
    })
    .catch(() => alert('Request failed.'));
}

/* Close modal on outside click or Escape */
document.getElementById('rejectModal').addEventListener('click', e => {
    if (e.target === document.getElementById('rejectModal')) closeReject();
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeReject();
});

/* Auto-dismiss alerts */
setTimeout(() => {
    document.querySelectorAll('.notif-alert').forEach(el => {
        el.style.transition = 'opacity .4s'; el.style.opacity = '0';
        setTimeout(() => el.remove(), 400);
    });
}, 5000);
</script>
</body>
</html>