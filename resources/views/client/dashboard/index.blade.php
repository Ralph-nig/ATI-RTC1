<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

<div class="welcome-section">
    <h2>Welcome to the Dashboard</h2>
</div>

@if(auth()->user()->isAdmin())
<div class="stats-container">
    <div class="stat-card green" onclick="navigateTo('/client/supplies')">
        <div class="stat-left">
            <div class="stat-icon">
                <ion-icon name="clipboard"></ion-icon>
            </div>
            <div class="stat-content">
                <h3>Total Items</h3>
                <span class="stat-number">{{ $totalItems }}</span>
            </div>
        </div>
        <div class="arrow-icon">
            <ion-icon name="chevron-forward"></ion-icon>
        </div>
    </div>
    <div class="stat-card green" onclick="navigateTo('/client/users')">
        <div class="stat-left">
            <div class="stat-icon">
                <ion-icon name="people"></ion-icon>
            </div>
            <div class="stat-content">
                <h3>Active Users</h3>
                <span class="stat-number">{{ $totalUsers }}</span>
            </div>
        </div>
        <div class="arrow-icon">
            <ion-icon name="chevron-forward"></ion-icon>
        </div>
    </div>
    <div class="stat-card green" onclick="navigateTo('/client/supplies')">
        <div class="stat-left">
            <div class="stat-icon">
                <ion-icon name="cube"></ion-icon>
            </div>
            <div class="stat-content">
                <h3>Items in Stock</h3>
                <span class="stat-number">{{ $itemsInStock}}</span>
            </div>
        </div>
        <div class="arrow-icon">
            <ion-icon name="chevron-forward"></ion-icon>
        </div>
    </div>
</div>
@endif

<div class="main-content-area">
    <div class="recently-added-section">
        <div class="section-header">
            <h3>
                <ion-icon name="time"></ion-icon>
                Recently Added
            </h3>
        </div>
        <div class="table-container">
            <div class="table-headers">
                <div class="header-cell items-header">
                    <ion-icon name="bag"></ion-icon>
                    Items
                </div>
                <div class="header-cell quantity-header">
                    <ion-icon name="layers"></ion-icon>
                    Quantity
                </div>
                <div class="header-cell date-header">
                    <ion-icon name="calendar"></ion-icon>
                    Date
                </div>
            </div>
            
            <div class="table-body">
                @forelse($recentItems ?? [] as $item)
                    <div class="table-row">
                        <div class="cell items-cell">{{ $item->name ?? 'Sample Item' }}</div>
                        <div class="cell quantity-cell">
                            <span class="quantity-badge">{{ $item->quantity ?? 25 }}</span>
                        </div>
                        <div class="cell date-cell">{{ $item->created_at?->format('M d, Y') ?? 'Dec 15, 2024' }}</div>
                    </div>
                @empty
                @endforelse
            </div>
        </div>
    </div>
    
    <div class="right-panel">
        <div class="low-stock-panel">
            <div class="low-stock-header">
                <ion-icon name="warning"></ion-icon>
                Low Stock Alert
            </div>
            
            <div class="low-stock-headers">
                <div class="low-header id-header">
                    <ion-icon name="finger-print"></ion-icon>
                    ID
                </div>
                <div class="low-header product-header">
                    <ion-icon name="bag-handle"></ion-icon>
                    Product
                </div>
                <div class="low-header qty-header">
                    <ion-icon name="analytics"></ion-icon>
                    Qty
                </div>
            </div>
            
            <div class="low-stock-body">
                @forelse($lowStockItems ?? [] as $item)
                    <div class="low-stock-row">
                        <div class="low-cell id-cell">{{ $loop->iteration }}</div>
                        <div class="low-cell product-cell">
                            <span class="product-indicator"></span>
                            {{ $item->name ?? 'Product' }}
                        </div>
                        <div class="low-cell qty-cell">
                            <span class="low-quantity">{{ $item->quantity ?? 0 }}</span>
                        </div>
                    </div>
                    @empty
                @endforelse
            </div>
            
            <button class="restock-btn" onclick="navigateTo('/client/supplies?low_stock=1')">
                <ion-icon name="add-circle"></ion-icon>
                Restock Items
            </button>
        </div>
    </div>
</div>

<script>
function navigateTo(url) {
    window.location.href = url;
}

function showLowStockDetails() {
    document.querySelector('.low-stock-panel').scrollIntoView({ 
        behavior: 'smooth',
        block: 'center'
    });
    
    const panel = document.querySelector('.low-stock-panel');
    panel.style.boxShadow = '0 0 30px rgba(255, 193, 7, 0.4)';
    setTimeout(() => {
        panel.style.boxShadow = '0 4px 20px rgba(0,0,0,0.08)';
    }, 2000);
}
</script>