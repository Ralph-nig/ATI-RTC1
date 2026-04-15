{{-- filepath: resources/views/layouts/core/sidebar.blade.php --}}

<div class="navigation" id="sidebar">
    <ul>
        <li>
            <a href="#">
                <span class="brand-container">
                    <img src="{{ asset('assets/img/atirtc1logo.jpg') }}" alt="AIMMS Logo" class="brand-logo aimms-logo">
                </span>
                <span class="title">ATI - RTC 1</span>
            </a>
        </li>

        {{-- Dashboard — visible to everyone --}}
        <li class="{{ Request::is('client/dashboard*') || Request::is('home') || Request::is('/') ? 'hovered' : '' }}">
            <a href="{{ route('client.dashboard') }}" class="nav-link" data-title="Dashboard">
                <span class="icon"><ion-icon name="home-outline"></ion-icon></span>
                <span class="title">Dashboard</span>
            </a>
        </li>

        @if(auth()->check() && !auth()->user()->isRequestor())
            {{-- Announcements — admin only --}}
            @if(auth()->user()->isAdmin())
            <li class="{{ Request::is('client/announcement*') ? 'hovered' : '' }}">
                <a href="{{ route('client.announcement.index') }}" data-title="Announcements">
                    <span class="icon"><ion-icon name="megaphone-outline"></ion-icon></span>
                    <span class="title">Announcements</span>
                </a>
            </li>
            @endif

            {{-- Manage Users — admin only --}}
            @if(auth()->user()->isAdmin())
            <li class="{{ Request::is('client/users*') ? 'hovered' : '' }}">
                <a href="{{ url('client/users') }}" class="nav-link" data-title="Manage Users">
                    <span class="icon"><ion-icon name="people-outline"></ion-icon></span>
                    <span class="title">Manage Users</span>
                </a>
            </li>
            @endif

            {{-- Supplies --}}
            <li class="{{ Request::is('client/supplies*') ? 'hovered' : '' }}">
                <a href="{{ url('client/supplies') }}" data-title="Supplies">
                    <span class="icon"><ion-icon name="archive-outline"></ion-icon></span>
                    <span class="title">Supplies</span>
                </a>
            </li>

            {{-- Equipment --}}
            <li class="{{ Request::is('client/equipment*') ? 'hovered' : '' }}">
                <a href="{{ url('client/equipment') }}" data-title="Equipment">
                    <span class="icon"><ion-icon name="construct-outline"></ion-icon></span>
                    <span class="title">Equipment</span>
                </a>
            </li>

            {{-- Report --}}
            <li class="{{ Request::is('client/reports*') ? 'hovered' : '' }}">
                <a href="{{ route('client.reports.index') }}" data-title="Report">
                    <span class="icon"><ion-icon name="stats-chart-outline"></ion-icon></span>
                    <span class="title">Report</span>
                </a>
            </li>

            {{-- Inventory dropdown --}}
            <li class="dropdown {{ Request::is('client/stockcard*') || Request::is('client/propertycard*') ? 'active' : '' }}">
                <a href="#" onclick="toggleDropdown(event, this)" data-title="Inventory">
                    <span class="icon"><ion-icon name="document-text-outline"></ion-icon></span>
                    <span class="title">Inventory</span>
                </a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('client/stockcard*') && !Request::is('client/stockcard/audit-trail') ? 'active' : '' }}">
                        <a href="{{ route('client.stockcard.index') }}">
                            <span class="icon">ㅤ<ion-icon name="document-text-outline"></ion-icon></span>
                            <span class="title">ㅤSupplies</span>
                        </a>
                    </li>
                    <li class="{{ Request::is('client/ris*') ? 'hovered' : '' }}">
                        <a href="{{ route('client.ris.index') }}" data-title="RIS">
                            <span class="icon">ㅤ<ion-icon name="newspaper-outline"></ion-icon></span>
                            <span class="title">ㅤRIS</span>
                        </a>
                    </li>
                    <li class="{{ Request::is('client/propertycard*') ? 'active' : '' }}">
                        <a href="{{ route('client.propertycard.index') }}">
                            <span class="icon">ㅤ<ion-icon name="receipt-outline"></ion-icon></span>
                            <span class="title">ㅤProperty</span>
                        </a>
                    </li>
                    <li class="{{ Request::is('client/stockcard/audit-trail') ? 'active' : '' }}">
                        <a href="{{ route('client.stockcard.audit-trail') }}">
                            <span class="icon">ㅤ<ion-icon name="time-outline"></ion-icon></span>
                            <span class="title">ㅤAudit Trail</span>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Help --}}
            <li class="{{ Request::is('client/help*') ? 'hovered' : '' }}">
                <a href="{{ route('client.help.index') }}" data-title="Help">
                    <span class="icon"><ion-icon name="help-outline"></ion-icon></span>
                    <span class="title">Help</span>
                </a>
            </li>

        @else
            {{-- Requestor-only nav items --}}
            <li class="{{ Request::is('client/ris*') ? 'hovered' : '' }}">
                <a href="{{ route('client.ris.index') }}" data-title="RIS">
                    <span class="icon"><ion-icon name="document-text-outline"></ion-icon></span>
                    <span class="title">RIS</span>
                </a>
            </li>
            <!-- <li class="{{ Request::is('client/help*') ? 'hovered' : '' }}">
                <a href="{{ route('client.help.index') }}" data-title="My Requests">
                    <span class="icon"><ion-icon name="clipboard-outline"></ion-icon></span>
                    <span class="title">Request logs</span>
                </a>
            </li> -->
            <li class="{{ Request::is('equipment.my*') ? 'hovered' : '' }}">
                <a href="{{ route('equipment.my') }}" data-title="My Requests">
                    <span class="icon"><ion-icon name="briefcase-outline"></ion-icon></span>
                    <span class="title">My Equipments</span>
                </a>
            </li>
        @endif

        {{-- Profile Settings — visible to everyone --}}
        <li class="{{ Request::is('client/profile*') ? 'hovered' : '' }}">
            <a href="{{ route('client.profile.index') }}" data-title="Profile Settings">
                <span class="icon"><ion-icon name="settings-outline"></ion-icon></span>
                <span class="title">Profile Settings</span>
            </a>
        </li>

        {{-- About — visible to everyone --}}
        <li class="{{ Request::is('client/about*') ? 'hovered' : '' }}">
            <a href="{{ route('client.about.index') }}" data-title="About">
                <span class="icon"><ion-icon name="information-circle-outline"></ion-icon></span>
                <span class="title">About</span>
            </a>
        </li>

        {{-- Sign Out — visible to everyone --}}
        <li>
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" data-title="Sign Out">
                <span class="icon"><ion-icon name="log-out-outline"></ion-icon></span>
                <span class="title">Sign Out</span>
            </a>
        </li>
    </ul>
