<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ATI-RTC1 - Organizational Chart</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* ── Base ── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        .orgchart-page {
            padding: 28px 32px 60px;
            min-height: 100vh;
            background: #f4f6f9;
            font-family: 'Segoe UI', sans-serif;
        }

        /* ── Page Header ── */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
            flex-wrap: wrap;
            gap: 12px;
        }
        .page-header-left { display: flex; align-items: center; gap: 14px; }
        .page-title {
            font-size: 22px;
            font-weight: 700;
            color: #1a3a0f;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .page-title i { color: #296218; }
        .page-subtitle { font-size: 13px; color: #6c757d; margin-top: 3px; }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
            background: white;
            border: 1.5px solid #296218;
            color: #296218;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: all .2s;
        }
        .btn-back:hover { background: #296218; color: white; }

        /* ── Legend ── */
        .legend-bar {
            display: flex;
            align-items: center;
            gap: 20px;
            background: white;
            border-radius: 10px;
            padding: 12px 20px;
            margin-bottom: 24px;
            box-shadow: 0 1px 6px rgba(0,0,0,.07);
            flex-wrap: wrap;
        }
        .legend-label { font-size: 12px; font-weight: 700; color: #6c757d; text-transform: uppercase; letter-spacing: .5px; margin-right: 4px; }
        .legend-item {
            display: flex; align-items: center; gap: 7px;
            font-size: 12px; color: #444; font-weight: 500;
        }
        .legend-dot {
            width: 12px; height: 12px; border-radius: 50%;
        }

        /* ── Filter & Search ── */
        .filter-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }
        .search-org {
            display: flex; align-items: center; gap: 8px;
            background: white; border: 1.5px solid #dee2e6;
            border-radius: 8px; padding: 8px 14px;
            flex: 1; min-width: 200px; max-width: 320px;
            transition: border-color .2s;
        }
        .search-org:focus-within { border-color: #296218; }
        .search-org i { color: #999; font-size: 13px; }
        .search-org input {
            border: none; outline: none; font-size: 13px;
            color: #333; width: 100%; background: transparent;
        }
        .filter-unit {
            padding: 8px 14px; background: white;
            border: 1.5px solid #dee2e6; border-radius: 8px;
            font-size: 13px; color: #333; outline: none;
            cursor: pointer; transition: border-color .2s;
        }
        .filter-unit:focus { border-color: #296218; }

        /* ── Chart Wrapper ── */
        .chart-scroll-area {
            overflow-x: auto;
            padding-bottom: 20px;
        }
        .org-chart {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0;
            min-width: 900px;
        }

        /* ── Connector Lines ── */
        .v-line {
            width: 2px; background: #c3d9bb;
            margin: 0 auto;
        }
        .h-line {
            height: 2px; background: #c3d9bb;
            width: 100%;
        }
        .branch-row {
            display: flex;
            align-items: flex-start;
            justify-content: center;
            gap: 0;
            width: 100%;
        }
        .branch-col {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
        }
        .branch-col .v-line { height: 24px; }
        .top-v    { height: 28px; }
        .short-v  { height: 18px; }

        /* ── Node Card ── */
        .node-card {
            background: white;
            border-radius: 12px;
            border: 2px solid #d4e8cc;
            padding: 14px 16px 12px;
            width: 170px;
            text-align: center;
            box-shadow: 0 3px 12px rgba(41,98,24,.08);
            transition: all .25s ease;
            cursor: pointer;
            position: relative;
        }
        .node-card:hover {
            border-color: #296218;
            box-shadow: 0 6px 20px rgba(41,98,24,.18);
            transform: translateY(-3px);
        }
        .node-card.highlighted { border-color: #296218 !important; background: #f0faf0 !important; }
        .node-card.dimmed { opacity: .35; pointer-events: none; }

        .node-avatar {
            width: 46px; height: 46px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 8px;
            font-size: 18px; color: white;
            font-weight: 700;
        }

        .node-name {
            font-size: 13px; font-weight: 700; color: #1a3a0f;
            line-height: 1.3; margin-bottom: 4px;
        }
        .node-email {
            font-size: 10px; color: #888; margin-bottom: 6px;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }

        .role-pill {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 2px 9px; border-radius: 20px;
            font-size: 10px; font-weight: 700; text-transform: uppercase;
            letter-spacing: .3px;
        }
        .role-admin    { background: #fff3cd; color: #856404; }
        .role-user     { background: #d4edda; color: #155724; }
        .role-requestor{ background: #d1ecf1; color: #0c5460; }

        .status-dot {
            position: absolute; top: 10px; right: 10px;
            width: 9px; height: 9px; border-radius: 50%;
            border: 1.5px solid white;
        }
        .status-active   { background: #28a745; }
        .status-inactive { background: #dc3545; }

        /* ── Section Header (unit banner) ── */
        .unit-banner {
            background: linear-gradient(135deg, #296218 0%, #3d8a28 100%);
            color: white;
            border-radius: 10px;
            padding: 8px 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 7px;
            white-space: nowrap;
            box-shadow: 0 3px 10px rgba(41,98,24,.2);
        }

        /* Top-level row (Director / Asst Director) */
        .top-row {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* ── Units row ── */
        .units-section {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .units-row {
            display: flex;
            justify-content: center;
            gap: 40px;
            width: 100%;
            flex-wrap: wrap;
        }
        .unit-col {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .unit-members {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }

        /* ── Empty slot ── */
        .node-card.vacant { border-style: dashed; border-color: #adb5bd; }
        .node-card.vacant .node-name { color: #6c757d; }
        .node-card.vacant .node-avatar { background: #dee2e6 !important; }

        /* ── Tooltip-style info popup ── */
        .node-popup {
            display: none;
            position: fixed;
            z-index: 9999;
            background: white;
            border: 2px solid #296218;
            border-radius: 14px;
            padding: 20px;
            width: 280px;
            box-shadow: 0 10px 40px rgba(0,0,0,.18);
        }
        .node-popup .pop-name  { font-size: 15px; font-weight: 700; color: #1a3a0f; margin-bottom: 4px; }
        .node-popup .pop-email { font-size: 12px; color: #6c757d; margin-bottom: 12px; }
        .node-popup .pop-row   { display: flex; justify-content: space-between; font-size: 12px; padding: 5px 0; border-bottom: 1px solid #f0f0f0; color: #555; }
        .node-popup .pop-row:last-child { border-bottom: none; }
        .node-popup .pop-row strong { color: #333; }
        .pop-close { position: absolute; top: 10px; right: 12px; cursor: pointer; color: #999; font-size: 16px; }
        .pop-close:hover { color: #296218; }

        /* Permission chip in popup */
        .perm-chips { display: flex; flex-wrap: wrap; gap: 5px; margin-top: 8px; }
        .perm-chip {
            font-size: 10px; padding: 2px 8px; border-radius: 20px;
            font-weight: 600;
        }
        .perm-on  { background: #d4edda; color: #155724; }
        .perm-off { background: #f0f0f0; color: #999; text-decoration: line-through; }

        /* ── No users message ── */
        .no-users-msg {
            text-align: center; padding: 60px 20px; color: #6c757d;
        }
        .no-users-msg i { font-size: 48px; opacity: .4; margin-bottom: 16px; display: block; }

        /* ── OCD top section ── */
        .ocd-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            background: white;
            border: 2px solid #c3d9bb;
            border-radius: 16px;
            padding: 20px 32px 24px;
            box-shadow: 0 4px 18px rgba(41,98,24,.10);
            position: relative;
        }
        .ocd-banner {
            background: linear-gradient(135deg, #1a3a0f 0%, #296218 100%);
            color: white;
            border-radius: 10px;
            padding: 10px 28px;
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: inline-flex;
            align-items: center;
            gap: 9px;
            box-shadow: 0 4px 14px rgba(41,98,24,.25);
            margin-bottom: 0;
        }
        .asst-label {
            font-size: 10px;
            font-weight: 700;
            color: #888;
            text-transform: uppercase;
            letter-spacing: .6px;
        }

        @media (max-width: 768px) {
            .orgchart-page { padding: 16px; }
            .units-row { gap: 20px; }
            .node-card { width: 140px; }
        }
    </style>
</head>
<body>
<div class="container">
    @include('layouts.core.sidebar')
    <div class="details">
        @include('layouts.core.header')

        <div class="orgchart-page">

            {{-- ── Page Header ── --}}
            <div class="page-header">
                <div class="page-header-left">
                    <div>
                        <div class="page-title">
                            <i class="fas fa-sitemap"></i>
                            Organizational Chart
                        </div>
                        <div class="page-subtitle">ATI Regional Training Center I — Staff Directory</div>
                    </div>
                </div>
                <a href="{{ route('users.index') }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                    Back to Users
                </a>
            </div>

            {{-- ── Legend ── --}}
            <div class="legend-bar">
                <span class="legend-label">Legend:</span>
                <div class="legend-item"><span class="legend-dot" style="background:#856404; border:2px solid #fff3cd;"></span> Admin</div>
                <div class="legend-item"><span class="legend-dot" style="background:#296218; border:2px solid #d4edda;"></span> User</div>
                <div class="legend-item"><span class="legend-dot" style="background:#0c5460; border:2px solid #d1ecf1;"></span> Requestor</div>
                <div class="legend-item"><span class="legend-dot" style="background:#28a745;"></span> Active</div>
                <div class="legend-item"><span class="legend-dot" style="background:#dc3545;"></span> Inactive</div>
            </div>

            {{-- ── Search / Filter ── --}}
            <div class="filter-row">
                <div class="search-org">
                    <i class="fas fa-search"></i>
                    <input type="text" id="orgSearch" placeholder="Search by name or email…">
                </div>
                <select class="filter-unit" id="unitFilter">
                    <option value="">All Units / Sections</option>
                    <option value="ocd">Office of Center Director</option>
                    <option value="pme">Planning, Monitoring &amp; Evaluation Unit</option>
                    <option value="admin">Administrative &amp; Finance Unit</option>
                    <option value="cdm">Career Development &amp; Management Section</option>
                    <option value="pas">Partnership &amp; Accreditation Section</option>
                    <option value="iss">Information Services Section</option>
                </select>
            </div>

            {{-- ── Org Chart ── --}}
            @php
                /*
                 * Group users by role and name pattern / you can later replace these
                 * with a real "unit" column or a pivot table. For now we assign units
                 * in the view based on whatever data is available, falling back to a
                 * generic "Staff" bucket.
                 *
                 * To add proper unit assignment, add a `unit` column to your users
                 * table and update the grouping below.
                 */
                $allUsers   = $user ?? collect();

                // Pull admins first for the top slots
                $admins     = $allUsers->where('role', 'admin')->values();
                $director   = $admins->first();
                $assistant  = $admins->count() > 1 ? $admins->get(1) : null;

                $nonAdmins  = $allUsers->whereNotIn('id', $admins->pluck('id'));

                // Split remaining users across the five units (round-robin or by unit field if present)
                $unitKeys = ['pme','admin','cdm','pas','iss'];
                $units = [
                    'pme'   => ['label' => 'Planning, Monitoring & Evaluation Unit',  'icon' => '',    'color' => '#1e6fb5', 'members' => collect()],
                    'admin' => ['label' => 'Administrative & Finance Unit',            'icon' => '',         'color' => '#8a4a00', 'members' => collect()],
                    'cdm'   => ['label' => 'Career Development & Management Section',  'icon' => '','color' => '#296218', 'members' => collect()],
                    'pas'   => ['label' => 'Partnership & Accreditation Section',      'icon' => '',     'color' => '#6a1e8a', 'members' => collect()],
                    'iss'   => ['label' => 'Information Services Section',             'icon' => '',        'color' => '#0c6460', 'members' => collect()],
                ];
                // OCD is NOT in the units row — it renders above as a banner over Director/Asst. Director

                foreach ($nonAdmins as $index => $u) {
                    // Use user's `unit` attribute if available, else round-robin
                    $key = $u->unit ?? $unitKeys[$index % count($unitKeys)];
                    if (!array_key_exists($key, $units)) { $key = $unitKeys[$index % count($unitKeys)]; }
                    $units[$key]['members']->push($u);
                }

                // Helper — initials from name
                function orgInitials(string $name): string {
                    $parts = explode(' ', trim($name));
                    $i = strtoupper(substr($parts[0], 0, 1));
                    if (count($parts) > 1) { $i .= strtoupper(substr(end($parts), 0, 1)); }
                    return $i;
                }

                // Avatar background colours per role
                $avatarColors = ['admin' => '#856404', 'user' => '#296218', 'requestor' => '#0c5460'];
            @endphp

            <div class="chart-scroll-area">
              <div class="org-chart" id="orgChart">

                {{-- ══ LEVEL 0: Office of Center Director Banner ══ --}}
                <div class="ocd-wrapper" data-unit="ocd">
                    <div class="ocd-banner">
                        <!-- <i class="fas fa-user-tie"></i> -->
                        Office of Center Director
                    </div>

                    {{-- Director card ══ --}}
                    @if($director)
                    <div class="v-line" style="height:20px;"></div>
                    <div class="top-row">
                        @include('client.users.orgchart._node', ['u' => $director, 'avatarColors' => $avatarColors])
                    </div>
                    @endif

                    {{-- Asst. Director card ══ --}}
                    @if($assistant)
                    <div class="v-line" style="height:16px;"></div>
                    <div class="asst-label">Asst. Center Director</div>
                    <div class="v-line" style="height:10px;"></div>
                    <div class="top-row">
                        @include('client.users.orgchart._node', ['u' => $assistant, 'avatarColors' => $avatarColors])
                    </div>
                    @endif
                </div>

                {{-- Connector down from OCD to units ══ --}}
                <div class="v-line" style="height:28px;"></div>
                <div style="width:85%; height:2px; background:#c3d9bb; margin:0 auto;"></div>

                {{-- ══ LEVEL 1: Five Units Row ══ --}}
                <div class="units-row" id="unitsRow">
                    @foreach($units as $unitKey => $unit)
                    <div class="unit-col" data-unit="{{ $unitKey }}">
                        <div class="v-line short-v"></div>
                        <div class="unit-banner">
                            <i class="fas {{ $unit['icon'] }}"></i>
                            {{ Str::limit($unit['label'], 30) }}
                        </div>

                        <div class="unit-members">
                            @forelse($unit['members'] as $u)
                                @include('client.users.orgchart._node', ['u' => $u, 'avatarColors' => $avatarColors])
                                @if(!$loop->last)
                                    <div class="v-line" style="height:14px;"></div>
                                @endif
                            @empty
                                <div class="node-card vacant">
                                    <div class="node-avatar" style="background:#dee2e6;">
                                        <i class="fas fa-user-slash" style="color:#adb5bd;font-size:16px;"></i>
                                    </div>
                                    <div class="node-name" style="color:#adb5bd;">No staff assigned</div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                    @endforeach
                </div>

              </div>{{-- /org-chart --}}
            </div>{{-- /chart-scroll-area --}}

            @if($allUsers->isEmpty())
            <div class="no-users-msg">
                <i class="fas fa-users-slash"></i>
                <h3>No users found</h3>
                <p>Add users to populate the organizational chart.</p>
            </div>
            @endif

        </div>{{-- /orgchart-page --}}
    </div>{{-- /details --}}
</div>{{-- /container --}}

{{-- ── Popup ── --}}
<div class="node-popup" id="nodePopup">
    <span class="pop-close" id="popClose"><i class="fas fa-times"></i></span>
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
        <div id="popAvatar" style="width:44px;height:44px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;color:white;flex-shrink:0;"></div>
        <div>
            <div class="pop-name"  id="popName"></div>
            <div class="pop-email" id="popEmail"></div>
        </div>
    </div>
    <div class="pop-row"><span>Role</span>        <strong id="popRole"></strong></div>
    <div class="pop-row"><span>Status</span>      <strong id="popStatus"></strong></div>
    <div class="pop-row"><span>Member since</span><strong id="popDate"></strong></div>
    <div style="margin-top:10px;font-size:11px;font-weight:700;color:#6c757d;text-transform:uppercase;letter-spacing:.4px;">Permissions</div>
    <div class="perm-chips" id="popPerms"></div>
</div>

@include('layouts.core.footer')

<script>
/* ── Search & Filter ── */
$('#orgSearch').on('input', filterChart);
$('#unitFilter').on('change', filterChart);

function filterChart() {
    const q    = $('#orgSearch').val().toLowerCase().trim();
    const unit = $('#unitFilter').val();

    $('.node-card').each(function () {
        const card = $(this);
        const name  = (card.data('name')  || '').toLowerCase();
        const email = (card.data('email') || '').toLowerCase();
        const matchQ    = !q    || name.includes(q) || email.includes(q);
        card.toggleClass('dimmed', !matchQ);
    });

    if (unit) {
        $('[data-unit]').not('[data-unit="' + unit + '"]').addClass('dimmed');
        $('[data-unit="' + unit + '"]').find('.node-card').removeClass('dimmed');
    } else if (!q) {
        $('.node-card').removeClass('dimmed');
    }
}

/* ── Node Popup ── */
$(document).on('click', '.node-card:not(.vacant)', function (e) {
    const c = $(this);

    $('#popAvatar').text(c.data('initials')).css('background', c.data('color'));
    $('#popName').text(c.data('name'));
    $('#popEmail').text(c.data('email'));
    $('#popRole').text(c.data('role'));

    const status = c.data('status');
    $('#popStatus').html(status === 'active'
        ? '<span style="color:#28a745;font-weight:700;">● Active</span>'
        : '<span style="color:#dc3545;font-weight:700;">● Inactive</span>');
    $('#popDate').text(c.data('date'));

    // Permissions
    const perms = JSON.parse(c.attr('data-perms') || '{}');
    const labels = { can_create:'Create', can_read:'Read', can_update:'Update',
                     can_delete:'Delete', can_stock_in:'Stock In',
                     can_stock_out:'Stock Out', can_request:'Request' };
    let html = '';
    $.each(labels, function (key, lbl) {
        const on = perms[key];
        html += '<span class="perm-chip ' + (on ? 'perm-on' : 'perm-off') + '">' + lbl + '</span>';
    });
    $('#popPerms').html(html);

    // Position near card
    const rect = this.getBoundingClientRect();
    const pw = 280, ph = 320;
    let left = rect.right + 12;
    let top  = rect.top;
    if (left + pw > window.innerWidth)  left = rect.left - pw - 12;
    if (top  + ph > window.innerHeight) top  = window.innerHeight - ph - 12;
    $('#nodePopup').css({ top: Math.max(8, top), left: Math.max(8, left) }).fadeIn(180);

    e.stopPropagation();
});

$(document).on('click', '#popClose, #nodePopup .pop-close', function () { $('#nodePopup').fadeOut(150); });
$(document).on('click', function (e) {
    if (!$(e.target).closest('.node-card, #nodePopup').length) $('#nodePopup').fadeOut(150);
});
</script>
</body>
</html>