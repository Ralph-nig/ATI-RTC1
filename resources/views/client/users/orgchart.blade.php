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
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        .orgchart-page {
            padding: 28px 32px 60px;
            min-height: 100vh;
            background: #f4f6f9;
            font-family: 'Segoe UI', sans-serif;
        }

        /* ── Page Header ── */
        .page-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 28px; flex-wrap: wrap; gap: 12px;
        }
        .page-header-left { display: flex; align-items: center; gap: 14px; }
        .page-header-right { display: flex; align-items: center; gap: 10px; }
        .page-title { font-size: 22px; font-weight: 700; color: #1a3a0f; display: flex; align-items: center; gap: 10px; }
        .page-title i { color: #296218; }
        .page-subtitle { font-size: 13px; color: #6c757d; margin-top: 3px; }

        .btn-back {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 9px 18px; background: white; border: 1.5px solid #296218;
            color: #296218; border-radius: 8px; text-decoration: none;
            font-size: 13px; font-weight: 600; transition: all .2s;
        }
        .btn-back:hover { background: #296218; color: white; }

        /* ── Edit Mode Toggle ── */
        .btn-edit-mode {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 9px 18px; background: white; border: 1.5px solid #856404;
            color: #856404; border-radius: 8px; font-size: 13px;
            font-weight: 600; cursor: pointer; transition: all .2s;
        }
        .btn-edit-mode:hover,
        .btn-edit-mode.active { background: #856404; color: white; }

        /* ── Edit Mode Banner ── */
        .edit-mode-banner {
            display: none; align-items: center; gap: 10px;
            background: #fff3cd; border: 1.5px solid #ffc107;
            border-radius: 10px; padding: 10px 18px; margin-bottom: 18px;
            font-size: 13px; color: #856404; font-weight: 600;
        }
        .edit-mode-banner.visible { display: flex; }

        /* ── Legend ── */
        .legend-bar {
            display: flex; align-items: center; gap: 20px;
            background: white; border-radius: 10px; padding: 12px 20px;
            margin-bottom: 24px; box-shadow: 0 1px 6px rgba(0,0,0,.07); flex-wrap: wrap;
        }
        .legend-label { font-size: 12px; font-weight: 700; color: #6c757d; text-transform: uppercase; letter-spacing: .5px; margin-right: 4px; }
        .legend-item { display: flex; align-items: center; gap: 7px; font-size: 12px; color: #444; font-weight: 500; }
        .legend-dot { width: 12px; height: 12px; border-radius: 50%; }

        /* ── Filter & Search ── */
        .filter-row { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; flex-wrap: wrap; }
        .search-org {
            display: flex; align-items: center; gap: 8px;
            background: white; border: 1.5px solid #dee2e6;
            border-radius: 8px; padding: 8px 14px;
            flex: 1; min-width: 200px; max-width: 320px; transition: border-color .2s;
        }
        .search-org:focus-within { border-color: #296218; }
        .search-org i { color: #999; font-size: 13px; }
        .search-org input { border: none; outline: none; font-size: 13px; color: #333; width: 100%; background: transparent; }
        .filter-unit {
            padding: 8px 14px; background: white; border: 1.5px solid #dee2e6;
            border-radius: 8px; font-size: 13px; color: #333; outline: none; cursor: pointer; transition: border-color .2s;
        }
        .filter-unit:focus { border-color: #296218; }

        /* ── Chart ── */
        .chart-scroll-area { overflow-x: auto; padding-bottom: 20px; }
        .org-chart { display: flex; flex-direction: column; align-items: center; gap: 0; min-width: 900px; }

        .v-line { width: 2px; background: #c3d9bb; margin: 0 auto; }
        .top-v   { height: 28px; }
        .short-v { height: 18px; }

        .units-row { display: flex; justify-content: center; gap: 40px; width: 100%; flex-wrap: wrap; }
        .unit-col  { display: flex; flex-direction: column; align-items: center; }
        .unit-members { display: flex; flex-direction: column; align-items: center; gap: 12px; }

        /* ── Unit Banner ── */
        .unit-banner {
            background: linear-gradient(135deg, #296218 0%, #3d8a28 100%);
            color: white; border-radius: 10px; padding: 8px 20px;
            font-size: 11px; font-weight: 700; text-transform: uppercase;
            letter-spacing: .8px; margin-bottom: 14px;
            display: flex; align-items: center; gap: 7px;
            white-space: nowrap; box-shadow: 0 3px 10px rgba(41,98,24,.2);
        }

        /* ── Node Card ── */
        .node-card {
            background: white; border-radius: 12px; border: 2px solid #d4e8cc;
            padding: 14px 16px 12px; width: 170px; text-align: center;
            box-shadow: 0 3px 12px rgba(41,98,24,.08);
            transition: all .25s ease; cursor: pointer; position: relative;
        }
        .node-card:hover { border-color: #296218; box-shadow: 0 6px 20px rgba(41,98,24,.18); transform: translateY(-3px); }
        .node-card.dimmed { opacity: .35; pointer-events: none; }
        .node-card.vacant { border-style: dashed; border-color: #adb5bd; cursor: default; }

        /* Section Head styling */
        .node-card.is-section-head { border-color: #c8a200; box-shadow: 0 3px 14px rgba(200,162,0,.22); }
        .section-head-badge {
            position: absolute; top: -11px; left: 50%; transform: translateX(-50%);
            background: linear-gradient(135deg, #856404, #c8a200);
            color: white; font-size: 9px; font-weight: 800;
            text-transform: uppercase; letter-spacing: .5px;
            padding: 2px 10px; border-radius: 20px; white-space: nowrap;
            box-shadow: 0 2px 6px rgba(133,100,4,.3); display: none;
        }
        .node-card.is-section-head .section-head-badge { display: block; }

        .node-avatar {
            width: 46px; height: 46px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 8px; font-size: 18px; color: white; font-weight: 700;
        }
        .node-name  { font-size: 13px; font-weight: 700; color: #1a3a0f; line-height: 1.3; margin-bottom: 4px; }
        .node-email { font-size: 10px; color: #888; margin-bottom: 6px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        .role-pill { display: inline-flex; align-items: center; gap: 4px; padding: 2px 9px; border-radius: 20px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .3px; }
        .role-admin     { background: #fff3cd; color: #856404; }
        .role-user      { background: #d4edda; color: #155724; }
        .role-requestor { background: #d1ecf1; color: #0c5460; }

        .status-dot { position: absolute; top: 10px; right: 10px; width: 9px; height: 9px; border-radius: 50%; border: 1.5px solid white; }
        .status-active   { background: #28a745; }
        .status-inactive { background: #dc3545; }

        /* ── Edit Panel per unit ── */
        .unit-edit-panel {
            display: none; margin-bottom: 14px; background: #fffdf0;
            border: 1.5px dashed #ffc107; border-radius: 10px;
            padding: 10px 14px; width: 100%; max-width: 220px;
        }
        .unit-edit-panel.visible { display: block; }
        .unit-edit-panel label { font-size: 10px; font-weight: 700; color: #856404; text-transform: uppercase; letter-spacing: .4px; display: block; margin-bottom: 5px; }
        .head-select { width: 100%; padding: 6px 10px; font-size: 12px; border: 1.5px solid #ffc107; border-radius: 7px; background: white; color: #333; outline: none; cursor: pointer; margin-bottom: 8px; }
        .head-select:focus { border-color: #856404; }
        .btn-assign-head {
            width: 100%; padding: 6px; background: #856404; color: white; border: none;
            border-radius: 7px; font-size: 11px; font-weight: 700; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 5px; transition: background .2s;
        }
        .btn-assign-head:hover { background: #6d5103; }
        .btn-assign-head:disabled { background: #aaa; cursor: not-allowed; }

        /* ── OCD wrapper ── */
        .ocd-wrapper {
            display: flex; flex-direction: column; align-items: center;
            background: white; border: 2px solid #c3d9bb; border-radius: 16px;
            padding: 20px 32px 24px; box-shadow: 0 4px 18px rgba(41,98,24,.10); position: relative;
        }
        .ocd-banner {
            background: linear-gradient(135deg, #1a3a0f 0%, #296218 100%);
            color: white; border-radius: 10px; padding: 10px 28px;
            font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;
            display: inline-flex; align-items: center; gap: 9px;
            box-shadow: 0 4px 14px rgba(41,98,24,.25);
        }
        .asst-label { font-size: 10px; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: .6px; }
        .top-row { display: flex; flex-direction: column; align-items: center; }

        /* ── OCD edit panel ── */
        .ocd-edit-panel {
            display: none; margin-top: 14px; background: #fffdf0;
            border: 1.5px dashed #ffc107; border-radius: 10px;
            padding: 12px 18px; width: 100%; max-width: 340px;
        }
        .ocd-edit-panel.visible { display: block; }
        .ocd-edit-title { font-size: 11px; font-weight: 700; color: #856404; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 10px; display: flex; align-items: center; gap: 6px; }
        .ocd-edit-row { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; }
        .ocd-edit-row label { font-size: 11px; font-weight: 600; color: #555; min-width: 80px; }
        .ocd-edit-row select { flex: 1; padding: 5px 8px; font-size: 12px; border: 1.5px solid #ffc107; border-radius: 6px; background: white; outline: none; cursor: pointer; }
        .ocd-edit-row select:focus { border-color: #856404; }
        .btn-assign-ocd {
            margin-top: 4px; width: 100%; padding: 7px; background: #856404;
            color: white; border: none; border-radius: 7px; font-size: 11px; font-weight: 700;
            cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: background .2s;
        }
        .btn-assign-ocd:hover { background: #6d5103; }

        /* ── Popup ── */
        .node-popup {
            display: none; position: fixed; z-index: 9999; background: white;
            border: 2px solid #296218; border-radius: 14px; padding: 20px; width: 280px;
            box-shadow: 0 10px 40px rgba(0,0,0,.18);
        }
        .node-popup .pop-name  { font-size: 15px; font-weight: 700; color: #1a3a0f; margin-bottom: 4px; }
        .node-popup .pop-email { font-size: 12px; color: #6c757d; margin-bottom: 12px; }
        .node-popup .pop-row   { display: flex; justify-content: space-between; font-size: 12px; padding: 5px 0; border-bottom: 1px solid #f0f0f0; color: #555; }
        .node-popup .pop-row:last-child { border-bottom: none; }
        .node-popup .pop-row strong { color: #333; }
        .pop-close { position: absolute; top: 10px; right: 12px; cursor: pointer; color: #999; font-size: 16px; }
        .pop-close:hover { color: #296218; }
        .perm-chips { display: flex; flex-wrap: wrap; gap: 5px; margin-top: 8px; }
        .perm-chip  { font-size: 10px; padding: 2px 8px; border-radius: 20px; font-weight: 600; }
        .perm-on  { background: #d4edda; color: #155724; }
        .perm-off { background: #f0f0f0; color: #999; text-decoration: line-through; }

        /* ── Toast ── */
        .org-toast {
            position: fixed; bottom: 28px; right: 28px;
            background: #1a3a0f; color: white; padding: 13px 20px;
            border-radius: 10px; font-size: 13px; font-weight: 600;
            box-shadow: 0 6px 24px rgba(0,0,0,.2); display: none;
            align-items: center; gap: 10px; z-index: 99999; max-width: 360px;
        }
        .org-toast.show  { display: flex; }
        .org-toast.error { background: #7d1a1a; }

        .no-users-msg { text-align: center; padding: 60px 20px; color: #6c757d; }
        .no-users-msg i { font-size: 48px; opacity: .4; margin-bottom: 16px; display: block; }

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

            {{-- Page Header --}}
            <div class="page-header">
                <div class="page-header-left">
                    <div>
                        <div class="page-title"><i class="fas fa-sitemap"></i> Organizational Chart</div>
                        <div class="page-subtitle">ATI Regional Training Center I — Staff Directory</div>
                    </div>
                </div>
                <div class="page-header-right">
                    <button class="btn-edit-mode" id="btnEditMode">
                        <i class="fas fa-pen-to-square"></i> Edit Chart
                    </button>
                    <a href="{{ route('users.index') }}" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Back to Users
                    </a>
                </div>
            </div>

            {{-- Edit Mode Banner --}}
            <div class="edit-mode-banner" id="editModeBanner">
                <i class="fas fa-pen-to-square"></i>
                Edit mode is ON — use the dropdowns on each section to assign a section head.
                <span style="margin-left:auto;font-size:11px;font-weight:500;opacity:.8;">Only 1 head per section allowed.</span>
            </div>

            {{-- Legend --}}
            <div class="legend-bar">
                <span class="legend-label">Legend:</span>
                <div class="legend-item"><span class="legend-dot" style="background:#856404;border:2px solid #fff3cd;"></span> Admin</div>
                <div class="legend-item"><span class="legend-dot" style="background:#296218;border:2px solid #d4edda;"></span> User</div>
                <div class="legend-item"><span class="legend-dot" style="background:#0c5460;border:2px solid #d1ecf1;"></span> Requestor</div>
                <div class="legend-item"><span class="legend-dot" style="background:#28a745;"></span> Active</div>
                <div class="legend-item"><span class="legend-dot" style="background:#dc3545;"></span> Inactive</div>
                <div class="legend-item" style="margin-left:auto;">
                    <span class="legend-dot" style="background:linear-gradient(135deg,#856404,#c8a200);"></span> Section Head
                </div>
            </div>

            {{-- Search / Filter --}}
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

            @php
                $allUsers  = $user ?? collect();
                $orgHeads  = $orgHeads ?? [];   // ['pme' => 7, 'ocd' => 1, ...]

                // Admins go to OCD
                $admins    = $allUsers->where('role', 'admin')->values();
                $director  = $admins->first();
                $assistant = $admins->count() > 1 ? $admins->get(1) : null;

                $nonAdmins = $allUsers->whereNotIn('id', $admins->pluck('id'));

                // Group remaining users by their org_unit (fallback round-robin)
                $unitKeys = ['pme', 'admin', 'cdm', 'pas', 'iss'];
                $units = [
                    'pme'   => ['label' => 'Planning, Monitoring & Evaluation Unit',  'icon' => 'fa-chart-line',    'members' => collect()],
                    'admin' => ['label' => 'Administrative & Finance Unit',            'icon' => 'fa-briefcase',     'members' => collect()],
                    'cdm'   => ['label' => 'Career Development & Management Section',  'icon' => 'fa-graduation-cap','members' => collect()],
                    'pas'   => ['label' => 'Partnership & Accreditation Section',      'icon' => 'fa-handshake',     'members' => collect()],
                    'iss'   => ['label' => 'Information Services Section',             'icon' => 'fa-network-wired', 'members' => collect()],
                ];

                foreach ($nonAdmins as $index => $u) {
                    $key = $u->org_unit ?? $unitKeys[$index % count($unitKeys)];
                    if (!array_key_exists($key, $units)) { $key = $unitKeys[$index % count($unitKeys)]; }
                    $units[$key]['members']->push($u);
                }

                function orgInitials(string $name): string {
                    $parts = explode(' ', trim($name));
                    $i = strtoupper(substr($parts[0], 0, 1));
                    if (count($parts) > 1) { $i .= strtoupper(substr(end($parts), 0, 1)); }
                    return $i;
                }

                $avatarColors = ['admin' => '#856404', 'user' => '#296218', 'requestor' => '#0c5460'];
            @endphp

            <div class="chart-scroll-area">
              <div class="org-chart" id="orgChart">

                {{-- OCD --}}
                <div class="ocd-wrapper" data-unit="ocd">
                    <div class="ocd-banner">Office of Center Director</div>

                    @if($director)
                        <div class="v-line" style="height:20px;"></div>
                        <div class="top-row">
                            @include('client.users.orgchart._node', ['u' => $director, 'avatarColors' => $avatarColors, 'headMap' => $orgHeads, 'headKey' => 'ocd'])
                        </div>
                    @endif

                    @if($assistant)
                        <div class="v-line" style="height:16px;"></div>
                        <div class="asst-label">Asst. Center Director</div>
                        <div class="v-line" style="height:10px;"></div>
                        <div class="top-row">
                            @include('client.users.orgchart._node', ['u' => $assistant, 'avatarColors' => $avatarColors, 'headMap' => $orgHeads, 'headKey' => 'ocd'])
                        </div>
                    @endif

                    {{-- OCD edit panel --}}
                    <div class="ocd-edit-panel" id="ocdEditPanel">
                        <div class="ocd-edit-title"><i class="fas fa-pen"></i> Assign OCD Positions</div>
                        <div class="ocd-edit-row">
                            <label>Director</label>
                            <select id="selectOcdDirector">
                                <option value="">— None —</option>
                                @foreach($allUsers as $u)
                                    <option value="{{ $u->id }}" {{ ($orgHeads['ocd'] ?? null) == $u->id ? 'selected' : '' }}>
                                        {{ $u->name }} ({{ ucfirst($u->role) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button class="btn-assign-ocd" id="btnAssignOcd">
                            <i class="fas fa-save"></i> Save OCD Assignment
                        </button>
                    </div>
                </div>

                <div class="v-line" style="height:28px;"></div>
                <div style="width:85%;height:2px;background:#c3d9bb;margin:0 auto;"></div>

                {{-- Unit columns --}}
                <div class="units-row" id="unitsRow">
                    @foreach($units as $unitKey => $unit)
                    <div class="unit-col" data-unit="{{ $unitKey }}">
                        <div class="v-line short-v"></div>
                        <div class="unit-banner">
                            <i class="fas {{ $unit['icon'] }}"></i>
                            {{ Str::limit($unit['label'], 30) }}
                        </div>

                        {{-- Edit panel --}}
                        <div class="unit-edit-panel" data-unit-panel="{{ $unitKey }}">
                            <label><i class="fas fa-crown"></i> Section Head</label>
                            <select class="head-select" data-unit-key="{{ $unitKey }}">
                                <option value="">— None —</option>
                                @foreach($allUsers as $u)
                                    <option value="{{ $u->id }}"
                                        {{ ($orgHeads[$unitKey] ?? null) == $u->id ? 'selected' : '' }}>
                                        {{ $u->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button class="btn-assign-head" data-unit-key="{{ $unitKey }}">
                                <i class="fas fa-save"></i> Save Head
                            </button>
                        </div>

                        <div class="unit-members">
                            @forelse($unit['members'] as $u)
                                @include('client.users.orgchart._node', [
                                    'u'            => $u,
                                    'avatarColors' => $avatarColors,
                                    'headMap'      => $orgHeads,
                                    'headKey'      => $unitKey,
                                ])
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

              </div>
            </div>

            @if($allUsers->isEmpty())
            <div class="no-users-msg">
                <i class="fas fa-users-slash"></i>
                <h3>No users found</h3>
                <p>Add users to populate the organizational chart.</p>
            </div>
            @endif

        </div>
    </div>
</div>

{{-- Popup --}}
<div class="node-popup" id="nodePopup">
    <span class="pop-close"><i class="fas fa-times"></i></span>
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
        <div id="popAvatar" style="width:44px;height:44px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;color:white;flex-shrink:0;"></div>
        <div>
            <div class="pop-name"  id="popName"></div>
            <div class="pop-email" id="popEmail"></div>
        </div>
    </div>
    <div class="pop-row"><span>Role</span>        <strong id="popRole"></strong></div>
    <div class="pop-row"><span>Status</span>      <strong id="popStatus"></strong></div>
    <div class="pop-row"><span>Section Head</span><strong id="popHead"></strong></div>
    <div class="pop-row"><span>Member since</span><strong id="popDate"></strong></div>
    <div style="margin-top:10px;font-size:11px;font-weight:700;color:#6c757d;text-transform:uppercase;letter-spacing:.4px;">Permissions</div>
    <div class="perm-chips" id="popPerms"></div>
</div>

{{-- Toast --}}
<div class="org-toast" id="orgToast">
    <i id="orgToastIcon" class="fas fa-check-circle"></i>
    <span id="orgToastMsg"></span>
</div>

@include('layouts.core.footer')

<script>
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

/* ── Toast ── */
function showToast(msg, isError = false) {
    $('#orgToastMsg').text(msg);
    $('#orgToastIcon').attr('class', isError ? 'fas fa-exclamation-circle' : 'fas fa-check-circle');
    $('#orgToast').toggleClass('error', isError).addClass('show');
    setTimeout(() => $('#orgToast').removeClass('show'), 3500);
}

/* ── Edit Mode ── */
let editMode = false;
$('#btnEditMode').on('click', function () {
    editMode = !editMode;
    $(this).toggleClass('active', editMode);
    // Update icon and label
    $(this).html(editMode
        ? '<i class="fas fa-xmark"></i> Exit Edit'
        : '<i class="fas fa-pen-to-square"></i> Edit Chart');
    $('#editModeBanner').toggleClass('visible', editMode);
    $('.unit-edit-panel').toggleClass('visible', editMode);
    $('#ocdEditPanel').toggleClass('visible', editMode);
    if (editMode) $('#nodePopup').fadeOut(100);
});

/* ── Assign section head (unit columns) ── */
$(document).on('click', '.btn-assign-head', function () {
    const unitKey = $(this).data('unit-key');
    const userId  = $(`.head-select[data-unit-key="${unitKey}"]`).val() || null;
    const $btn    = $(this);

    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving…');

    $.ajax({
        url: '{{ route("orgchart.assignHead") }}',
        method: 'POST',
        data: { unit: unitKey, user_id: userId },
        success(res) {
            refreshUnitHead(unitKey, userId);
            showToast(res.message);
        },
        error(xhr) {
            showToast(xhr.responseJSON?.error || 'Failed to save.', true);
        },
        complete() {
            $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Save Head');
        }
    });
});

/* ── Assign OCD director ── */
$('#btnAssignOcd').on('click', function () {
    const userId = $('#selectOcdDirector').val() || null;
    const $btn   = $(this);
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving…');

    $.ajax({
        url: '{{ route("orgchart.assignHead") }}',
        method: 'POST',
        data: { unit: 'ocd', user_id: userId },
        success(res) {
            showToast(res.message);
        },
        error(xhr) {
            showToast(xhr.responseJSON?.error || 'Failed to save OCD assignment.', true);
        },
        complete() {
            $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Save OCD Assignment');
        }
    });
});

/* ── Refresh head badge client-side (no reload) ── */
function refreshUnitHead(unitKey, userId) {
    const $col = $(`.unit-col[data-unit="${unitKey}"]`);
    $col.find('.node-card').removeClass('is-section-head');
    $col.find('.section-head-badge').hide();
    if (userId) {
        $col.find(`.node-card[data-user-id="${userId}"]`)
            .addClass('is-section-head')
            .find('.section-head-badge').show();
    }
}

/* ── Search & Filter ── */
$('#orgSearch').on('input', filterChart);
$('#unitFilter').on('change', filterChart);

function filterChart() {
    const q    = $('#orgSearch').val().toLowerCase().trim();
    const unit = $('#unitFilter').val();

    $('.node-card').each(function () {
        const name  = ($(this).data('name')  || '').toLowerCase();
        const email = ($(this).data('email') || '').toLowerCase();
        $(this).toggleClass('dimmed', !!q && !name.includes(q) && !email.includes(q));
    });

    if (unit) {
        $('[data-unit]').not(`[data-unit="${unit}"]`).addClass('dimmed');
        $(`[data-unit="${unit}"]`).find('.node-card').removeClass('dimmed');
    } else if (!q) {
        $('.node-card').removeClass('dimmed');
    }
}

/* ── Node Popup ── */
$(document).on('click', '.node-card:not(.vacant)', function (e) {
    if (editMode) return;
    const c = $(this);

    $('#popAvatar').text(c.data('initials')).css('background', c.data('color'));
    $('#popName').text(c.data('name'));
    $('#popEmail').text(c.data('email'));
    $('#popRole').text(c.data('role'));

    const status = c.data('status');
    $('#popStatus').html(status === 'active'
        ? '<span style="color:#28a745;font-weight:700;">● Active</span>'
        : '<span style="color:#dc3545;font-weight:700;">● Inactive</span>');

    $('#popHead').html(c.hasClass('is-section-head')
        ? '<span style="color:#856404;font-weight:700;"><i class="fas fa-crown" style="font-size:10px;"></i> Yes</span>'
        : '<span style="color:#aaa;">No</span>');

    $('#popDate').text(c.data('date'));

    const perms  = JSON.parse(c.attr('data-perms') || '{}');
    const labels = { can_create:'Create', can_read:'Read', can_update:'Update',
                     can_delete:'Delete', can_stock_in:'Stock In',
                     can_stock_out:'Stock Out', can_request:'Request' };
    let html = '';
    $.each(labels, (k, l) => { html += `<span class="perm-chip ${perms[k] ? 'perm-on' : 'perm-off'}">${l}</span>`; });
    $('#popPerms').html(html);

    const rect = this.getBoundingClientRect();
    const pw = 280, ph = 350;
    let left = rect.right + 12, top = rect.top;
    if (left + pw > window.innerWidth)  left = rect.left - pw - 12;
    if (top  + ph > window.innerHeight) top  = window.innerHeight - ph - 12;
    $('#nodePopup').css({ top: Math.max(8, top), left: Math.max(8, left) }).fadeIn(180);
    e.stopPropagation();
});

$(document).on('click', '.pop-close', () => $('#nodePopup').fadeOut(150));
$(document).on('click', e => {
    if (!$(e.target).closest('.node-card, #nodePopup').length) $('#nodePopup').fadeOut(150);
});
</script>
</body>
</html>