</div>

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

<script>
const SIDEBAR_WIDTH          = 300;
const SIDEBAR_COLLAPSED_WIDTH = 72;

(function createToggleButton() {
    const btn = document.createElement('button');
    btn.id        = 'sidebarToggle';
    btn.title     = 'Toggle Sidebar';
    btn.innerHTML = '<ion-icon name="chevron-back-outline"></ion-icon>';
    btn.setAttribute('aria-label', 'Toggle Sidebar');

    Object.assign(btn.style, {
        position:       'fixed',
        top:            '92px',
        left:           SIDEBAR_WIDTH + 'px',
        transform:      'translateX(-50%)',
        width:          '30px',
        height:         '30px',
        borderRadius:   '50%',
        border:         '2px solid #e5e7eb',
        background:     '#ffffff',
        color:          '#296218',
        cursor:         'pointer',
        zIndex:         '9999',
        display:        'flex',
        alignItems:     'center',
        justifyContent: 'center',
        boxShadow:      '2px 0 8px rgba(0,0,0,0.15)',
        transition:     'left 0.3s cubic-bezier(0.4,0,0.2,1), background 0.2s, color 0.2s, box-shadow 0.2s',
        padding:        '0',
        outline:        'none',
    });

    btn.addEventListener('mouseenter', () => {
        btn.style.background  = '#296218';
        btn.style.color       = '#ffffff';
        btn.style.borderColor = '#296218';
        btn.style.boxShadow   = '0 4px 14px rgba(41,98,24,0.4)';
    });
    btn.addEventListener('mouseleave', () => {
        btn.style.background  = '#ffffff';
        btn.style.color       = '#296218';
        btn.style.borderColor = '#e5e7eb';
        btn.style.boxShadow   = '2px 0 8px rgba(0,0,0,0.15)';
    });

    btn.addEventListener('click', toggleSidebar);
    document.body.appendChild(btn);
})();

