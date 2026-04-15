<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Equipment</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/supplies.css') }}">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* ── Page shell ── */
        .my-equip-page {
            padding: 0 16px 40px;
            box-sizing: border-box;
            max-width: 100%;
            overflow-x: hidden;
        }

        /* ── Hero banner ── */
        .my-equip-hero {
            background: linear-gradient(135deg, #1a3a0f 0%, #296218 60%, #3d8a28 100%);
            border-radius: 0;
            padding: 28px 30px 26px;
            margin-bottom: 24px;
            margin-left: -16px;   /* cancel the .my-equip-page padding */
            margin-right: -16px;  /* cancel the .my-equip-page padding */
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
            position: relative;
            overflow: hidden;
        }
        .my-equip-hero::before {
            content: '';
            position: absolute;
            right: -60px; top: -60px;
            width: 260px; height: 260px;
            border-radius: 50%;
            border: 55px solid rgba(255,255,255,.06);
            pointer-events: none;
        }
        .my-equip-hero::after {
            content: '';
            position: absolute;
            right: 60px; bottom: -80px;
            width: 200px; height: 200px;
            border-radius: 50%;
            border: 40px solid rgba(255,255,255,.04);
            pointer-events: none;
        }

        .hero-left { position: relative; z-index: 1; }
        .hero-sub {
            font-size: 11px;
            color: rgba(255,255,255,.5);
            text-transform: uppercase;
            letter-spacing: 1.2px;
            margin-bottom: 6px;
        }
        .hero-title {
            font-size: 24px;
            font-weight: 700;
            color: #fff;
            margin: 0 0 4px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .hero-name {
            font-size: 13px;
            color: rgba(255,255,255,.6);
        }

        /* ── Stat chips in hero ── */
        .hero-stats {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            position: relative;
            z-index: 1;
        }
        .hero-stat {
            background: rgba(255,255,255,.1);
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 12px;
            padding: 14px 22px;
            text-align: center;
            min-width: 80px;
        }
        .hero-stat-val {
            display: block;
            font-size: 26px;
            font-weight: 700;
            color: #fff;
            line-height: 1;
        }
        .hero-stat-lbl {
            display: block;
            font-size: 10px;
            color: rgba(255,255,255,.5);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 5px;
        }
        .hero-stat.stat-serviceable .hero-stat-val { color: #7effa0; }
        .hero-stat.stat-unserviceable .hero-stat-val { color: #ff9b9b; }

        /* ── Toolbar ── */
        .my-equip-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .toolbar-left {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .me-search {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            border: 1.5px solid #dee2e6;
            border-radius: 8px;
            padding: 8px 14px;
            transition: border-color .2s;
            min-width: 220px;
        }
        .me-search:focus-within { border-color: #296218; }
        .me-search i { color: #aaa; font-size: 13px; }
        .me-search input {
            border: none; outline: none;
            font-size: 13px; color: #333;
            width: 100%; background: transparent;
        }
        .me-filter {
            padding: 9px 14px;
            background: #fff;
            border: 1.5px solid #dee2e6;
            border-radius: 8px;
            font-size: 13px;
            color: #333;
            outline: none;
            cursor: pointer;
            transition: border-color .2s;
        }
        .me-filter:focus { border-color: #296218; }

        /* View toggle */
        .view-toggle {
            display: flex;
            background: #fff;
            border: 1.5px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
        }
        .view-btn {
            padding: 8px 13px;
            background: transparent;
            border: none;
            cursor: pointer;
            color: #aaa;
            font-size: 14px;
            transition: all .2s;
        }
        .view-btn.active { background: #296218; color: #fff; }

        /* ── Grid view ── */
        .equip-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
            gap: 16px;
        }
        .equip-card {
            background: #fff;
            border-radius: 14px;
            border: 1.5px solid #e9ecef;
            overflow: hidden;
            transition: all .25s ease;
            position: relative;
        }
        .equip-card:hover {
            border-color: #296218;
            box-shadow: 0 6px 24px rgba(41,98,24,.12);
            transform: translateY(-3px);
        }
        .equip-card-stripe {
            height: 5px;
            background: linear-gradient(90deg, #296218, #5cb85c);
        }
        .equip-card-stripe.unserviceable {
            background: linear-gradient(90deg, #dc3545, #e77);
        }
        .equip-card-body {
            padding: 18px 18px 14px;
        }
        .equip-card-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 12px;
            gap: 8px;
        }
        .equip-icon-wrap {
            width: 44px; height: 44px;
            border-radius: 10px;
            background: #f0faf0;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; color: #296218;
            flex-shrink: 0;
        }
        .equip-icon-wrap.unserviceable {
            background: #fff0f0; color: #dc3545;
        }
        .equip-card-article {
            font-size: 14px; font-weight: 700; color: #1a3a0f;
            line-height: 1.3;
        }
        .equip-card-prop {
            font-size: 11px; color: #888; margin-top: 2px;
            font-family: monospace;
        }
        .equip-card-badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 9px; border-radius: 20px;
            font-size: 10px; font-weight: 700; white-space: nowrap;
            flex-shrink: 0;
        }
        .badge-svc  { background: #d4edda; color: #155724; }
        .badge-unsvc { background: #f8d7da; color: #721c24; }

        .equip-card-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-bottom: 14px;
        }
        .meta-item label {
            display: block; font-size: 10px;
            color: #aaa; text-transform: uppercase;
            letter-spacing: .4px; margin-bottom: 2px;
        }
        .meta-item span {
            font-size: 12px; font-weight: 600; color: #444;
        }
        .meta-item.full { grid-column: 1 / -1; }

        .equip-card-footer {
            border-top: 1px solid #f0f0f0;
            padding-top: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .card-acq-date {
            font-size: 11px; color: #999;
            display: flex; align-items: center; gap: 5px;
        }
        .btn-card-view {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 5px 13px;
            background: #296218; color: #fff;
            border-radius: 7px; font-size: 12px; font-weight: 600;
            text-decoration: none; border: none; cursor: pointer;
            transition: background .2s;
        }
        .btn-card-view:hover { background: #1a3a0f; color: #fff; }

        /* ── List / Table view ── */
        .equip-table-wrap {
            background: #fff;
            border-radius: 14px;
            border: 1.5px solid #e9ecef;
            overflow: hidden;
        }
        .equip-table {
            width: 100%;
            border-collapse: collapse;
        }
        .equip-table thead tr {
            background: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
        }
        .equip-table th {
            padding: 12px 16px;
            font-size: 11px;
            font-weight: 700;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: .5px;
            text-align: left;
            white-space: nowrap;
        }
        .equip-table td {
            padding: 13px 16px;
            font-size: 13px;
            color: #444;
            border-bottom: 1px solid #f4f4f4;
            vertical-align: middle;
        }
        .equip-table tbody tr:hover { background: #fafff9; }
        .equip-table tbody tr:last-child td { border-bottom: none; }

        .tbl-article-cell { display: flex; align-items: center; gap: 10px; }
        .tbl-icon {
            width: 34px; height: 34px; border-radius: 8px;
            background: #f0faf0; display: flex; align-items: center;
            justify-content: center; color: #296218; font-size: 14px;
            flex-shrink: 0;
        }
        .tbl-icon.unsvc { background: #fff0f0; color: #dc3545; }
        .tbl-article-name { font-weight: 600; color: #1a3a0f; font-size: 13px; }
        .tbl-prop-num { font-size: 11px; color: #888; font-family: monospace; }

        /* ── Empty state ── */
        .my-equip-empty {
            text-align: center;
            padding: 70px 20px;
            color: #adb5bd;
        }
        .my-equip-empty i {
            font-size: 56px;
            display: block;
            margin-bottom: 16px;
            opacity: .4;
        }
        .my-equip-empty h3 { font-size: 18px; color: #6c757d; margin-bottom: 6px; }
        .my-equip-empty p  { font-size: 13px; }

        /* ── Detail modal ── */
        .eq-detail-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.5);
            z-index: 9998;
            align-items: center;
            justify-content: center;
            padding: 16px;
            box-sizing: border-box;
        }
        .eq-detail-overlay.open { display: flex; }
        .eq-detail-modal {
            background: #fff;
            border-radius: 18px;
            width: 100%;
            max-width: 580px;
            max-height: 88vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0,0,0,.25);
            box-sizing: border-box;
        }
        .eq-detail-header {
            background: linear-gradient(135deg, #1a3a0f, #296218);
            padding: 22px 24px;
            border-radius: 18px 18px 0 0;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }
        .eq-detail-header h3 {
            color: #fff; font-size: 17px; font-weight: 700; margin: 0 0 4px;
            word-break: break-word;
        }
        .eq-detail-header p {
            color: rgba(255,255,255,.6); font-size: 12px; margin: 0;
            font-family: monospace;
            word-break: break-all;
        }
        .eq-modal-close {
            background: rgba(255,255,255,.15);
            border: none; border-radius: 50%;
            width: 32px; height: 32px;
            color: #fff; font-size: 16px;
            cursor: pointer; display: flex;
            align-items: center; justify-content: center;
            flex-shrink: 0; transition: background .2s;
        }
        .eq-modal-close:hover { background: rgba(255,255,255,.28); }
        .eq-detail-body {
            padding: 24px;
        }
        .eq-detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 20px;
        }
        .eq-detail-field label {
            display: block; font-size: 10px;
            font-weight: 700; color: #aaa;
            text-transform: uppercase; letter-spacing: .5px;
            margin-bottom: 4px;
        }
        .eq-detail-field span {
            font-size: 14px; font-weight: 600; color: #333;
        }
        .eq-detail-field.full { grid-column: 1 / -1; }
        .eq-detail-divider {
            height: 1px; background: #f0f0f0; margin: 16px 0;
        }
        .eq-detail-remarks {
            background: #f8f9fa; border-radius: 10px;
            padding: 14px; font-size: 13px; color: #555;
            line-height: 1.6;
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .my-equip-page { padding: 0 12px 40px; overflow-x: hidden; }
            .my-equip-hero { padding: 18px 16px; border-radius: 12px; box-sizing: border-box; }
            .hero-title { font-size: 18px; }
            .equip-grid { grid-template-columns: 1fr; }
            .equip-card { width: 100%; box-sizing: border-box; min-width: 0; }
            .hero-stats { gap: 8px; }
            .hero-stat { padding: 10px 14px; min-width: 64px; }
            .hero-stat-val { font-size: 20px; }

            /* Toolbar stacks vertically */
            .my-equip-toolbar {
                flex-direction: column;
                align-items: stretch;
            }
            .toolbar-left {
                flex-direction: column;
                align-items: stretch;
            }
            .me-search {
                min-width: unset;
                width: 100%;
                box-sizing: border-box;
            }
            .me-filter {
                width: 100%;
                box-sizing: border-box;
            }

            /* Card meta single column */
            .equip-card-meta {
                grid-template-columns: 1fr;
            }

            /* Table horizontal scroll */
            .equip-table-wrap {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            /* Modal fixes */
            .eq-detail-overlay {
                padding: 8px;
                align-items: flex-end;
            }
            .eq-detail-modal {
                max-width: 100%;
                max-height: 92vh;
                border-radius: 16px;
                margin: 0;
            }
            .eq-detail-header {
                padding: 16px;
                border-radius: 16px 16px 0 0;
            }
            .eq-detail-header h3 {
                font-size: 14px;
            }
            .eq-detail-body {
                padding: 16px;
            }
            .eq-detail-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }
            .eq-detail-field.full {
                grid-column: 1;
            }
        }

        @media (max-width: 480px) {
            .hero-title { font-size: 16px; }
            .hero-stat { padding: 8px 10px; min-width: 54px; }
            .hero-stat-val { font-size: 18px; }
            .hero-stat-lbl { font-size: 9px; }
            .equip-card-top {
                flex-wrap: wrap;
            }
        }
    </style>
</head>
<body>
<div class="container">
    @include('layouts.core.sidebar')
    <div class="details">
        @include('layouts.core.header')
        @include('layouts.core.footer')

        <div class="supplies-container my-equip-page">

            @php
                $me          = auth()->user();
                $myEquipment = \App\Models\Equipment::where('responsible_person', $me->name)
                                ->orderBy('article')
                                ->get();

                $total        = $myEquipment->count();
                $serviceable  = $myEquipment->where('condition', 'Serviceable')->count();
                $unserviceable = $myEquipment->where('condition', 'Unserviceable')->count();

                $totalValue   = $myEquipment->sum('unit_value');

                function equipIcon(string $article): string {
                    $a = strtolower($article);
                    foreach ([
                        'laptop'    => 'fa-laptop',
                        'computer'  => 'fa-desktop',
                        'printer'   => 'fa-print',
                        'phone'     => 'fa-phone',
                        'camera'    => 'fa-camera',
                        'chair'     => 'fa-chair',
                        'table'     => 'fa-table',
                        'projector' => 'fa-video',
                        'monitor'   => 'fa-tv',
                        'server'    => 'fa-server',
                        'scanner'   => 'fa-barcode',
                        'vehicle'   => 'fa-car',
                        'air'       => 'fa-wind',
                        'ref'       => 'fa-snowflake',
                    ] as $keyword => $icon) {
                        if (str_contains($a, $keyword)) return $icon;
                    }
                    return 'fa-box';
                }
            @endphp

            {{-- ── Hero ── --}}
            <div class="my-equip-hero">
                <div class="hero-left">
                    <p class="hero-sub">Assigned to you</p>
                    <h2 class="hero-title">
                        <i class="fas fa-boxes"></i>
                        My Equipment
                    </h2>
                    <p class="hero-name">
                        <i class="fas fa-user" style="margin-right:5px;"></i>
                        {{ $me->name }}
                        &nbsp;·&nbsp;
                        Total value: <strong style="color:#7effa0;">₱{{ number_format($totalValue, 2) }}</strong>
                    </p>
                </div>
                <div class="hero-stats">
                    <div class="hero-stat">
                        <span class="hero-stat-val">{{ $total }}</span>
                        <span class="hero-stat-lbl">Total</span>
                    </div>  
                    <div class="hero-stat stat-serviceable">
                        <span class="hero-stat-val">{{ $serviceable }}</span>
                        <span class="hero-stat-lbl">Serviceable</span>
                    </div>
                    <div class="hero-stat stat-unserviceable">
                        <span class="hero-stat-val">{{ $unserviceable }}</span>
                        <span class="hero-stat-lbl">Unserviceable</span>
                    </div>
                </div>
            </div>

            {{-- ── Alerts ── --}}
            @if($unserviceable > 0)
            <div class="alert" style="background:#fff3cd;border:1px solid #ffc107;color:#856404;border-radius:10px;padding:12px 18px;margin-bottom:18px;display:flex;align-items:center;gap:10px;font-size:13px;">
                <i class="fas fa-exclamation-triangle"></i>
                You have <strong>{{ $unserviceable }}</strong> unserviceable item{{ $unserviceable > 1 ? 's' : '' }} assigned to you. Please coordinate with the admin for disposal or replacement.
            </div>
            @endif

            {{-- ── Toolbar ── --}}
            <div class="my-equip-toolbar">
                <div class="toolbar-left">
                    <div class="me-search">
                        <i class="fas fa-search"></i>
                        <input type="text" id="meSearch" placeholder="Search article, property no…">
                    </div>
                    <select class="me-filter" id="meCondFilter">
                        <option value="">All Conditions</option>
                        <option value="Serviceable">Serviceable</option>
                        <option value="Unserviceable">Unserviceable</option>
                    </select>
                    <select class="me-filter" id="meDocFilter">
                        <option value="">All Doc Types</option>
                        <option value="ICS">ICS</option>
                        <option value="PAR">PAR</option>
                    </select>
                </div>
                <div class="view-toggle">
                    <button class="view-btn active" id="btnGrid" title="Card view">
                        <i class="fas fa-th-large"></i>
                    </button>
                    <button class="view-btn" id="btnList" title="List view">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>

            {{-- ── No equipment ── --}}
            @if($myEquipment->isEmpty())
            <div class="my-equip-empty">
                <i class="fas fa-box-open"></i>
                <h3>No equipment assigned</h3>
                <p>You don't have any equipment assigned to your name yet.</p>
            </div>
            @else

            {{-- ══ GRID VIEW ══ --}}
            <div id="gridView" class="equip-grid">
                @foreach($myEquipment as $eq)
                @php
                    $svc  = $eq->condition === 'Serviceable';
                    $icon = equipIcon($eq->article);
                @endphp
                <div class="equip-card"
                     data-article="{{ strtolower($eq->article) }}"
                     data-prop="{{ strtolower($eq->property_number) }}"
                     data-condition="{{ $eq->condition }}"
                     data-doctype="{{ $eq->document_type ?? '' }}">
                    <div class="equip-card-stripe {{ $svc ? '' : 'unserviceable' }}"></div>
                    <div class="equip-card-body">
                        <div class="equip-card-top">
                            <div style="display:flex;align-items:flex-start;gap:10px;flex:1;min-width:0;">
                                <div class="equip-icon-wrap {{ $svc ? '' : 'unserviceable' }}">
                                    <i class="fas {{ $icon }}"></i>
                                </div>
                                <div style="min-width:0;">
                                    <div class="equip-card-article" title="{{ $eq->article }}">
                                        {{ Str::limit($eq->article, 34) }}
                                    </div>
                                    <div class="equip-card-prop">{{ $eq->property_number }}</div>
                                </div>
                            </div>
                            <span class="equip-card-badge {{ $svc ? 'badge-svc' : 'badge-unsvc' }}">
                                <i class="fas fa-{{ $svc ? 'check-circle' : 'times-circle' }}"></i>
                                {{ $eq->condition }}
                            </span>
                        </div>

                        <div class="equip-card-meta">
                            <div class="meta-item">
                                <label>Doc Type</label>
                                <span>{{ $eq->document_type ?? '—' }}</span>
                            </div>
                            <div class="meta-item">
                                <label>Unit Value</label>
                                <span>₱{{ number_format($eq->unit_value, 2) }}</span>
                            </div>
                            <div class="meta-item">
                                <label>Unit of Measure</label>
                                <span>{{ $eq->unit_of_measurement }}</span>
                            </div>
                            <div class="meta-item">
                                <label>Responsibility Ctr</label>
                                <span>{{ $eq->responsibility_center ?? '—' }}</span>
                            </div>
                            @if($eq->classification)
                            <div class="meta-item full">
                                <label>Classification</label>
                                <span>{{ $eq->classification }}</span>
                            </div>
                            @endif
                        </div>

                        <div class="equip-card-footer">
                            <span class="card-acq-date">
                                <i class="fas fa-calendar-alt"></i>
                                {{ $eq->acquisition_date ? \Carbon\Carbon::parse($eq->acquisition_date)->format('M d, Y') : 'N/A' }}
                            </span>
                            <button class="btn-card-view" onclick="openDetail({{ $eq->id }})">
                                <i class="fas fa-eye"></i> Details
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- ══ LIST / TABLE VIEW ══ --}}
            <div id="listView" class="equip-table-wrap" style="display:none;">
                <table class="equip-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Article</th>
                            <th>Doc Type</th>
                            <th>Unit Value</th>
                            <th>Unit of Measure</th>
                            <th>Responsibility Ctr</th>
                            <th>Acq. Date</th>
                            <th>Condition</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @foreach($myEquipment as $idx => $eq)
                        @php $svc = $eq->condition === 'Serviceable'; @endphp
                        <tr class="equip-row"
                            data-article="{{ strtolower($eq->article) }}"
                            data-prop="{{ strtolower($eq->property_number) }}"
                            data-condition="{{ $eq->condition }}"
                            data-doctype="{{ $eq->document_type ?? '' }}">
                            <td style="color:#999;font-size:12px;">{{ $idx + 1 }}</td>
                            <td>
                                <div class="tbl-article-cell">
                                    <div class="tbl-icon {{ $svc ? '' : 'unsvc' }}">
                                        <i class="fas {{ equipIcon($eq->article) }}"></i>
                                    </div>
                                    <div>
                                        <div class="tbl-article-name">{{ $eq->article }}</div>
                                        <div class="tbl-prop-num">{{ $eq->property_number }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="background:#e9ecef;padding:2px 9px;border-radius:5px;font-size:11px;font-weight:700;color:#495057;">
                                    {{ $eq->document_type ?? '—' }}
                                </span>
                            </td>
                            <td style="font-weight:600;">₱{{ number_format($eq->unit_value, 2) }}</td>
                            <td>{{ $eq->unit_of_measurement }}</td>
                            <td>{{ $eq->responsibility_center ?? '—' }}</td>
                            <td style="font-size:12px;color:#888;">
                                {{ $eq->acquisition_date ? \Carbon\Carbon::parse($eq->acquisition_date)->format('M d, Y') : '—' }}
                            </td>
                            <td>
                                <span class="equip-card-badge {{ $svc ? 'badge-svc' : 'badge-unsvc' }}">
                                    <i class="fas fa-{{ $svc ? 'check' : 'times' }}-circle"></i>
                                    {{ $eq->condition }}
                                </span>
                            </td>
                            <td>
                                <button class="btn-card-view" onclick="openDetail({{ $eq->id }})">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div id="listEmpty" style="display:none;text-align:center;padding:40px;color:#aaa;">
                    <i class="fas fa-search" style="font-size:32px;margin-bottom:12px;opacity:.4;display:block;"></i>
                    No equipment matches your search.
                </div>
            </div>

            {{-- No-results message for grid --}}
            <div id="gridEmpty" style="display:none;text-align:center;padding:60px;color:#aaa;">
                <i class="fas fa-search" style="font-size:36px;margin-bottom:14px;opacity:.4;display:block;"></i>
                No equipment matches your search.
            </div>

            @endif {{-- end myEquipment not empty --}}
        </div>{{-- /supplies-container --}}
    </div>{{-- /details --}}
</div>{{-- /container --}}

{{-- ── Detail Modal ── --}}
<div class="eq-detail-overlay" id="detailOverlay">
    <div class="eq-detail-modal">
        <div class="eq-detail-header">
            <div style="min-width:0;flex:1;">
                <h3 id="modalArticle">—</h3>
                <p id="modalPropNum">—</p>
            </div>
            <button class="eq-modal-close" id="modalClose">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="eq-detail-body">
            <div class="eq-detail-grid">
                <div class="eq-detail-field">
                    <label>Document Type</label>
                    <span id="modalDocType">—</span>
                </div>
                <div class="eq-detail-field">
                    <label>Document Number</label>
                    <span id="modalDocNum" style="font-family:monospace;word-break:break-all;">—</span>
                </div>
                <div class="eq-detail-field">
                    <label>Classification</label>
                    <span id="modalClass">—</span>
                </div>
                <div class="eq-detail-field">
                    <label>Unit of Measurement</label>
                    <span id="modalUoM">—</span>
                </div>
                <div class="eq-detail-field">
                    <label>Unit Value</label>
                    <span id="modalValue" style="color:#296218;font-size:16px;">—</span>
                </div>
                <div class="eq-detail-field">
                    <label>Quantity</label>
                    <span id="modalQty">—</span>
                </div>
                <div class="eq-detail-field">
                    <label>Condition</label>
                    <span id="modalCondition">—</span>
                </div>
                <div class="eq-detail-field">
                    <label>Acquisition Date</label>
                    <span id="modalAcqDate">—</span>
                </div>
                <!-- <div class="eq-detail-field">
                    <label>Location</label>
                    <span id="modalLocation">—</span>
                </div> -->
                <div class="eq-detail-field">
                    <label>Responsibility Center</label>
                    <span id="modalRespCenter">—</span>
                </div>
                <div class="eq-detail-field full">
                    <label>Description</label>
                    <span id="modalDesc">—</span>
                </div>
            </div>
            <div class="eq-detail-divider"></div>
            <div>
                <div style="font-size:10px;font-weight:700;color:#aaa;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">Remarks</div>
                <div class="eq-detail-remarks" id="modalRemarks">None</div>
            </div>
            <div id="modalDisposalSection" style="display:none;margin-top:14px;">
                <div class="eq-detail-divider"></div>
                <div style="background:#fff3cd;border:1px solid #ffc107;border-radius:10px;padding:14px;">
                    <div style="font-size:11px;font-weight:700;color:#856404;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">
                        <i class="fas fa-recycle"></i> Disposal Information
                    </div>
                    <div style="font-size:13px;color:#856404;">
                        Method: <strong id="modalDisposalMethod">—</strong>
                        <span id="modalDisposalDetailsWrap"> &mdash; <span id="modalDisposalDetails"></span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@php
    $equipmentJson = $myEquipment->map(function($e) {
        return [
            'id'                  => $e->id,
            'article'             => $e->article,
            'property_number'     => $e->property_number,
            'document_type'       => $e->document_type ?? '—',
            'document_number'     => $e->document_number ?? '—',
            'classification'      => $e->classification ?? '—',
            'unit_of_measurement' => $e->unit_of_measurement,
            'unit_value'          => number_format($e->unit_value, 2),
            'quantity'            => $e->quantity ?? 1,
            'condition'           => $e->condition,
            'acquisition_date'    => $e->acquisition_date
                                        ? \Carbon\Carbon::parse($e->acquisition_date)->format('F d, Y')
                                        : 'N/A',
            'location'            => $e->responsibility_center ?? '—',
            'responsibility_center' => $e->responsibility_center ?? '—',
            'description'         => $e->description ?? '—',
            'remarks'             => $e->remarks ?? 'None',
            'disposal_method'     => $e->disposal_method ?? null,
            'disposal_details'    => $e->disposal_details ?? null,
        ];
    });
@endphp

<script>
const MY_EQUIPMENT = @json($equipmentJson);

/* ── View toggle ── */
const btnGrid = document.getElementById('btnGrid');
const btnList = document.getElementById('btnList');
const gridView = document.getElementById('gridView');
const listView = document.getElementById('listView');

btnGrid?.addEventListener('click', () => {
    btnGrid.classList.add('active'); btnList.classList.remove('active');
    gridView.style.display = ''; listView.style.display = 'none';
    filterItems();
});
btnList?.addEventListener('click', () => {
    btnList.classList.add('active'); btnGrid.classList.remove('active');
    listView.style.display = ''; gridView.style.display = 'none';
    filterItems();
});

/* ── Filter logic ── */
function filterItems() {
    const q    = (document.getElementById('meSearch')?.value || '').toLowerCase().trim();
    const cond = document.getElementById('meCondFilter')?.value || '';
    const doc  = document.getElementById('meDocFilter')?.value  || '';

    let gridVisible = 0;
    document.querySelectorAll('.equip-card').forEach(card => {
        const match =
            (!q    || card.dataset.article.includes(q) || card.dataset.prop.includes(q)) &&
            (!cond || card.dataset.condition === cond) &&
            (!doc  || card.dataset.doctype === doc);
        card.style.display = match ? '' : 'none';
        if (match) gridVisible++;
    });
    const gridEmpty = document.getElementById('gridEmpty');
    if (gridEmpty) gridEmpty.style.display = gridVisible === 0 ? '' : 'none';

    let listVisible = 0;
    document.querySelectorAll('.equip-row').forEach(row => {
        const match =
            (!q    || row.dataset.article.includes(q) || row.dataset.prop.includes(q)) &&
            (!cond || row.dataset.condition === cond) &&
            (!doc  || row.dataset.doctype === doc);
        row.style.display = match ? '' : 'none';
        if (match) listVisible++;
    });
    const listEmpty = document.getElementById('listEmpty');
    if (listEmpty) listEmpty.style.display = listVisible === 0 ? '' : 'none';
}

document.getElementById('meSearch')?.addEventListener('input', filterItems);
document.getElementById('meCondFilter')?.addEventListener('change', filterItems);
document.getElementById('meDocFilter')?.addEventListener('change', filterItems);

/* ── Detail modal ── */
function openDetail(id) {
    const eq = MY_EQUIPMENT.find(e => e.id === id);
    if (!eq) return;

    document.getElementById('modalArticle').textContent    = eq.article;
    document.getElementById('modalPropNum').textContent    = eq.property_number;
    document.getElementById('modalDocType').textContent    = eq.document_type;
    document.getElementById('modalDocNum').textContent     = eq.document_number;
    document.getElementById('modalClass').textContent      = eq.classification;
    document.getElementById('modalUoM').textContent        = eq.unit_of_measurement;
    document.getElementById('modalValue').textContent      = '₱' + eq.unit_value;
    document.getElementById('modalQty').textContent        = eq.quantity;
    document.getElementById('modalRespCenter').textContent = eq.responsibility_center;
    document.getElementById('modalDesc').textContent       = eq.description;
    document.getElementById('modalRemarks').textContent    = eq.remarks;
    document.getElementById('modalAcqDate').textContent    = eq.acquisition_date;

    const svc = eq.condition === 'Serviceable';
    document.getElementById('modalCondition').innerHTML =
        `<span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700;background:${svc ? '#d4edda' : '#f8d7da'};color:${svc ? '#155724' : '#721c24'};">
            <i class="fas fa-${svc ? 'check' : 'times'}-circle"></i>${eq.condition}
         </span>`;

    const dispSection = document.getElementById('modalDisposalSection');
    if (eq.disposal_method) {
        dispSection.style.display = '';
        document.getElementById('modalDisposalMethod').textContent = eq.disposal_method;
        const detailsWrap = document.getElementById('modalDisposalDetailsWrap');
        if (eq.disposal_details) {
            detailsWrap.style.display = '';
            document.getElementById('modalDisposalDetails').textContent = eq.disposal_details;
        } else {
            detailsWrap.style.display = 'none';
        }
    } else {
        dispSection.style.display = 'none';
    }

    document.getElementById('detailOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';
}

document.getElementById('modalClose')?.addEventListener('click', closeDetail);
document.getElementById('detailOverlay')?.addEventListener('click', function(e) {
    if (e.target === this) closeDetail();
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeDetail();
});

function closeDetail() {
    document.getElementById('detailOverlay')?.classList.remove('open');
    document.body.style.overflow = '';
}
</script>
</body>
</html>