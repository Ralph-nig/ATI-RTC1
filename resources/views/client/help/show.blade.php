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
        <div class="help-detail-container">
            <div class="help-detail-header">
                <div class="header-left">
                    <h1><ion-icon name="document-text-outline"></ion-icon> Help Request Details</h1>
                    <div class="help-meta">
                        <span class="badge badge-{{ $helpRequest->priority_color }}">{{ ucfirst($helpRequest->priority) }}</span>
                        <span class="badge badge-{{ $helpRequest->status_color }}">{{ ucfirst(str_replace('_', ' ', $helpRequest->status)) }}</span>
                        <span class="help-date">{{ $helpRequest->created_date }}</span>
                    </div>
                </div>
                <div class="header-actions">
                    @if(auth()->user()->isAdmin() || ($helpRequest->user_id === auth()->id() && $helpRequest->status === 'pending'))
                        <a href="{{ route('help.edit', $helpRequest->id) }}" class="btn btn-warning">
                            <ion-icon name="create-outline"></ion-icon> Edit
                        </a>
                    @endif
                    <a href="{{ route('client.help.index') }}" class="btn btn-secondary">
                        <ion-icon name="arrow-back-outline"></ion-icon> Back
                    </a>
                </div>
            </div>

            <div class="help-detail-content">
                <div class="help-card">
                    <div class="card-section">
                        <h3><ion-icon name="person-outline"></ion-icon> Submitted by</h3>
                        <div class="user-info-detailed">
                            <img src="{{ $helpRequest->user->avatar_url }}" alt="{{ $helpRequest->user->name }}" class="user-avatar">
                            <div>
                                <strong>{{ $helpRequest->user->name }}</strong>
                                <p>{{ $helpRequest->user->email }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="card-section">
                        <h3><ion-icon name="text-outline"></ion-icon> Subject</h3>
                        <p class="subject">{{ $helpRequest->subject }}</p>
                    </div>

                    <div class="card-section">
                        <h3><ion-icon name="document-outline"></ion-icon> Description</h3>
                        <div class="description">{{ $helpRequest->description }}</div>
                    </div>

                    @if($helpRequest->admin_response)
                    <div class="card-section">
                        <h3><ion-icon name="chatbubble-outline"></ion-icon> Admin Response</h3>
                        <div class="admin-response">
                            {{ $helpRequest->admin_response }}
                            @if($helpRequest->assignedTo)
                                <div class="assigned-info">
                                    <strong>Assigned to:</strong> {{ $helpRequest->assignedTo->name }}
                                </div>
                            @endif
                            @if($helpRequest->resolved_at)
                                <div class="resolved-info">
                                    <strong>Resolved on:</strong> {{ $helpRequest->resolved_at->format('d F, Y h:i A') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.details {
    position: relative;
    width: calc(100% - 300px) !important;
    left: 300px !important;
    min-height: 100vh;
    background: #f5f5f5;
    transition: 0.5s;
    padding: 20px;
    font-family: 'Inter', sans-serif;
    box-sizing: border-box;
}

.help-detail-container {
    max-width: 100%;
}

.help-detail-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding: 20px 0;
    border-bottom: 2px solid #e0e0e0;
}

.header-left h1 {
    color: #296218;
    font-size: 28px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

.help-meta {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
}

.header-actions {
    display: flex;
    gap: 10px;
}

.help-detail-content {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.card-section {
    padding: 25px;
    border-bottom: 1px solid #f0f0f0;
}

.card-section:last-child {
    border-bottom: none;
}

.card-section h3 {
    color: #333;
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.user-info-detailed {
    display: flex;
    align-items: center;
    gap: 15px;
}

.user-info-detailed .user-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
}

.subject {
    font-size: 18px;
    font-weight: 500;
    color: #333;
    margin: 0;
}

.description {
    color: #666;
    line-height: 1.6;
    white-space: pre-wrap;
}

.admin-response {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    border-left: 4px solid #296218;
    color: #666;
    line-height: 1.6;
    white-space: pre-wrap;
}

.assigned-info,
.resolved-info {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #e0e0e0;
    color: #888;
    font-size: 14px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.readonly {
    background: #f8f9fa;
    color: #666;
    cursor: not-allowed;
}

@media (max-width: 768px) {
    .help-detail-header {
        flex-direction: column;
        gap: 20px;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .header-actions {
        width: 100%;
        justify-content: flex-end;
    }
}
</style>