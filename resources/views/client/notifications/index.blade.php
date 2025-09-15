<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container">
    @include('layouts.core.sidebar')
    <div class="details">
        @include('layouts.core.header')
        <div class="notifications-container">
            <div class="notifications-header">
                <h1><ion-icon name="notifications-outline"></ion-icon> All Notifications</h1>
                <button class="btn btn-primary" onclick="markAllAsRead()">
                    <ion-icon name="checkmark-done-outline"></ion-icon> Mark All as Read
                </button>
            </div>

            <div class="notifications-content">
                @if($notifications->count() > 0)
                    <div class="notifications-list">
                        @foreach($notifications as $notification)
                        <div class="notification-card {{ !$notification->is_read ? 'unread' : '' }}" 
                            onclick="markAsReadAndRedirect({{ $notification->id }}, '{{ getNotificationUrl($notification) }}')">
                            <div class="notification-icon">
                                @switch($notification->type)
                                    @case('help_request')
                                        <ion-icon name="help-circle-outline" style="color: #dc3545;"></ion-icon>
                                        @break
                                    @case('help_response')
                                        <ion-icon name="chatbubble-outline" style="color: #28a745;"></ion-icon>
                                        @break
                                    @default
                                        <ion-icon name="information-circle-outline" style="color: #17a2b8;"></ion-icon>
                                @endswitch
                            </div>
                            <div class="notification-content">
                                <div class="notification-header">
                                    <h4>{{ $notification->title }}</h4>
                                    <span class="notification-time">{{ $notification->created_date }}</span>
                                </div>
                                <p class="notification-message">{{ $notification->message }}</p>
                                @if($notification->data && isset($notification->data['priority']))
                                    <div class="notification-meta">
                                        <span class="priority-badge priority-{{ $notification->data['priority'] }}">
                                            {{ ucfirst($notification->data['priority']) }} Priority
                                        </span>
                                    </div>
                                @endif
                            </div>
                            @if(!$notification->is_read)
                                <div class="unread-indicator"></div>
                            @endif
                        </div>
                        @endforeach
                    </div>

                    <div class="pagination-wrapper">
                        {{ $notifications->links() }}
                    </div>
                @else
                    <div class="empty-state">
                        <ion-icon name="notifications-off-outline"></ion-icon>
                        <h3>No notifications yet</h3>
                        <p>You'll see notifications here when there are updates or responses to your help requests.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
/* Dashboard Header - Add higher z-index */
.dashboard-header {
    background: #296218;
    color: white;
    padding: 20px 30px;
    border-radius: 10px;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 15px rgba(41, 98, 24, 0.3);
    position: relative; /* Add this */
    z-index: 1000; /* Add this */
}

.header-right {
    display: flex;
    align-items: center;
    gap: 20px;
    position: relative; /* Add this */
}

.notifications {
    position: relative;
    cursor: pointer;
    background: rgba(255, 255, 255, 0.2);
    padding: 12px;
    border-radius: 50%;
    transition: all 0.3s ease;
    z-index: 1001; /* Add this */
}

.notification-dropdown {
    position: absolute;
    top: 50px;
    right: 0;
    width: 350px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    z-index: 9999; /* Increase this value significantly */
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s ease;
    border: 1px solid #ddd; /* Add border for better visibility */
}

.notification-dropdown.active {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

/* Additional fix for the main content area */
.details {
    position: relative;
    width: calc(100% - 300px);
    left: 300px;
    min-height: 100vh;
    background: #f5f5f5;
    transition: 0.5s;
    padding: 20px;
    z-index: 1; /* Add this to ensure main content stays below */
}

/* Fix for any overlapping elements */
.main-content, 
.content-area, 
.page-content {
    position: relative;
    z-index: 1;
}

/* Ensure dropdown appears above all other content */
.notification-dropdown {
    position: fixed !important; /* Change from absolute to fixed */
    top: auto !important;
    right: 30px !important; /* Adjust based on your layout */
    z-index: 999999 !important;
}

/* Alternative approach - if fixed positioning causes issues, use this instead */
/*
.notification-dropdown {
    position: absolute;
    top: 60px;
    right: 0;
    width: 350px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    z-index: 999999;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s ease;
    border: 1px solid #ddd;
}
*/

/* Rest of your existing styles remain the same */
.notification-header {
    padding: 20px 20px 15px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.notification-header h4 {
    margin: 0;
    color: #333;
    font-size: 16px;
    font-weight: 600;
}

.mark-all-read {
    background: none;
    border: none;
    color: #296218;
    font-size: 12px;
    cursor: pointer;
    font-weight: 500;
    transition: color 0.3s ease;
}

.mark-all-read:hover {
    color: #1e4612;
    text-decoration: underline;
}

.notification-list {
    max-height: 400px;
    overflow-y: auto;
}

.notification-item {
    padding: 15px 20px;
    border-bottom: 1px solid #f0f0f0;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.notification-item:hover {
    background-color: #f8f9fa;
}

.notification-item.unread {
    background-color: #f0f8ff;
    border-left: 3px solid #296218;
}

.notification-title {
    font-weight: 600;
    color: #333;
    font-size: 14px;
    margin-bottom: 4px;
}

.notification-message {
    color: #666;
    font-size: 13px;
    line-height: 1.4;
    margin-bottom: 6px;
}

.notification-time {
    color: #999;
    font-size: 11px;
}

.notification-footer {
    padding: 15px 20px;
    text-align: center;
    border-top: 1px solid #eee;
}

.notification-footer a {
    color: #296218;
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    transition: color 0.3s ease;
}

.notification-footer a:hover {
    color: #1e4612;
    text-decoration: underline;
}

.empty-notifications {
    padding: 30px 20px;
    text-align: center;
    color: #999;
}

.empty-notifications ion-icon {
    font-size: 32px;
    margin-bottom: 10px;
    opacity: 0.5;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .notification-dropdown {
        width: 300px;
        right: 10px !important;
    }
    
    .header-right {
        gap: 10px;
    }
    
    .user-info {
        display: none;
    }
}

<script>
function markAsReadAndRedirect(notificationId, url) {
    fetch(`/client/notifications/${notificationId}/mark-as-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && url && url !== '#') {
            window.location.href = url;
        } else if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('Error marking notification as read:', error));
}

function markAllAsRead() {
    if (confirm('Mark all notifications as read?')) {
        fetch('/client/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => console.error('Error marking all notifications as read:', error));
    }
}

function getNotificationUrl(notification) {
    if (notification.type === 'help_request' || notification.type === 'help_response') {
        if (notification.data && notification.data.help_request_id) {
            return `/client/help/${notification.data.help_request_id}`;
        }
    }
    return '#';
}
</script>

@php
function getNotificationUrl($notification) {
    if ($notification->type === 'help_request' || $notification->type === 'help_response') {
        if ($notification->data && isset($notification->data['help_request_id'])) {
            return route('help.show', $notification->data['help_request_id']);
        }
    }
    return '#';
}
@endphp