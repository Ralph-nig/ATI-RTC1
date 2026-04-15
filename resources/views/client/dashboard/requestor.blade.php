{{-- filepath: resources/views/client/dashboard/requestor.blade.php --}}

<div class="requestor-wrap">
    <div class="requestor-banner">

        <div class="requestor-watermark-ring"></div>

        {{-- Top row: greeting + quick stats --}}
        <div class="requestor-top-row">
            <div class="requestor-greeting">
                <span class="requestor-greeting-sub">Good day</span>
                <h2>Welcome, {{ auth()->user()->name }}</h2>
                <p>How can I help you today?</p>
            </div>
            <div class="requestor-stats">
                <div class="requestor-stat">
                    <span class="requestor-stat-val">{{ $pending ?? 0 }}</span>
                    <span class="requestor-stat-lbl">Pending</span>
                </div>
                <div class="requestor-stat">
                    <span class="requestor-stat-val">{{ $approved ?? 0 }}</span>
                    <span class="requestor-stat-lbl">Approved</span>
                </div>
                <div class="requestor-stat">
                    <span class="requestor-stat-val">{{ $items ?? 0 }}</span>
                    <span class="requestor-stat-lbl">Items</span>
                </div>
            </div>
        </div>

        <div class="requestor-divider"></div>

        {{-- Action cards --}}
        <div class="requestor-cards">
            <a href="{{ route('client.ris.create') }}" class="requestor-card">
                <div class="requestor-card-icon"><i class="fas fa-file-alt"></i></div>
                <div class="requestor-card-body">
                    <div class="requestor-card-title">RIS</div>
                    <div class="requestor-card-sub">Requisition for Issuance Slip</div>
                </div>
            </a>
            <a href="{{ route('client.ris.index') }}" class="requestor-card">
                <div class="requestor-card-icon"><i class="fas fa-clipboard-list"></i></div>
                <div class="requestor-card-body">
                    <div class="requestor-card-title">My Requests</div>
                    <div class="requestor-card-sub">Track your submissions</div>
                </div>
            </a>
            <a href="{{ route('equipment.my') }}" class="requestor-card">
                <div class="requestor-card-icon"><i class="fas fa-box"></i></div>
                <div class="requestor-card-body">
                    <div class="requestor-card-title">My Equipments</div>
                    <div class="requestor-card-sub">User's inventory</div>
                </div>
            </a>
            <a href="{{ route('client.profile.index') }}" class="requestor-card">
                <div class="requestor-card-icon"><i class="fas fa-user-circle"></i></div>
                <div class="requestor-card-body">
                    <div class="requestor-card-title">Profile</div>
                    <div class="requestor-card-sub">Settings &amp; account</div>
                </div>
            </a>
        </div>

        <div class="requestor-divider"></div>

        {{-- Recent requests --}}
        <div class="requestor-recent">
            <p class="requestor-recent-label">Recent requests</p>

            @if(isset($recentRis) && $recentRis->isNotEmpty())
            <div class="requestor-recent-list">
                @foreach($recentRis as $ris)
                @php
                    $dotClass   = match($ris->status) { 'approved' => 'dot-green', 'rejected' => 'dot-red', default => 'dot-yellow' };
                    $badgeClass = match($ris->status) { 'approved' => 'badge-green', 'rejected' => 'badge-red', default => 'badge-yellow' };
                    $label      = ucfirst($ris->status);
                @endphp
                <a href="{{ route('client.ris.show', $ris->id) }}" class="requestor-recent-item">
                    <span class="requestor-dot {{ $dotClass }}"></span>
                    <div class="requestor-recent-info">
                        <span class="requestor-recent-title">{{ $ris->purpose }}</span>
                        <span class="requestor-recent-date">
                            {{ $ris->reference }} &middot; Submitted {{ $ris->created_at->format('M d, Y') }}
                        </span>
                    </div>
                    <span class="requestor-badge {{ $badgeClass }}">{{ $label }}</span>
                </a>
                @endforeach
            </div>
            @else
            <div style="text-align:center;padding:24px 0;color:rgba(255,255,255,.3);font-size:13px;">
                <i class="fas fa-inbox" style="font-size:28px;display:block;margin-bottom:8px;opacity:.4;"></i>
                No requests yet.
            </div>
            @endif
        </div>

        <div class="requestor-footer-text">
            <span>1898 · AGRICULTURAL TRAINING INSTITUTE · REGIONAL TRAINING CENTER 1</span>
        </div>

    </div>
</div>

<style>
/* ── Wrapper ── */
.requestor-wrap {
    flex: 1;
    padding: 20px;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
}

/* ── Banner ── */
.requestor-banner {
    background: #1d4e0f;
    border-radius: 20px;
    flex: 1;
    display: flex;
    flex-direction: column;
    position: relative;
    overflow: hidden;
    padding: 28px 28px 24px;
    gap: 0;
}

