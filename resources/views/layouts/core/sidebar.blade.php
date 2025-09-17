{{-- filepath: resources/views/layouts/core/sidebar.blade.php --}}
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

.sidebar {
    position: fixed;
    left: 0;
    top: 0;
    width: 300px;
    height: 100vh;
    background: #ffffff;
    border-right: 1px solid #e2e8f0;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    overflow-y: auto;
    font-family: 'Inter', sans-serif;
}

/* Brand Section */
.brand {
    padding: 24px 20px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.brand-logo {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background-color: #296218;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    border: 2px solid rgba(41, 98, 24, 0.2);
}

.brand-logo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

.brand-text {
    font-size: 20px;
    font-weight: 700;
    color: #1e293b;
    letter-spacing: -0.02em;
}

/* Navigation */
.nav {
    padding: 20px 0;
}

.nav-item {
    margin: 0 16px 4px;
}

.nav-item.dropdown {
    margin: 0 16px 4px;
}

.nav-link {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    color: #64748b;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.2s ease;
    font-size: 14px;
    font-weight: 500;
    position: relative;
}

.nav-link:hover {
    background: #f1f5f9;
    color: #334155;
    text-decoration: none;
}

.nav-link.active {
    background-color: #296218;
    color: white;
    box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3);
}

.nav-icon {
    width: 20px;
    height: 20px;
    margin-right: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.nav-icon ion-icon {
    font-size: 20px;
}

/* Dropdown */
.dropdown {
    position: relative;
    margin: 0 16px 4px;
}

.dropdown-toggle {
    justify-content: space-between;
    cursor: pointer;
}

.dropdown-arrow {
    transition: transform 0.3s ease;
    margin-left: auto;
    width: 16px;
    height: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.dropdown-arrow ion-icon {
    font-size: 16px;
}

.dropdown.open .dropdown-arrow {
    transform: rotate(180deg);
}

.dropdown-menu {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease, padding 0.3s ease;
    background: #f8fafc;
    margin: 4px 0 0;
    border-radius: 8px;
    width: 100%;
}

.dropdown.open .dropdown-menu {
    max-height: 200px;
    padding: 8px 0;
}

.dropdown-item {
    display: flex;
    align-items: center;
    padding: 10px 20px;
    color: #64748b;
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    transition: all 0.2s ease;
    border-radius: 6px;
    margin: 2px 8px;
    position: relative;
}

.dropdown-item:hover {
    background: #e2e8f0;
    color: #334155;
    text-decoration: none;
}

.dropdown-item.active {
    background: #296218 !important;
    color: white !important;
}

.dropdown-item .nav-icon {
    width: 16px;
    height: 16px;
    margin-right: 10px;
}

.dropdown-item .nav-icon ion-icon {
    font-size: 16px;
}

/* Custom Scrollbar */
.sidebar::-webkit-scrollbar {
    width: 4px;
}

.sidebar::-webkit-scrollbar-track {
    background: transparent;
}

.sidebar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Responsive */
@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
        transition: transform 0.3s ease;
    }

    .sidebar.open {
        transform: translateX(0);
    }
}
</style>
<div class="sidebar">
    <!-- Brand Section -->
    <div class="brand">
        <div class="brand-logo">
            <img src="{{ asset('assets/img/atirtc1logo.jpg') }}" alt="AGRISUPPLY Logo">
        </div>
        <div class="brand-text">AGRISUPPLY</div>
    </div>

    <!-- Navigation -->
    <nav class="nav">
        <!-- Dashboard -->
        <div class="nav-item">
            <a href="{{ route('client.dashboard') }}" 
               class="nav-link {{ Request::is('client/dashboard*') || Request::is('home') || Request::is('/') ? 'active' : '' }}">
                <div class="nav-icon">
                    <ion-icon name="home-outline"></ion-icon>
                </div>
                <span>Dashboard</span>
            </a>
        </div>

        <!-- Manage Users (Admin Only) -->
        @if(auth()->check() && auth()->user()->isAdmin())
            <div class="nav-item">
                <a href="{{ url('client/users') }}" 
                   class="nav-link {{ Request::is('client/users*') ? 'active' : '' }}">
                    <div class="nav-icon">
                        <ion-icon name="people-outline"></ion-icon>
                    </div>
                    <span>Manage Users</span>
                </a>
            </div>
        @endif

        <!-- Supplies -->
        <div class="nav-item">
            <a href="{{ url('client/supplies') }}" 
               class="nav-link {{ Request::is('client/supplies*') ? 'active' : '' }}">
                <div class="nav-icon">
                    <ion-icon name="archive-outline"></ion-icon>
                </div>
                <span>Supplies</span>
            </a>
        </div>

        <!-- Report -->
        <div class="nav-item">
            <a href="{{ route('client.reports.index') }}" 
               class="nav-link {{ Request::is('client/reports*') ? 'active' : '' }}">
                <div class="nav-icon">
                    <ion-icon name="stats-chart-outline"></ion-icon>
                </div>
                <span>Report</span>
            </a>
        </div>

        <!-- Generate Cards Dropdown -->
        <div class="nav-item dropdown {{ Request::is('client/stockcard*') || Request::is('client/propertycard*') ? 'open' : '' }}">
            <a href="#" class="nav-link dropdown-toggle {{ Request::is('client/stockcard*') || Request::is('client/propertycard*') ? 'active' : '' }}">
                <div class="nav-icon">
                    <ion-icon name="card-outline"></ion-icon>
                </div>
                <span>Generate Cards</span>
                <div class="dropdown-arrow">
                    <ion-icon name="chevron-down-outline"></ion-icon>
                </div>
            </a>
            <div class="dropdown-menu">
                <a href="{{ route('client.stockcard.index') }}" 
                   class="dropdown-item {{ Request::is('client/stockcard*') ? 'active' : '' }}">
                    <div class="nav-icon">
                        <ion-icon name="receipt-outline"></ion-icon>
                    </div>
                    <span>Stock Card</span>
                </a>
                <a href="{{ route('client.propertycard.index') }}" 
                   class="dropdown-item {{ Request::is('client/propertycard*') ? 'active' : '' }}">
                    <div class="nav-icon">
                        <ion-icon name="document-text-outline"></ion-icon>
                    </div>
                    <span>Property Card</span>
                </a>
            </div>
        </div>

        <!-- Help -->
        <div class="nav-item">
            <a href="{{ route('client.help.index') }}" 
               class="nav-link {{ Request::is('client/help*') ? 'active' : '' }}">
                <div class="nav-icon">
                    <ion-icon name="help-circle-outline"></ion-icon>
                </div>
                <span>Help</span>
            </a>
        </div>

        <!-- Profile Settings -->
        <div class="nav-item">
            <a href="{{ route('client.profile.index') }}" 
               class="nav-link {{ Request::is('client/profile*') ? 'active' : '' }}">
                <div class="nav-icon">
                    <ion-icon name="settings-outline"></ion-icon>
                </div>
                <span>Profile Settings</span>
            </a>
        </div>

        <!-- Sign Out -->
        <div class="nav-item">
            <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <div class="nav-icon">
                    <ion-icon name="log-out-outline"></ion-icon>
                </div>
                <span>Sign Out</span>
            </a>
        </div>
    </nav>
</div>

<!-- Logout Form -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dropdown functionality
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
    
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            
            const dropdown = this.closest('.dropdown');
            
            // Close all other dropdowns first
            document.querySelectorAll('.dropdown.open').forEach(otherDropdown => {
                if (otherDropdown !== dropdown) {
                    otherDropdown.classList.remove('open');
                }
            });
            
            // Toggle current dropdown
            dropdown.classList.toggle('open');
        });
    });

    // Handle dropdown item clicks - Simple approach
    const dropdownItems = document.querySelectorAll('.dropdown-item');
    dropdownItems.forEach(item => {
        item.addEventListener('click', function(e) {
            // Find the parent dropdown and close it immediately
            const parentDropdown = this.closest('.dropdown');
            if (parentDropdown) {
                parentDropdown.classList.remove('open');
            }
            
            // Let the browser handle the navigation normally
            // Don't prevent default - we want the link to work
        });
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown.open').forEach(dropdown => {
                dropdown.classList.remove('open');
            });
        }
    });
});
</script>