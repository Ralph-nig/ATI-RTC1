{{-- filepath: resources/views/client/ris/index.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My RIS Requests</title>
    <link rel="stylesheet" href="{{ asset('css/supplies.css') }}">
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
                <h1 class="ris-page-title"><i class="fas fa-file-alt"></i>
                    @if(auth()->user()->isAdmin()) All RIS Requests @else My RIS Requests @endif
                </h1>
                @if(!auth()->user()->isAdmin())
                <a href="{{ route('client.ris.create') }}" class="ris-btn ris-btn-primary">
                    <i class="fas fa-plus"></i> New Request
                </a>
                @endif
            </div>

            @if(session('success'))
                <div class="ris-alert ris-alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="ris-alert ris-alert-danger"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
            @endif

            <div class="ris-filter-bar">
                <form method="GET" action="{{ route('client.ris.index') }}" class="ris-filter-form">
                    <div class="ris-search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" placeholder="Search by purpose…" value="{{ request('search') }}">
                    </div>
                    <select name="status" class="ris-select ris-select-sm" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="pending"  {{ request('status')==='pending'  ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status')==='approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status')==='rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </form>
            </div>

            <div class="ris-card">
                <div class="ris-table-wrap">
                    <table class="ris-table">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                @if(auth()->user()->isAdmin())<th>Requestor</th>@endif
                                <th>Purpose</th>
                                <th style="width:80px;text-align:center">Items</th>
                                <!-- <th style="width:110px">Date Needed</th> -->
                                <th style="width:100px">Status</th>
                                <th style="width:150px">Date Filed</th>
                                <th style="width:70px;text-align:center">View</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($risRequests as $ris)
                            <tr>
                                <td><span class="ris-ref">{{ $ris->reference }}</span></td>
                                @if(auth()->user()->isAdmin())
                                <td style="font-size:13px;font-weight:600;">{{ $ris->requester->name }}</td>
                                @endif
                                <td>{{ $ris->purpose }}</td>
                                <td style="text-align:center">
                                    <span class="ris-count-badge">{{ $ris->supplies->count() }}</span>
                                </td>
                                <!-- <td class="ris-date">{{ $ris->date_needed ? $ris->date_needed->format('M d, Y') : '—' }}</td> -->
                                <td>
                                    <span class="ris-badge ris-badge-{{ $ris->status }}">
                                        <i class="fas fa-circle" style="font-size:7px"></i>
                                        {{ ucfirst($ris->status) }}
                                    </span>
                                </td>
                                <td class="ris-date">{{ $ris->created_at->format('M d, Y g:i A') }}</td>
                                <td style="text-align:center">
                                    <a href="{{ route('client.ris.show', $ris->id) }}" class="ris-icon-btn ris-icon-view" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ auth()->user()->isAdmin() ? 8 : 7 }}">
                                    <div class="ris-empty">
                                        <i class="fas fa-file-alt"></i>
                                        <h3>No Requests Yet</h3>
                                        @if(!auth()->user()->isAdmin())
                                        <p>You haven't submitted any supply requests.</p>
                                        <a href="{{ route('client.ris.create') }}" class="ris-btn ris-btn-primary">
                                            <i class="fas fa-plus"></i> Create Your First Request
                                        </a>
                                        @else
                                        <p>No RIS requests have been submitted yet.</p>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
@if($risRequests->hasPages())
    <div class="pagination-wrapper">
        <div class="pagination-info">
            Showing {{ $risRequests->firstItem() }} to {{ $risRequests->lastItem() }} of {{ $risRequests->total() }} items
        </div>
        
        <nav>
            <ul class="pagination">
                {{-- Previous Page Link --}}
                @if ($risRequests->onFirstPage())
                    <li class="disabled" aria-disabled="true">
                        <span aria-hidden="true">
                            <i class="fas fa-chevron-left"></i> Previous
                        </span>
                    </li>
                @else
                    <li>
                        <a href="{{ $risRequests->previousPageUrl() }}" rel="prev">
                            <i class="fas fa-chevron-left"></i> Previous
                        </a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($risRequests->getUrlRange(1, $risRequests->lastPage()) as $page => $url)
                    @if ($page == $risRequests->currentPage())
                        <li class="active" aria-current="page">
                            <span>{{ $page }}</span>
                        </li>
                    @else
                        <li>
                            <a href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($risRequests->hasMorePages())
                    <li>
                        <a href="{{ $risRequests->nextPageUrl() }}" rel="next">
                            Next <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                @else
                    <li class="disabled" aria-disabled="true">
                        <span aria-hidden="true">
                            Next <i class="fas fa-chevron-right"></i>
                        </span>
                    </li>
                @endif
            </ul>
        </nav>
        
        <div class="items-per-page">
            <label for="perPage">Show:</label>
            <select id="perPage" onchange="changePerPage(this.value)">
                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
            </select>
        </div>
    </div>
@endif
            </div>

        </div>
    </div>
</div>

@include('layouts.core.footer')

<style>
.ris-wrap{padding:24px;display:flex;flex-direction:column;gap:16px}
.ris-page-header{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px}
.ris-page-title{font-size:22px;font-weight:600;color:#296218;margin:0;display:flex;align-items:center;gap:10px}
.ris-alert{display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:8px;font-size:14px;font-weight:500}
.ris-alert-success{background:#d4edda;border:1px solid #c3e6cb;color:#155724}
.ris-alert-danger{background:#f8d7da;border:1px solid #f5c6cb;color:#721c24}
.ris-filter-bar{background:#fff;border:1px solid #e9ecef;border-radius:10px;padding:12px 16px}
.ris-filter-form{display:flex;gap:10px;align-items:center;flex-wrap:wrap}
.ris-search-box{display:flex;align-items:center;gap:8px;border:1px solid #dee2e6;border-radius:8px;padding:0 12px;background:#fff;height:36px;flex:1;min-width:180px}
.ris-search-box i{color:#adb5bd;font-size:13px}
.ris-search-box input{border:none;outline:none;background:transparent;font-size:14px;color:#495057;width:100%}
.ris-select{padding:7px 32px 7px 12px;border:1px solid #dee2e6;border-radius:8px;font-size:14px;color:#495057;background:#fff;outline:none;appearance:none;cursor:pointer;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23adb5bd' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 10px center}
.ris-select-sm{height:36px}
.ris-btn{display:inline-flex;align-items:center;gap:6px;padding:0 16px;height:38px;border-radius:8px;font-size:14px;font-weight:500;cursor:pointer;border:none;text-decoration:none;white-space:nowrap;transition:filter .15s}
.ris-btn:hover{filter:brightness(.9)}
.ris-btn-primary{background:#296218;color:#fff}
.ris-card{background:#fff;border:1px solid #e9ecef;border-radius:12px;overflow:hidden}
.ris-table-wrap{overflow-x:auto}
.ris-table{width:100%;border-collapse:collapse;font-size:14px}
.ris-table thead tr{background:#296218}
.ris-table thead th{padding:13px 14px;color:#fff;font-weight:500;text-align:left;white-space:nowrap}
.ris-table tbody tr{border-bottom:1px solid #f0f0f0;transition:background .1s}
.ris-table tbody tr:last-child{border-bottom:none}
.ris-table tbody tr:hover{background:#f8fdf6}
.ris-table td{padding:12px 14px;color:#495057;vertical-align:middle}
.ris-date{font-size:13px;color:#6c757d}
.ris-ref{font-weight:600;font-size:13px;color:#296218;font-family:monospace}
.ris-count-badge{display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;background:#e9ecef;border-radius:50%;font-size:12px;font-weight:600;color:#495057}
.ris-badge{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.3px}
.ris-badge-pending{background:#fff3cd;color:#856404;border:1px solid #ffeeba}
.ris-badge-approved{background:#d4edda;color:#155724;border:1px solid #c3e6cb}
.ris-badge-rejected{background:#f8d7da;color:#721c24;border:1px solid #f5c6cb}
.ris-icon-btn{width:30px;height:30px;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;font-size:13px;cursor:pointer;border:none;text-decoration:none;transition:filter .15s}
.ris-icon-btn:hover{filter:brightness(.88)}
.ris-icon-view{background:#17a2b8;color:#fff}
.ris-empty{text-align:center;padding:60px 20px;display:flex;flex-direction:column;align-items:center;gap:10px}
.ris-empty i{font-size:48px;color:#dee2e6}
.ris-empty h3{font-size:18px;color:#495057;margin:0}
.ris-empty p{font-size:14px;color:#6c757d;margin:0}
.ris-pagination{padding:14px;border-top:1px solid #f0f0f0}
</style>

<script>
setTimeout(() => {
    document.querySelectorAll('.ris-alert').forEach(el => {
        el.style.transition = 'opacity .4s'; el.style.opacity = '0';
        setTimeout(() => el.remove(), 400);
    });
}, 5000);
</script>
</body>
</html>