<div class="welcome-section">
    <h2>Welcome to the Dashboard</h2>
</div>

<div class="stats-container">
    <div class="stat-card green" onclick="navigateTo('/client/supplies')">
        <div class="stat-left">
            <div class="stat-icon">
                <ion-icon name="clipboard"></ion-icon>
            </div>
            <div class="stat-content">
                <h3>Total Items</h3>
                <span class="stat-number">{{ $totalItems ?? 1430 }}</span>
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
                <span class="stat-number">{{ $totalUsers ?? 3 }}</span>
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

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

/* Dashboard specific styles - Updated to work with existing sidebar */
.details {
    position: relative;
    width: calc(100% - 300px);
    left: 300px;
    min-height: 100vh;
    background: #f5f5f5;
    transition: 0.5s;
    padding: 20px;
    font-family: 'Inter', sans-serif;
}

/* Welcome Section */
.welcome-section {
    margin-bottom: 20px;
}

.welcome-section h2 {
    font-size: 24px;
    color: #333;
    font-weight: 500;
    margin: 0;
}

/* Stats Cards */
.stats-container {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 20px;
}

.stat-card {
    background-color: #296218;
    color: white;
    padding: 24px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    transition: all 0.3s ease;
    min-height: 100px;
    box-shadow: 0 4px 20px rgba(74, 124, 89, 0.2);
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 30px rgba(74, 124, 89, 0.3);
}

.stat-left {
    display: flex;
    align-items: center;
    gap: 16px;
}

.stat-icon {
    width: 56px;
    height: 56px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(10px);
}

.stat-icon ion-icon {
    font-size: 28px;
    color: white;
}

.stat-content h3 {
    font-size: 14px;
    font-weight: 500;
    margin: 0 0 8px 0;
    opacity: 0.9;
}

.stat-number {
    font-size: 32px;
    font-weight: 700;
    line-height: 1;
}

.arrow-icon {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.arrow-icon ion-icon {
    font-size: 20px;
    color: white;
}

.stat-card:hover .arrow-icon {
    background: rgba(255, 255, 255, 0.3);
    transform: translateX(2px);
}

/* Main Content Area */
.main-content-area {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 20px;
    align-items: start;
}

/* Recently Added Section */
.recently-added-section {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    overflow: hidden;
}

.section-header {
    background-color: #296218;
    color: white;
    padding: 20px 24px;
    margin: 0;
}

.section-header h3 {
    font-size: 18px;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.section-header h3 ion-icon {
    font-size: 20px;
}

/* Table Headers */
.table-headers {
    background: #296218;
    display: grid;
    grid-template-columns: 1fr auto 120px;
    color: white;
}

.header-cell {
    padding: 16px 24px;
    font-weight: 600;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.header-cell ion-icon {
    font-size: 16px;
}

/* Table Body */
.table-body {
    max-height: 300px;
    overflow-y: auto;
}

.table-row {
    display: grid;
    grid-template-columns: 1fr auto 120px;
    border-bottom: 1px solid #f0f0f0;
    transition: background-color 0.2s ease;
}

.table-row:hover {
    background: #f8faf9;
}

.cell {
    padding: 16px 24px;
    font-size: 14px;
    display: flex;
    align-items: center;
}

.items-cell {
    font-weight: 500;
    color: #333;
}

.quantity-cell {
    justify-content: center;
}

.quantity-badge {
    background: #007bff;
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    min-width: 40px;
    text-align: center;
}

.date-cell {
    color: #666;
    font-size: 13px;
    justify-content: center;
}

/* Right Panel */
.right-panel {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

/* Low Stock Panel */
.low-stock-panel {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    overflow: hidden;
    border-left: 4px solid #ffc107;
}

.low-stock-header {
    background-color: #296218;
    color: white;
    padding: 20px 24px;
    font-weight: 600;
    font-size: 16px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.low-stock-header ion-icon {
    font-size: 20px;
    color: #ffc107;
}

/* Low Stock Headers */
.low-stock-headers {
    background: #f8f9fa;
    display: grid;
    grid-template-columns: 40px 1fr 60px;
    border-bottom: 1px solid #e9ecef;
}

.low-header {
    padding: 12px 16px;
    font-size: 12px;
    font-weight: 600;
    color: #666;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    gap: 6px;
}

.low-header ion-icon {
    font-size: 14px;
}

/* Low Stock Body */
.low-stock-body {
    max-height: 200px;
    overflow-y: auto;
}

.low-stock-row {
    display: grid;
    grid-template-columns: 40px 1fr 60px;
    border-bottom: 1px solid #f0f0f0;
    transition: background-color 0.2s ease;
}

.low-stock-row:hover {
    background: #fff3cd;
}

.low-cell {
    padding: 12px 16px;
    font-size: 13px;
    display: flex;
    align-items: center;
}

.id-cell {
    font-weight: 600;
    color: #666;
    justify-content: center;
}

.product-cell {
    gap: 8px;
    font-weight: 500;
}

.product-indicator {
    width: 8px;
    height: 8px;
    background: #dc3545;
    border-radius: 50%;
    flex-shrink: 0;
}

.qty-cell {
    justify-content: center;
}

.low-quantity {
    background: #dc3545;
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 11px;
    min-width: 24px;
    text-align: center;
}

.restock-btn {
    width: 100%;
    background-color: #296218;
    color: white;
    border: none;
    padding: 16px 24px;
    font-weight: 600;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.restock-btn:hover {
    background: linear-gradient(135deg, #3a5f45, #4a7c59);
    transform: translateY(-1px);
}

.restock-btn ion-icon {
    font-size: 18px;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .details {
        width: 100%;
        left: 0;
        padding: 15px;
    }
    
    .main-content-area {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .stats-container {
        grid-template-columns: 1fr;
        gap: 15px;
    }
}

@media (max-width: 768px) {
    .stats-container {
        grid-template-columns: 1fr;
    }
    
    .table-headers,
    .table-row {
        grid-template-columns: 1fr;
    }
    
    .header-cell,
    .cell {
        padding: 12px 16px;
    }
    
    .low-stock-headers,
    .low-stock-row {
        grid-template-columns: 1fr;
    }
    
    .low-header,
    .low-cell {
        padding: 8px 16px;
    }
    
    .stat-icon {
        width: 48px;
        height: 48px;
    }
    
    .stat-icon ion-icon {
        font-size: 24px;
    }
    
    .stat-number {
        font-size: 28px;
    }
}

/* Scrollbar Styling */
.table-body::-webkit-scrollbar,
.low-stock-body::-webkit-scrollbar {
    width: 6px;
}

.table-body::-webkit-scrollbar-track,
.low-stock-body::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.table-body::-webkit-scrollbar-thumb,
.low-stock-body::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.table-body::-webkit-scrollbar-thumb:hover,
.low-stock-body::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>

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

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('dashboardSearch');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                console.log('Searching for:', this.value);
            }
        });
    }
});
</script>