function toggleSidebar() {
    const nav     = document.getElementById('sidebar');
    const btn     = document.getElementById('sidebarToggle');
    const details = document.querySelector('.details');

    const isCollapsed = nav.classList.toggle('collapsed');

    btn.style.left = (isCollapsed ? SIDEBAR_COLLAPSED_WIDTH : SIDEBAR_WIDTH) + 'px';

    btn.querySelector('ion-icon').setAttribute(
        'name',
        isCollapsed ? 'chevron-forward-outline' : 'chevron-back-outline'
    );

    if (details) {
        details.style.width = isCollapsed
            ? 'calc(100% - ' + SIDEBAR_COLLAPSED_WIDTH + 'px)'
            : 'calc(100% - ' + SIDEBAR_WIDTH + 'px)';
        details.style.left = (isCollapsed ? SIDEBAR_COLLAPSED_WIDTH : SIDEBAR_WIDTH) + 'px';
    }

    localStorage.setItem('sidebarCollapsed', isCollapsed);
}

function toggleDropdown(event, element) {
    event.preventDefault();

    const parentLi = element.parentElement;

    const nav = document.getElementById('sidebar');
    if (nav.classList.contains('collapsed')) {
        nav.classList.remove('collapsed');
        const btn     = document.getElementById('sidebarToggle');
        const details = document.querySelector('.details');
        btn.style.left = SIDEBAR_WIDTH + 'px';
        btn.querySelector('ion-icon').setAttribute('name', 'chevron-back-outline');
        if (details) {
            details.style.width = 'calc(100% - ' + SIDEBAR_WIDTH + 'px)';
            details.style.left  = SIDEBAR_WIDTH + 'px';
        }
        localStorage.setItem('sidebarCollapsed', false);
    }

    document.querySelectorAll('.navigation ul li').forEach(li => li.classList.remove('hovered'));

    document.querySelectorAll('.navigation .dropdown').forEach(dropdown => {
        if (dropdown !== parentLi) dropdown.classList.remove('open', 'active');
    });

    parentLi.classList.toggle('open');
    if (parentLi.classList.contains('open')) parentLi.classList.add('hovered');

    if (parentLi.classList.contains('open') || parentLi.querySelector('.dropdown-menu li.active')) {
        parentLi.classList.add('active');
    } else {
        parentLi.classList.remove('active');
    }

    parentLi.querySelectorAll('.dropdown-menu li.active').forEach(c => c.classList.add('hovered'));
}

document.addEventListener('click', function(e) {
    const nav = document.getElementById('sidebar');
    const btn = document.getElementById('sidebarToggle');
    if (!nav.contains(e.target) && e.target !== btn) {
        document.querySelectorAll('.navigation .dropdown').forEach(d => d.classList.remove('open'));
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (isCollapsed) {
        const nav     = document.getElementById('sidebar');
        const btn     = document.getElementById('sidebarToggle');
        const details = document.querySelector('.details');

        nav.classList.add('collapsed');
        btn.style.left = SIDEBAR_COLLAPSED_WIDTH + 'px';
        btn.querySelector('ion-icon').setAttribute('name', 'chevron-forward-outline');

        if (details) {
            details.style.width = 'calc(100% - ' + SIDEBAR_COLLAPSED_WIDTH + 'px)';
            details.style.left  = SIDEBAR_COLLAPSED_WIDTH + 'px';
        }
    }

    document.querySelectorAll('.dropdown-menu a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.stopPropagation();
            window.location.href = this.href;
        });
    });
});
</script>