@keyframes wm-pulse {
    0%, 100% { opacity: .6; }
    50%       { opacity: 1; }
}

/* ── Top row ── */
.requestor-top-row {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
    position: relative;
    z-index: 1;
    animation: fadeUp .4s cubic-bezier(.22,1,.36,1) both;
}
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ── Greeting ── */
.requestor-greeting-sub {
    font-size: 12px;
    color: rgba(255,255,255,.5);
    letter-spacing: .5px;
    display: block;
    margin-bottom: 4px;
}
.requestor-greeting h2 {
    font-size: 26px;
    font-weight: 600;
    color: #fff;
    margin: 0 0 2px;
}
.requestor-greeting p {
    font-size: 14px;
    color: rgba(255,255,255,.6);
    margin: 0;
}

/* ── Stats ── */
.requestor-stats {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}
.requestor-stat {
    background: rgba(255,255,255,.08);
    border: 0.5px solid rgba(255,255,255,.12);
    border-radius: 12px;
    padding: 14px 20px;
    min-width: 76px;
    text-align: center;
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.requestor-stat-val {
    font-size: 22px;
    font-weight: 600;
    color: #fff;
}
.requestor-stat-lbl {
    font-size: 10px;
    color: rgba(255,255,255,.5);
    text-transform: uppercase;
    letter-spacing: 1.2px;
}

/* ── Divider ── */
.requestor-divider {
    height: 0.5px;
    background: rgba(255,255,255,.1);
    margin: 22px 0;
    position: relative;
    z-index: 1;
}

/* ── Action cards ── */
.requestor-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(155px, 1fr));
    gap: 14px;
    position: relative;
    z-index: 1;
    animation: fadeUp .45s cubic-bezier(.22,1,.36,1) .08s both;
}
.requestor-card {
    background: #296218;
    border: 0.5px solid rgba(255,255,255,.14);
    border-radius: 14px;
    padding: 22px 18px 20px;
    color: #fff;
    text-decoration: none;
    display: flex;
    flex-direction: column;
    gap: 12px;
    transition: transform .2s, filter .2s;
}
.requestor-card:hover {
    transform: translateY(-3px);
    filter: brightness(1.08);
}
.requestor-card-icon {
    width: 44px;
    height: 44px;
    background: rgba(255,255,255,.1);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}
.requestor-card-body { display: flex; flex-direction: column; gap: 3px; }
.requestor-card-title { font-size: 15px; font-weight: 600; color: #fff; }
.requestor-card-sub   { font-size: 12px; color: rgba(255,255,255,.5); }

/* ── Recent requests ── */
.requestor-recent {
    position: relative;
    z-index: 1;
    animation: fadeUp .45s cubic-bezier(.22,1,.36,1) .16s both;
}
.requestor-recent-label {
    font-size: 11px;
    color: rgba(255,255,255,.4);
    text-transform: uppercase;
    letter-spacing: 1.5px;
    margin: 0 0 12px;
}
.requestor-recent-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.requestor-recent-item {
    background: rgba(255,255,255,.06);
    border: 0.5px solid rgba(255,255,255,.1);
    border-radius: 10px;
    padding: 12px 14px;
    display: flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
    transition: background .2s;
}
.requestor-recent-item:hover { background: rgba(255,255,255,.1); }
.requestor-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
    align-self: flex-start;
    margin-top: 3px;
}
.dot-yellow { background: #FAC775; }
.dot-green  { background: #5DCAA5; }
.dot-red    { background: #F0997B; }

.requestor-recent-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.requestor-recent-title { font-size: 13px; color: #fff; }
.requestor-recent-date  { font-size: 11px; color: rgba(255,255,255,.4); }

.requestor-badge {
    font-size: 10px;
    padding: 3px 10px;
    border-radius: 20px;
    font-weight: 500;
    white-space: nowrap;
    flex-shrink: 0;
}
.badge-yellow { background: rgba(250,199,117,.18); color: #FAC775; }
.badge-green  { background: rgba(93,202,165,.18);  color: #5DCAA5; }
.badge-red    { background: rgba(240,153,123,.18); color: #F0997B; }

/* ── Footer ── */
.requestor-footer-text {
    text-align: center;
    margin-top: 22px;
    position: relative;
    z-index: 1;
    font-size: 10px;
    color: rgba(255,255,255,.2);
    letter-spacing: 1.5px;
    text-transform: uppercase;
}

/* ── Responsive ── */
@media (max-width: 640px) {
    .requestor-top-row     { flex-direction: column; }
    .requestor-stats       { width: 100%; justify-content: space-between; }
    .requestor-cards       { grid-template-columns: 1fr 1fr; }
    .requestor-greeting h2 { font-size: 20px; }
}
@media (max-width: 400px) {
    .requestor-cards { grid-template-columns: 1fr; }
}
</style>