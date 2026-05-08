<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ATI-RTC1 - Organizational Chart</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; }

        .orgchart-page { padding: 28px 32px 80px; min-height: 100vh; background: #f4f6f9; }

        /* ── Header ── */
        .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
        .page-title  { font-size: 22px; font-weight: 700; color: #1a3a0f; display: flex; align-items: center; gap: 10px; }
        .page-title i { color: #296218; }
        .page-subtitle { font-size: 13px; color: #6c757d; margin-top: 3px; }
        .page-header-right { display: flex; align-items: center; gap: 10px; }

        .btn-back {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 9px 18px; background: white; border: 1.5px solid #296218;
            color: #296218; border-radius: 8px; text-decoration: none;
            font-size: 13px; font-weight: 600; transition: all .2s;
        }
        .btn-back:hover { background: #296218; color: white; }

        .btn-edit-mode {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 9px 18px; background: white; border: 1.5px solid #856404;
            color: #856404; border-radius: 8px; font-size: 13px;
            font-weight: 600; cursor: pointer; transition: all .2s;
        }
        .btn-edit-mode:hover, .btn-edit-mode.active { background: #856404; color: white; }

        /* ── Edit Banner ── */
        .edit-mode-banner {
            display: none; align-items: center; gap: 10px;
            background: #fff3cd; border: 1.5px solid #ffc107;
            border-radius: 10px; padding: 10px 18px; margin-bottom: 16px;
            font-size: 13px; color: #856404; font-weight: 600;
        }
        .edit-mode-banner.visible { display: flex; }

        /* ── Legend ── */
        .legend-bar {
            display: flex; align-items: center; gap: 20px;
            background: white; border-radius: 10px; padding: 11px 20px;
            margin-bottom: 20px; box-shadow: 0 1px 6px rgba(0,0,0,.06); flex-wrap: wrap;
        }
        .legend-label { font-size: 11px; font-weight: 700; color: #6c757d; text-transform: uppercase; letter-spacing: .5px; }
        .legend-item  { display: flex; align-items: center; gap: 6px; font-size: 12px; color: #444; font-weight: 500; }
        .legend-dot   { width: 11px; height: 11px; border-radius: 50%; flex-shrink: 0; }

        /* ── Filter ── */
        .filter-row { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; flex-wrap: wrap; }
        .search-org {
            display: flex; align-items: center; gap: 8px;
            background: white; border: 1.5px solid #dee2e6;
            border-radius: 8px; padding: 8px 14px;
            flex: 1; min-width: 200px; max-width: 300px; transition: border-color .2s;
        }
        .search-org:focus-within { border-color: #296218; }
        .search-org i { color: #999; font-size: 13px; }
        .search-org input { border: none; outline: none; font-size: 13px; color: #333; width: 100%; background: transparent; }
        .filter-unit {
            padding: 8px 14px; background: white; border: 1.5px solid #dee2e6;
            border-radius: 8px; font-size: 13px; color: #333; outline: none; cursor: pointer;
        }
        .filter-unit:focus { border-color: #296218; }

        /* ── Tree scroll ── */
        .chart-scroll-area { overflow-x: auto; padding: 10px 0 40px; }

        /* ── Tree container ── */
        .tree {
            display: flex; flex-direction: column; align-items: center;
            min-width: max-content; padding: 0 60px;
        }

        /* ── Connector lines ── */
        .conn-v      { width: 2px; background: #9dc48a; flex-shrink: 0; }
        .conn-h      { height: 2px; background: #9dc48a; }
        .conn-drop   { width: 2px; height: 22px; background: #9dc48a; flex-shrink: 0; }

        /* ── Level rows ── */
        .tree-level  { display: flex; justify-content: center; }

        /* ── Node card ── */
        .node-card {
            background: white; border-radius: 12px; border: 2px solid #d4e8cc;
            padding: 14px 16px 12px; width: 178px; text-align: center;
            box-shadow: 0 2px 10px rgba(41,98,24,.08);
            transition: all .22s ease; cursor: pointer; position: relative; flex-shrink: 0;
        }
        .node-card:hover { border-color: #296218; box-shadow: 0 6px 20px rgba(41,98,24,.18); transform: translateY(-3px); }
        .node-card.dimmed { opacity: .3; pointer-events: none; }
        .node-card.vacant { border-style: dashed; border-color: #ced4da; cursor: default; background: #fafafa; }
        .node-card.vacant:hover { transform: none; box-shadow: 0 2px 10px rgba(41,98,24,.08); border-color: #ced4da; }
        .node-card.is-section-head { border-color: #c8a200; box-shadow: 0 3px 14px rgba(200,162,0,.25); }

        .section-head-badge {
            position: absolute; top: -11px; left: 50%; transform: translateX(-50%);
            background: linear-gradient(135deg, #856404, #c8a200);
            color: white; font-size: 9px; font-weight: 800;
            text-transform: uppercase; letter-spacing: .5px;
            padding: 2px 10px; border-radius: 20px; white-space: nowrap;
            box-shadow: 0 2px 6px rgba(133,100,4,.3); display: none;
        }
        .node-card.is-section-head .section-head-badge { display: block; }

        .node-avatar { width: 46px; height: 46px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; font-size: 17px; color: white; font-weight: 700; }
        .node-name   { font-size: 13px; font-weight: 700; color: #1a3a0f; line-height: 1.3; margin-bottom: 3px; }
        .node-email  { font-size: 10px; color: #999; margin-bottom: 7px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        .role-pill        { display: inline-flex; align-items: center; gap: 4px; padding: 2px 9px; border-radius: 20px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .3px; }
        .role-admin       { background: #fff3cd; color: #856404; }
        .role-user        { background: #d4edda; color: #155724; }
        .role-requestor   { background: #d1ecf1; color: #0c5460; }

        .status-dot          { position: absolute; top: 10px; right: 10px; width: 9px; height: 9px; border-radius: 50%; border: 1.5px solid white; }
        .status-active       { background: #28a745; }
        .status-inactive     { background: #dc3545; }

        /* Director card slightly larger */
        .node-card.director-card { width: 200px; border-color: #a3c78e; box-shadow: 0 4px 18px rgba(41,98,24,.13); }
        .node-card.director-card .node-avatar { width: 54px; height: 54px; font-size: 20px; }
        .node-card.director-card .node-name   { font-size: 14px; }

        /* ── Unit banner ── */
        .unit-banner-card {
            background: linear-gradient(135deg, #1e5c14 0%, #296218 60%, #3d8a28 100%);
            color: white; border-radius: 10px; padding: 9px 20px;
            font-size: 11px; font-weight: 700; text-transform: uppercase;
            letter-spacing: .7px; display: flex; align-items: center; gap: 7px;
            white-space: nowrap; box-shadow: 0 3px 12px rgba(41,98,24,.25);
            min-width: 178px; justify-content: center;
        }

        /* ── Edit panels ── */
        .unit-edit-panel {
            display: none; margin-bottom: 10px; background: #fffdf0;
            border: 1.5px dashed #ffc107; border-radius: 10px;
            padding: 10px 12px; width: 200px;
        }
        .unit-edit-panel.visible { display: block; }
        .unit-edit-panel label   { font-size: 10px; font-weight: 700; color: #856404; text-transform: uppercase; letter-spacing: .4px; display: block; margin-bottom: 5px; }
        .head-select             { width: 100%; padding: 6px 8px; font-size: 12px; border: 1.5px solid #ffc107; border-radius: 7px; background: white; color: #333; outline: none; cursor: pointer; margin-bottom: 8px; }
        .head-select:focus       { border-color: #856404; }
        .btn-assign-head {
            width: 100%; padding: 6px; background: #856404; color: white; border: none;
            border-radius: 7px; font-size: 11px; font-weight: 700; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 5px; transition: background .2s;
        }
        .btn-assign-head:hover     { background: #6d5103; }
        .btn-assign-head:disabled  { background: #bbb; cursor: not-allowed; }

        .ocd-edit-panel {
            display: none; margin-bottom: 12px; background: #fffdf0;
            border: 1.5px dashed #ffc107; border-radius: 10px;
            padding: 12px 16px; width: 248px;
        }
        .ocd-edit-panel.visible { display: block; }
        .ocd-edit-title  { font-size: 10px; font-weight: 700; color: #856404; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 8px; display: flex; align-items: center; gap: 5px; }
        .ocd-edit-row    { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; }
        .ocd-edit-row label  { font-size: 11px; font-weight: 600; color: #555; min-width: 60px; }
        .ocd-edit-row select { flex: 1; padding: 5px 7px; font-size: 12px; border: 1.5px solid #ffc107; border-radius: 6px; background: white; outline: none; cursor: pointer; }
        .ocd-edit-row select:focus { border-color: #856404; }
        .btn-assign-ocd {
            width: 100%; padding: 7px; background: #856404; color: white; border: none;
            border-radius: 7px; font-size: 11px; font-weight: 700; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 6px; transition: background .2s;
        }
        .btn-assign-ocd:hover    { background: #6d5103; }
        .btn-assign-ocd:disabled { background: #bbb; cursor: not-allowed; }

        /* ── Head Preview Card (NEW) ── */
        .head-preview-container {
            margin: 4px 0 8px;
            min-height: 50px;
        }
        .head-preview-card {
            display: flex; align-items: center; gap: 9px;
            background: #fff; border: 1.5px solid #d4e8cc;
            border-radius: 8px; padding: 7px 9px;
            animation: headCardIn .2s ease;
            box-shadow: 0 2px 8px rgba(41,98,24,.07);
        }
        @keyframes headCardIn {
            from { opacity: 0; transform: translateY(-5px) scale(.97); }
            to   { opacity: 1; transform: none; }
        }
        .head-preview-avatar {
            width: 34px; height: 34px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 700; color: #fff; flex-shrink: 0;
        }
        .head-preview-info { flex: 1; min-width: 0; }
        .head-preview-name {
            font-size: 11px; font-weight: 700; color: #1a3a0f;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .head-preview-email {
            font-size: 10px; color: #999; margin-top: 1px;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .head-preview-badge {
            display: inline-flex; align-items: center; gap: 3px;
            margin-top: 3px; padding: 1px 7px; border-radius: 20px;
            font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: .4px;
            background: linear-gradient(135deg, #856404, #c8a200);
            color: #fff; box-shadow: 0 1px 4px rgba(133,100,4,.2);
        }
        .head-preview-empty {
            display: flex; align-items: center; gap: 7px;
            padding: 8px 10px; border: 1.5px dashed #ced4da;
            border-radius: 8px; color: #adb5bd;
            font-size: 11px; font-weight: 600;
            animation: headCardIn .2s ease;
        }
        .head-preview-empty i { font-size: 13px; opacity: .6; }

        /* ── Popup ── */
        .node-popup {
            display: none; position: fixed; z-index: 9999; background: white;
            border: 2px solid #296218; border-radius: 14px; padding: 20px; width: 280px;
            box-shadow: 0 10px 40px rgba(0,0,0,.18);
        }
        .node-popup .pop-name  { font-size: 15px; font-weight: 700; color: #1a3a0f; margin-bottom: 3px; }
        .node-popup .pop-email { font-size: 12px; color: #6c757d; margin-bottom: 12px; }
        .node-popup .pop-row   { display: flex; justify-content: space-between; font-size: 12px; padding: 5px 0; border-bottom: 1px solid #f0f0f0; color: #555; }
        .node-popup .pop-row:last-child { border-bottom: none; }
        .node-popup .pop-row strong { color: #333; }
        .pop-close       { position: absolute; top: 10px; right: 12px; cursor: pointer; color: #999; font-size: 16px; }
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

        /* Dimmed unit col */
        .unit-col-wrap.dimmed-col .node-card       { opacity: .3; pointer-events: none; }
        .unit-col-wrap.dimmed-col .unit-banner-card { opacity: .3; }
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
                <div>
                    <div class="page-title"><i class="fas fa-sitemap"></i> Organizational Chart</div>
                    <div class="page-subtitle">ATI Regional Training Center I — Staff Directory</div>
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
                Edit mode ON — use the dropdowns to assign a section head per unit.
                <span style="margin-left:auto;font-size:11px;font-weight:500;opacity:.8;">1 head per section.</span>
            </div>

            {{-- Legend --}}
            <div class="legend-bar">
                <span class="legend-label">Legend:</span>
                <div class="legend-item"><span class="legend-dot" style="background:#856404;"></span> Admin</div>
                <div class="legend-item"><span class="legend-dot" style="background:#296218;"></span> User</div>
                <div class="legend-item"><span class="legend-dot" style="background:#0c5460;"></span> Requestor</div>
                <div class="legend-item"><span class="legend-dot" style="background:#28a745;"></span> Active</div>
                <div class="legend-item"><span class="legend-dot" style="background:#dc3545;"></span> Inactive</div>
                <div class="legend-item" style="margin-left:auto;">
                    <span class="legend-dot" style="background:linear-gradient(135deg,#856404,#c8a200);"></span> Section Head

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
                    <option value="pme">Planning, Monitoring &amp; Evaluation</option>
                    <option value="admin">Administrative &amp; Finance</option>
                    <option value="cdm">Career Development &amp; Management</option>
                    <option value="pas">Partnership &amp; Accreditation</option>
                    <option value="iss">Information Services</option>
                </select>
            </div>

            @php
                $allUsers = $user ?? collect();
                $orgHeads = $orgHeads ?? [];

                $admins    = $allUsers->where('role', 'admin')->values();
                $director  = $admins->first();
                $assistant = $admins->count() > 1 ? $admins->get(1) : null;
                $nonAdmins = $allUsers->whereNotIn('id', $admins->pluck('id'))->values();

                $unitKeys = ['pme', 'admin', 'cdm', 'pas', 'iss'];
                $unitDefs = [
                    'pme'   => ['label' => 'Planning, Monitoring & Evaluation', 'icon' => 'fa-chart-line'],
                    'admin' => ['label' => 'Administrative & Finance',           'icon' => 'fa-briefcase'],
                    'cdm'   => ['label' => 'Career Dev. & Management',           'icon' => 'fa-graduation-cap'],
                    'pas'   => ['label' => 'Partnership & Accreditation',        'icon' => 'fa-handshake'],
                    'iss'   => ['label' => 'Information Services',               'icon' => 'fa-network-wired'],
                ];

                $units = [];
                foreach ($unitDefs as $k => $def) {
                    $units[$k] = array_merge($def, ['members' => collect()]);
                }
                foreach ($nonAdmins as $idx => $u) {
                    $key = $u->org_unit ?? $unitKeys[$idx % count($unitKeys)];
                    if (!array_key_exists($key, $units)) $key = $unitKeys[$idx % count($unitKeys)];
                    $units[$key]['members']->push($u);
                }

                if (!function_exists('orgInitials')) {
                    function orgInitials(string $name): string {
                        $parts = explode(' ', trim($name));
                        $i = strtoupper(substr($parts[0], 0, 1));
                        if (count($parts) > 1) $i .= strtoupper(substr(end($parts), 0, 1));
                        return $i;
                    }
                }
                $avatarColors = ['admin' => '#856404', 'user' => '#296218', 'requestor' => '#0c5460'];
            @endphp

            @if($allUsers->isEmpty())
                <div class="no-users-msg">
                    <i class="fas fa-users-slash"></i>
                    <h3>No users found</h3>
                    <p>Add users to populate the organizational chart.</p>
                </div>
            @else
            <div class="chart-scroll-area">
              <div class="tree" id="orgTree">

                {{-- ══════════════════════════════
                     ROW 1 — Center Director
                ══════════════════════════════ --}}
                <div class="tree-level" data-unit="ocd" style="flex-direction:column;align-items:center;">

                    {{-- OCD edit panel --}}
                    <div class="ocd-edit-panel" id="ocdEditPanel">
                        <div class="ocd-edit-title"><i class="fas fa-pen"></i> Assign OCD Director</div>
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
                            <i class="fas fa-save"></i> Save
                        </button>
                    </div>

                    {{-- OCD label banner --}}
                    <div style="background:linear-gradient(135deg,#1a3a0f,#296218);color:white;border-radius:10px;padding:10px 32px;font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:1px;display:inline-flex;align-items:center;gap:9px;box-shadow:0 4px 14px rgba(41,98,24,.25);white-space:nowrap;">
                        <i class="fas fa-building"></i> Office of Center Director
                    </div>

                    <div class="conn-v" style="height:28px;"></div>

                    {{-- Director card --}}
                    @if($director)
                        @php
                            $dr = $director;
                            $drRole   = $dr->role ?? 'admin';
                            $drColor  = $avatarColors[$drRole] ?? '#856404';
                            $drInit   = orgInitials($dr->name);
                            $drStatus = $dr->status ?? 'active';
                            $drHead   = (bool)$dr->is_section_head && ($orgHeads['ocd'] ?? null) == $dr->id;
                            $drPerms  = ['can_create'=>(bool)$dr->can_create,'can_read'=>(bool)$dr->can_read,'can_update'=>(bool)$dr->can_update,'can_delete'=>(bool)$dr->can_delete,'can_stock_in'=>(bool)$dr->can_stock_in,'can_stock_out'=>(bool)$dr->can_stock_out,'can_request'=>(bool)$dr->can_request];
                        @endphp
                        <div class="node-card director-card {{ $drHead ? 'is-section-head' : '' }}"
                             data-user-id="{{ $dr->id }}"
                             data-name="{{ $dr->name }}"
                             data-email="{{ $dr->email }}"
                             data-role="{{ ucfirst($drRole) }}"
                             data-status="{{ $drStatus }}"
                             data-date="{{ $dr->created_at ? $dr->created_at->format('M d, Y') : 'N/A' }}"
                             data-initials="{{ $drInit }}"
                             data-color="{{ $drColor }}"
                             data-perms='@json($drPerms)'>
                            <span class="section-head-badge"><i class="fas fa-crown" style="font-size:8px;margin-right:2px;"></i>Director</span>
                            <span class="status-dot status-{{ $drStatus }}"></span>
                            <div class="node-avatar" style="background:{{ $drColor }};">{{ $drInit }}</div>
                            <div class="node-name">{{ $dr->name }}</div>
                            <div class="node-email">{{ $dr->email }}</div>
                            <span class="role-pill role-{{ $drRole }}"><i class="fas fa-crown" style="font-size:9px;"></i> {{ ucfirst($drRole) }}</span>
                        </div>

                        @if($assistant)
                            @php
                                $as = $assistant;
                                $asRole   = $as->role ?? 'admin';
                                $asColor  = $avatarColors[$asRole] ?? '#856404';
                                $asInit   = orgInitials($as->name);
                                $asStatus = $as->status ?? 'active';
                                $asPerms  = ['can_create'=>(bool)$as->can_create,'can_read'=>(bool)$as->can_read,'can_update'=>(bool)$as->can_update,'can_delete'=>(bool)$as->can_delete,'can_stock_in'=>(bool)$as->can_stock_in,'can_stock_out'=>(bool)$as->can_stock_out,'can_request'=>(bool)$as->can_request];
                            @endphp
                            <div class="conn-v" style="height:20px;"></div>
                            <div style="font-size:10px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:.6px;margin-bottom:6px;">Asst. Director</div>
                            <div class="node-card director-card"
                                 data-user-id="{{ $as->id }}"
                                 data-name="{{ $as->name }}"
                                 data-email="{{ $as->email }}"
                                 data-role="{{ ucfirst($asRole) }}"
                                 data-status="{{ $asStatus }}"
                                 data-date="{{ $as->created_at ? $as->created_at->format('M d, Y') : 'N/A' }}"
                                 data-initials="{{ $asInit }}"
                                 data-color="{{ $asColor }}"
                                 data-perms='@json($asPerms)'>
                                <span class="status-dot status-{{ $asStatus }}"></span>
                                <div class="node-avatar" style="background:{{ $asColor }};">{{ $asInit }}</div>
                                <div class="node-name">{{ $as->name }}</div>
                                <div class="node-email">{{ $as->email }}</div>
                                <span class="role-pill role-{{ $asRole }}"><i class="fas fa-crown" style="font-size:9px;"></i> {{ ucfirst($asRole) }}</span>
                            </div>
                        @endif
                    @else
                        <div class="node-card director-card vacant">
                            <div class="node-avatar" style="background:#dee2e6;"><i class="fas fa-user-slash" style="color:#adb5bd;font-size:18px;"></i></div>
                            <div class="node-name" style="color:#adb5bd;">No Director Assigned</div>
                        </div>
                    @endif

                </div>{{-- /row 1 --}}

                {{-- ══════════════════════════════
                     Branch connector stem + bus
                ══════════════════════════════ --}}
                <div style="display:flex;flex-direction:column;align-items:center;width:100%;">
                    <div class="conn-v" style="height:32px;"></div>
                    <div id="hBus" style="height:2px;background:#9dc48a;width:0;transition:width .3s;"></div>
                </div>

                {{-- ══════════════════════════════
                     ROW 2 — Unit columns
                ══════════════════════════════ --}}
                <div class="tree-level" id="unitsLevel" style="align-items:flex-start;gap:0;">
                  @foreach($units as $unitKey => $unit)
                  <div class="unit-col-wrap" data-unit="{{ $unitKey }}"
                       style="display:flex;flex-direction:column;align-items:center;padding:0 18px;">

                    {{-- Drop from bus to banner --}}
                    <div class="conn-drop"></div>

                    {{-- ══════════════════════════════
                         UNIT EDIT PANEL (with preview)
                    ══════════════════════════════ --}}
                    <div class="unit-edit-panel" data-unit-panel="{{ $unitKey }}">
                        <label>
                            <i class="fas fa-crown" style="color:#c8a200;margin-right:3px;"></i>
                            Section Head
                        </label>

                        {{-- Dropdown: each option carries user data for JS card rendering --}}
                        <select class="head-select" data-unit-key="{{ $unitKey }}" id="headSelect_{{ $unitKey }}">
                            <option value="">— None —</option>
                            @foreach($allUsers as $u)
                                <option value="{{ $u->id }}"
                                        data-name="{{ $u->name }}"
                                        data-email="{{ $u->email ?? '' }}"
                                        data-role="{{ ucfirst($u->role ?? 'user') }}"
                                        data-color="{{ $avatarColors[$u->role ?? 'user'] ?? '#296218' }}"
                                        data-initials="{{ orgInitials($u->name) }}"
                                        {{ ($orgHeads[$unitKey] ?? null) == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }} ({{ ucfirst($u->role ?? 'user') }})
                                </option>
                            @endforeach
                        </select>

                        {{-- Preview container: server-rendered on first load, JS-updated on change --}}
                        <div class="head-preview-container" id="headPreview_{{ $unitKey }}">
                            @php
                                $currentHeadId = $orgHeads[$unitKey] ?? null;
                                $currentHead   = $currentHeadId
                                    ? $allUsers->firstWhere('id', $currentHeadId)
                                    : null;
                            @endphp

                            @if($currentHead)
                                @php
                                    $ph      = $currentHead;
                                    $phRole  = $ph->role ?? 'user';
                                    $phColor = $avatarColors[$phRole] ?? '#296218';
                                    $phInit  = orgInitials($ph->name);
                                @endphp
                                <div class="head-preview-card">
                                    <div class="head-preview-avatar" style="background:{{ $phColor }};">
                                        {{ $phInit }}
                                    </div>
                                    <div class="head-preview-info">
                                        <div class="head-preview-name">{{ $ph->name }}</div>
                                        @if($ph->email)
                                            <div class="head-preview-email">{{ $ph->email }}</div>
                                        @endif
                                        <span class="head-preview-badge">
                                            <i class="fas fa-crown" style="font-size:8px;"></i> Section Head
                                        </span>
                                    </div>
                                </div>
                            @else
                                <div class="head-preview-empty">
                                    <i class="fas fa-user-slash"></i>
                                    <span>No staff assigned</span>
                                </div>
                            @endif
                        </div>
                        {{-- /head-preview-container --}}

                        <button class="btn-assign-head" data-unit-key="{{ $unitKey }}">
                            <i class="fas fa-save"></i> Save Head
                        </button>
                    </div>
                    {{-- /unit-edit-panel --}}

                    {{-- Unit banner --}}
                    <div class="unit-banner-card">
                        <i class="fas {{ $unit['icon'] }}"></i>
                        {{ $unit['label'] }}
                    </div>

                    {{-- Stem from banner to members --}}
                    <div class="conn-v" style="height:22px;"></div>

                    {{-- Member cards --}}
                    @php $members = $unit['members']; $mCount = $members->count(); @endphp

                    @if($mCount > 1)
                        {{-- Multiple members: horizontal sub-bus + drop per member --}}
                        <div id="mBus_{{ $unitKey }}" style="height:2px;background:#9dc48a;width:0;"></div>
                        <div style="display:flex;gap:0;" id="mRow_{{ $unitKey }}">
                          @foreach($members as $u)
                            @php
                                $mr = $u->role ?? 'user';
                                $mc = $avatarColors[$mr] ?? '#296218';
                                $mi = orgInitials($u->name);
                                $ms = $u->status ?? 'active';
                                $mh = (bool)$u->is_section_head && ($orgHeads[$unitKey] ?? null) == $u->id;
                                $mp = ['can_create'=>(bool)$u->can_create,'can_read'=>(bool)$u->can_read,'can_update'=>(bool)$u->can_update,'can_delete'=>(bool)$u->can_delete,'can_stock_in'=>(bool)$u->can_stock_in,'can_stock_out'=>(bool)$u->can_stock_out,'can_request'=>(bool)$u->can_request];
                            @endphp
                            <div style="display:flex;flex-direction:column;align-items:center;padding:0 10px;">
                                <div class="conn-drop"></div>
                                <div class="node-card {{ $mh ? 'is-section-head' : '' }}"
                                     data-user-id="{{ $u->id }}" data-name="{{ $u->name }}"
                                     data-email="{{ $u->email }}" data-role="{{ ucfirst($mr) }}"
                                     data-status="{{ $ms }}"
                                     data-date="{{ $u->created_at ? $u->created_at->format('M d, Y') : 'N/A' }}"
                                     data-initials="{{ $mi }}" data-color="{{ $mc }}"
                                     data-perms='@json($mp)'>
                                    <span class="section-head-badge"><i class="fas fa-crown" style="font-size:8px;margin-right:2px;"></i>Section Head</span>
                                    <span class="status-dot status-{{ $ms }}"></span>
                                    <div class="node-avatar" style="background:{{ $mc }};">{{ $mi }}</div>
                                    <div class="node-name">{{ $u->name }}</div>
                                    <div class="node-email">{{ $u->email }}</div>
                                    @if($mr==='admin') <span class="role-pill role-admin"><i class="fas fa-crown" style="font-size:9px;"></i> Admin</span>
                                    @elseif($mr==='requestor') <span class="role-pill role-requestor"><i class="fas fa-file-alt" style="font-size:9px;"></i> Requestor</span>
                                    @else <span class="role-pill role-user"><i class="fas fa-user" style="font-size:9px;"></i> User</span>
                                    @endif
                                </div>
                            </div>
                          @endforeach
                        </div>

                    @elseif($mCount === 1)
                        @php
                            $u  = $members->first();
                            $mr = $u->role ?? 'user';
                            $mc = $avatarColors[$mr] ?? '#296218';
                            $mi = orgInitials($u->name);
                            $ms = $u->status ?? 'active';
                            $mh = (bool)$u->is_section_head && ($orgHeads[$unitKey] ?? null) == $u->id;
                            $mp = ['can_create'=>(bool)$u->can_create,'can_read'=>(bool)$u->can_read,'can_update'=>(bool)$u->can_update,'can_delete'=>(bool)$u->can_delete,'can_stock_in'=>(bool)$u->can_stock_in,'can_stock_out'=>(bool)$u->can_stock_out,'can_request'=>(bool)$u->can_request];
                        @endphp
                        <div class="node-card {{ $mh ? 'is-section-head' : '' }}"
                             data-user-id="{{ $u->id }}" data-name="{{ $u->name }}"
                             data-email="{{ $u->email }}" data-role="{{ ucfirst($mr) }}"
                             data-status="{{ $ms }}"
                             data-date="{{ $u->created_at ? $u->created_at->format('M d, Y') : 'N/A' }}"
                             data-initials="{{ $mi }}" data-color="{{ $mc }}"
                             data-perms='@json($mp)'>
                            <span class="section-head-badge"><i class="fas fa-crown" style="font-size:8px;margin-right:2px;"></i>Section Head</span>
                            <span class="status-dot status-{{ $ms }}"></span>
                            <div class="node-avatar" style="background:{{ $mc }};">{{ $mi }}</div>
                            <div class="node-name">{{ $u->name }}</div>
                            <div class="node-email">{{ $u->email }}</div>
                            @if($mr==='admin') <span class="role-pill role-admin"><i class="fas fa-crown" style="font-size:9px;"></i> Admin</span>
                            @elseif($mr==='requestor') <span class="role-pill role-requestor"><i class="fas fa-file-alt" style="font-size:9px;"></i> Requestor</span>
                            @else <span class="role-pill role-user"><i class="fas fa-user" style="font-size:9px;"></i> User</span>
                            @endif
                        </div>

                    @else
                        <div class="node-card vacant">
                            <div class="node-avatar" style="background:#e9ecef;"><i class="fas fa-user-slash" style="color:#adb5bd;font-size:16px;"></i></div>
                            <div class="node-name" style="color:#adb5bd;font-size:12px;">No staff assigned</div>
                        </div>
                    @endif

                  </div>{{-- /unit-col-wrap --}}
                  @endforeach
                </div>{{-- /row 2 --}}

              </div>{{-- /tree --}}
            </div>{{-- /chart-scroll-area --}}
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
    <div class="pop-row"><span>Role</span>         <strong id="popRole"></strong></div>
    <div class="pop-row"><span>Status</span>       <strong id="popStatus"></strong></div>
    <div class="pop-row"><span>Section Head</span> <strong id="popHead"></strong></div>
    <div class="pop-row"><span>Member since</span> <strong id="popDate"></strong></div>
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

/* ══════════════════════════════════════════════
   Draw the top-level horizontal bus line that
   spans from the leftmost to rightmost unit col.
   Also draws sub-bus lines for multi-member cols.
══════════════════════════════════════════════ */
function drawBuses() {
    var $cols = $('.unit-col-wrap');
    if (!$cols.length) return;

    // Top bus (level 1 → level 2)
    var busEl    = document.getElementById('hBus');
    var busParent = busEl.parentElement.getBoundingClientRect();
    var first    = $cols.first()[0].getBoundingClientRect();
    var last     = $cols.last()[0].getBoundingClientRect();
    var leftEdge  = first.left + first.width / 2 - busParent.left;
    var rightEdge = last.left  + last.width  / 2 - busParent.left;
    $(busEl).css({ width: (rightEdge - leftEdge) + 'px', marginLeft: leftEdge + 'px' });

    // Sub-buses (banner → multiple members)
    $cols.each(function () {
        var unitKey = $(this).data('unit');
        var $mRow   = $('#mRow_' + unitKey);
        var $mBus   = $('#mBus_' + unitKey);
        if (!$mRow.length || !$mBus.length) return;

        var children = $mRow.children();
        if (children.length < 2) { $mBus.hide(); return; }

        var rowRect    = $mRow[0].getBoundingClientRect();
        var firstChild = children.first()[0].getBoundingClientRect();
        var lastChild  = children.last()[0].getBoundingClientRect();
        var mLeft  = firstChild.left  + firstChild.width  / 2 - rowRect.left;
        var mRight = lastChild.left   + lastChild.width   / 2 - rowRect.left;

        $mBus.css({ width: (mRight - mLeft) + 'px', marginLeft: mLeft + 'px', display: 'block' });
    });
}

$(window).on('load resize', drawBuses);
setTimeout(drawBuses, 120);

/* ══════════════════════════════════════════════
   Toast
══════════════════════════════════════════════ */
function showToast(msg, isError) {
    isError = isError || false;
    $('#orgToastMsg').text(msg);
    $('#orgToastIcon').attr('class', isError ? 'fas fa-exclamation-circle' : 'fas fa-check-circle');
    $('#orgToast').toggleClass('error', isError).addClass('show');
    setTimeout(function () { $('#orgToast').removeClass('show'); }, 3500);
}

/* ══════════════════════════════════════════════
   Edit Mode
══════════════════════════════════════════════ */
var editMode = false;
$('#btnEditMode').on('click', function () {
    editMode = !editMode;
    $(this).toggleClass('active', editMode);
    $(this).html(editMode
        ? '<i class="fas fa-xmark"></i> Exit Edit'
        : '<i class="fas fa-pen-to-square"></i> Edit Chart');
    $('#editModeBanner').toggleClass('visible', editMode);
    $('.unit-edit-panel').toggleClass('visible', editMode);
    $('#ocdEditPanel').toggleClass('visible', editMode);
    if (editMode) { $('#nodePopup').fadeOut(100); }
    setTimeout(drawBuses, 60);
});

/* ══════════════════════════════════════════════
   Assign head — save then reload
══════════════════════════════════════════════ */
function doAssignHead(unit, userId, $btn, resetLabel) {
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving…');
    $.ajax({
        url: '{{ route("orgchart.assignHead") }}',
        method: 'POST',
        data: { unit: unit, user_id: userId },
        success: function (res) {
            showToast(res.message);
            setTimeout(function () { location.reload(); }, 1200);
        },
        error: function (xhr) {
            showToast((xhr.responseJSON && xhr.responseJSON.error) || 'Failed to save.', true);
            $btn.prop('disabled', false).html(resetLabel);
        }
    });
}

$(document).on('click', '.btn-assign-head', function () {
    var unitKey = $(this).data('unit-key');
    var userId  = $('.head-select[data-unit-key="' + unitKey + '"]').val() || null;
    doAssignHead(unitKey, userId, $(this), '<i class="fas fa-save"></i> Save Head');
});

$('#btnAssignOcd').on('click', function () {
    var userId = $('#selectOcdDirector').val() || null;
    doAssignHead('ocd', userId, $(this), '<i class="fas fa-save"></i> Save');
});

/* ══════════════════════════════════════════════
   Search & Filter
══════════════════════════════════════════════ */
$('#orgSearch').on('input', filterChart);
$('#unitFilter').on('change', filterChart);

function filterChart() {
    var q    = $('#orgSearch').val().toLowerCase().trim();
    var unit = $('#unitFilter').val();

    $('.node-card').removeClass('dimmed');
    $('.unit-col-wrap').removeClass('dimmed-col');

    if (q) {
        $('.node-card:not(.vacant)').each(function () {
            var name  = ($(this).data('name')  || '').toLowerCase();
            var email = ($(this).data('email') || '').toLowerCase();
            if (!name.includes(q) && !email.includes(q)) $(this).addClass('dimmed');
        });
    }

    if (unit) {
        $('.unit-col-wrap').each(function () {
            if ($(this).data('unit') !== unit) $(this).addClass('dimmed-col');
        });
    }
}

/* ══════════════════════════════════════════════
   Node Popup
══════════════════════════════════════════════ */
$(document).on('click', '.node-card:not(.vacant)', function (e) {
    if (editMode) return;
    var c = $(this);

    $('#popAvatar').text(c.data('initials')).css('background', c.data('color'));
    $('#popName').text(c.data('name'));
    $('#popEmail').text(c.data('email'));
    $('#popRole').text(c.data('role'));

    var status = c.data('status');
    $('#popStatus').html(status === 'active'
        ? '<span style="color:#28a745;font-weight:700;">● Active</span>'
        : '<span style="color:#dc3545;font-weight:700;">● Inactive</span>');

    $('#popHead').html(c.hasClass('is-section-head')
        ? '<span style="color:#856404;font-weight:700;"><i class="fas fa-crown" style="font-size:10px;"></i> Yes</span>'
        : '<span style="color:#aaa;">No</span>');

    $('#popDate').text(c.data('date'));

    var perms  = JSON.parse(c.attr('data-perms') || '{}');
    var labels = { can_create:'Create', can_read:'Read', can_update:'Update',
                   can_delete:'Delete', can_stock_in:'Stock In',
                   can_stock_out:'Stock Out', can_request:'Request' };
    var html = '';
    $.each(labels, function (k, l) {
        html += '<span class="perm-chip ' + (perms[k] ? 'perm-on' : 'perm-off') + '">' + l + '</span>';
    });
    $('#popPerms').html(html);

    var rect = this.getBoundingClientRect();
    var pw = 280, ph = 350;
    var left = rect.right + 12, top = rect.top;
    if (left + pw > window.innerWidth)  left = rect.left - pw - 12;
    if (top  + ph > window.innerHeight) top  = window.innerHeight - ph - 12;
    $('#nodePopup').css({ top: Math.max(8, top), left: Math.max(8, left) }).fadeIn(180);
    e.stopPropagation();
});

$(document).on('click', '.pop-close', function () { $('#nodePopup').fadeOut(150); });
$(document).on('click', function (e) {
    if (!$(e.target).closest('.node-card, #nodePopup').length) $('#nodePopup').fadeOut(150);
});

/* ══════════════════════════════════════════════
   Section Head Dropdown — Dynamic Preview
   ──────────────────────────────────────────────
   Renders a mini user card (or empty placeholder)
   below the head-select dropdown instantly on
   change, with no page reload or AJAX call.
══════════════════════════════════════════════ */

/**
 * Build HTML for the user card shown in the preview container.
 * All data comes from the <option> data attributes set by Blade.
 *
 * @param {{ name:string, email:string, role:string, color:string, initials:string }} user
 * @returns {string}
 */
function buildHeadPreviewCard(user) {
    var safeName     = $('<div>').text(user.name).html();
    var safeInitials = $('<div>').text(user.initials).html();
    var emailHtml    = user.email
        ? '<div class="head-preview-email">' + $('<div>').text(user.email).html() + '</div>'
        : '';

    return '<div class="head-preview-card">'
        +   '<div class="head-preview-avatar" style="background:' + user.color + ';">' + safeInitials + '</div>'
        +   '<div class="head-preview-info">'
        +     '<div class="head-preview-name">' + safeName + '</div>'
        +     emailHtml
        +     '<span class="head-preview-badge">'
        +       '<i style="font-size:8px;"></i> Section Head'
        +     '</span>'
        +   '</div>'
        + '</div>';
}

/**
 * Build HTML for the "None" / empty placeholder.
 *
 * @returns {string}
 */
function buildHeadPreviewEmpty() {
    return '<div class="head-preview-empty">'
        + '<i class="fas fa-user-slash"></i>'
        + '<span>No staff assigned</span>'
        + '</div>';
}

/**
 * Swap the preview container content for the given unit.
 *
 * @param {string} unitKey  e.g. 'pme'
 * @param {string} userId   selected option value; empty string = None
 */
function updateHeadPreview(unitKey, userId) {
    var $container = $('#headPreview_' + unitKey);
    if (!$container.length) return;

    if (!userId) {
        $container.html(buildHeadPreviewEmpty());
        return;
    }

    var $opt = $('#headSelect_' + unitKey + ' option[value="' + userId + '"]');
    if (!$opt.length) {
        $container.html(buildHeadPreviewEmpty());
        return;
    }

    var rawName = $opt.data('name') || $opt.text();
    var user = {
        name:     rawName,
        email:    $opt.data('email')    || '',
        role:     $opt.data('role')     || 'User',
        color:    $opt.data('color')    || '#296218',
        initials: $opt.data('initials') || rawName.charAt(0).toUpperCase()
    };

    $container.html(buildHeadPreviewCard(user));
}

/* ── Change listener — fires for every unit automatically ── */
$(document).on('change', '.head-select', function () {
    updateHeadPreview($(this).data('unit-key'), $(this).val());
});

/* ── On page load: sync any dropdown that has no server-rendered preview yet ── */
$(function () {
    $('.head-select').each(function () {
        var unitKey    = $(this).data('unit-key');
        var $container = $('#headPreview_' + unitKey);
        // Only replace if Blade left the container empty (edge-case guard)
        if ($container.length && $container.children().length === 0) {
            updateHeadPreview(unitKey, $(this).val());
        }
    });
});
</script>
</body>
</html>