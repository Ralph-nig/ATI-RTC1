@php
    $activeWarnings = \App\Models\EquipmentMaintenanceWarning::with('equipment')
        ->where('status', 'active')
        ->whereHas('equipment', function($query) {
            $query->whereNotNull('maintenance_schedule_end');
        })
        ->orderBy('warning_type', 'desc')
        ->get();
@endphp

@if($activeWarnings->count() > 0 && !session('maintenance_notification_dismissed_' . date('Y-m-d')))
<div id="maintenanceNotificationOverlay" style="
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.7);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.3s ease;
">
    <div style="
        background: white;
        border-radius: 15px;
        max-width: 600px;
        width: 90%;
        max-height: 80vh;
        overflow-y: auto;
        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        animation: slideUp 0.3s ease;
    ">
        <!-- Header -->
        <div style="
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            color: white;
            padding: 20px;
            border-radius: 15px 15px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        ">
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-exclamation-triangle" style="font-size: 24px; animation: pulse 2s infinite;"></i>
                <div>
                    <h2 style="margin: 0; font-size: 20px;">⚠️ Maintenance Alert!</h2>
                    <p style="margin: 5px 0 0 0; font-size: 14px; opacity: 0.9;">
                        {{ $activeWarnings->count() }} equipment item(s) need attention
                    </p>
                </div>
            </div>
            <button onclick="closeMaintenanceNotification()" style="
                background: transparent;
                border: none;
                color: white;
                font-size: 28px;
                cursor: pointer;
                line-height: 1;
                transition: transform 0.2s;
            " onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'">&times;</button>
        </div>

        <!-- Warnings List -->
        <div style="padding: 20px;">
            @foreach($activeWarnings as $warning)
            <div style="
                border-left: 4px solid {{ $warning->warning_type === 'critical' ? '#721c24' : ($warning->warning_type === 'overdue' ? '#dc3545' : '#ffc107') }};
                background: {{ $warning->warning_type === 'critical' ? '#f8d7da' : ($warning->warning_type === 'overdue' ? '#fff5f5' : '#fff9e6') }};
                padding: 15px;
                margin-bottom: 15px;
                border-radius: 8px;
                transition: transform 0.2s, box-shadow 0.2s;
            " onmouseover="this.style.transform='translateX(5px)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='translateX(0)'; this.style.boxShadow='none'">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                    <div>
                        <h3 style="margin: 0; font-size: 16px; color: #333;">
                            🔧 {{ $warning->equipment->article }}
                        </h3>
                        <p style="margin: 5px 0 0 0; font-size: 12px; color: #666;">
                            Property #{{ $warning->equipment->property_number }}
                        </p>
                    </div>
                    <span style="
                        background: {{ $warning->warning_type === 'critical' ? '#721c24' : ($warning->warning_type === 'overdue' ? '#dc3545' : '#ffc107') }};
                        color: {{ $warning->warning_type === 'due_soon' ? '#000' : '#fff' }};
                        padding: 4px 10px;
                        border-radius: 12px;
                        font-size: 11px;
                        font-weight: bold;
                        text-transform: uppercase;
                    ">{{ $warning->formatted_warning_type }}</span>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 13px; margin-bottom: 10px;">
                    <div>
                        <span style="color: #666;">Due Date:</span>
                        <strong style="color: #dc3545;">
                            {{ $warning->equipment->maintenance_schedule_end ? $warning->equipment->maintenance_schedule_end->format('M d, Y') : 'N/A' }}
                        </strong>
                    </div>
                    <div>
                        <span style="color: #666;">Location:</span>
                        <strong>{{ $warning->equipment->location ?: 'N/A' }}</strong>
                    </div>
                    @if($warning->equipment->responsible_person)
                    <div style="grid-column: 1 / -1;">
                        <span style="color: #666;">Responsible:</span>
                        <strong>{{ $warning->equipment->responsible_person }}</strong>
                    </div>
                    @endif
                </div>

                <a href="{{ route('client.equipment.maintenance.warnings') }}" style="
                    display: inline-block;
                    background: #007bff;
                    color: white;
                    padding: 8px 15px;
                    border-radius: 5px;
                    text-decoration: none;
                    font-size: 13px;
                    margin-top: 5px;
                    transition: background 0.2s;
                " onmouseover="this.style.background='#0056b3'" onmouseout="this.style.background='#007bff'">
                    <i class="fas fa-wrench"></i> Take Action Now
                </a>
            </div>
            @endforeach
        </div>

        <!-- Footer -->
        <div style="
            background: #f8f9fa;
            padding: 15px 20px;
            border-radius: 0 0 15px 15px;
            text-align: center;
            border-top: 1px solid #dee2e6;
        ">
            <a href="{{ route('client.equipment.maintenance.warnings') }}" style="
                color: #007bff;
                text-decoration: none;
                font-weight: 600;
                transition: color 0.2s;
            " onmouseover="this.style.color='#0056b3'" onmouseout="this.style.color='#007bff'">
                <i class="fas fa-list"></i> View All Warnings
            </a>
            <span style="margin: 0 10px; color: #dee2e6;">|</span>
            <a href="#" onclick="closeMaintenanceNotification(); return false;" style="
                color: #6c757d;
                text-decoration: none;
                transition: color 0.2s;
            " onmouseover="this.style.color='#495057'" onmouseout="this.style.color='#6c757d'">
                <i class="fas fa-times"></i> Dismiss for Today
            </a>
        </div>
    </div>
</div>

<style>
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from { 
        transform: translateY(50px);
        opacity: 0;
    }
    to { 
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

@keyframes fadeOut {
    from { opacity: 1; }
    to { opacity: 0; }
}
</style>

<script>
function closeMaintenanceNotification() {
    const overlay = document.getElementById('maintenanceNotificationOverlay');
    if (!overlay) return;
    
    overlay.style.animation = 'fadeOut 0.3s ease';
    setTimeout(() => {
        overlay.remove();
        // Store in session to not show again today
        fetch('/client/equipment/maintenance/dismiss-notification', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).catch(err => console.log('Could not save dismiss state'));
    }, 300);
}

// Close on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const overlay = document.getElementById('maintenanceNotificationOverlay');
        if (overlay) {
            closeMaintenanceNotification();
        }
    }
});

// Auto-close after 60 seconds if user doesn't interact
setTimeout(() => {
    const overlay = document.getElementById('maintenanceNotificationOverlay');
    if (overlay) {
        // Add a small notification before closing
        const footer = overlay.querySelector('div[style*="background: #f8f9fa"]');
        if (footer) {
            footer.innerHTML = '<p style="color: #dc3545; margin: 0; font-size: 13px;"><i class="fas fa-clock"></i> Auto-closing in 5 seconds...</p>';
        }
        setTimeout(closeMaintenanceNotification, 5000);
    }
}, 55000);
</script>
@endif