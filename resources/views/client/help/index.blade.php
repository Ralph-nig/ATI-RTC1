<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help & Support</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
        <div class="container">
        @include('layouts.core.sidebar')
        
        <div class="details">
            @include('layouts.core.header')
            <div class="help-container">
                <div class="help-header">
                    <h1><ion-icon name="help-circle-outline"></ion-icon> Help & Support</h1>
                    @if(!auth()->user()->isAdmin())
                    <a href="{{ route('help.create') }}" class="btn btn-primary">
                        <ion-icon name="add-outline"></ion-icon> Submit New Request
                    </a>
                    @endif
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="help-requests">
                    @if($helpRequests->count() > 0)
                        @foreach($helpRequests as $request)
                        <div class="help-card">
                            <div class="help-card-header">
                                <div class="help-info">
                                    <h3>{{ $request->subject }}</h3>
                                    @if(auth()->user()->isAdmin())
                                        <span class="user-name">by {{ $request->user->name }}</span>
                                    @endif
                                    <span class="help-date">{{ $request->created_date }}</span>
                                </div>
                                <div class="help-badges">
                                    <span class="badge badge-{{ $request->priority_color }}">{{ ucfirst($request->priority) }}</span>
                                    <span class="badge badge-{{ $request->status_color }}">{{ ucfirst(str_replace('_', ' ', $request->status)) }}</span>
                                </div>
                            </div>
                            <div class="help-card-body">
                                <p>{{ Str::limit($request->description, 150) }}</p>
                                @if($request->admin_response)
                                    <div class="admin-response">
                                        <strong>Admin Response:</strong>
                                        <p>{{ Str::limit($request->admin_response, 100) }}</p>
                                    </div>
                                @endif
                            </div>
                            <div class="help-card-actions">
                                <a href="{{ route('help.show', $request->id) }}" class="btn btn-sm btn-outline">View Details</a>
                                @if(auth()->user()->isAdmin() || ($request->user_id === auth()->id() && $request->status === 'pending'))
                                    <a href="{{ route('help.edit', $request->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                @endif
                                @if(auth()->user()->isAdmin() || ($request->user_id === auth()->id() && $request->status === 'pending'))
                                    <button class="btn btn-sm btn-danger" onclick="deleteRequest({{ $request->id }})">Delete</button>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <ion-icon name="help-circle-outline"></ion-icon>
                            <h3>No help requests found</h3>
                            @if(!auth()->user()->isAdmin())
                                <p>Submit your first help request to get started.</p>
                                <a href="{{ route('client.help.create') }}" class="btn btn-primary">Submit Request</a>
                            @else
                                <p>No help requests have been submitted yet.</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>

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

.help-container {
    max-width: 100%;
}

.help-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding: 20px 0;
    border-bottom: 2px solid #e0e0e0;
}

.help-header h1 {
    color: #296218;
    font-size: 28px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

.help-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.help-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.help-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 20px;
    border-bottom: 1px solid #f0f0f0;
}

.help-info h3 {
    color: #333;
    font-size: 18px;
    font-weight: 600;
    margin: 0 0 5px 0;
}

.user-name {
    color: #666;
    font-size: 14px;
    font-style: italic;
}

.help-date {
    color: #888;
    font-size: 12px;
}

.help-badges {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-success { background: #d4edda; color: #155724; }
.badge-warning { background: #fff3cd; color: #856404; }
.badge-danger { background: #f8d7da; color: #721c24; }
.badge-info { background: #d1ecf1; color: #0c5460; }
.badge-secondary { background: #e2e3e5; color: #383d41; }

.help-card-body {
    padding: 20px;
}

.admin-response {
    background: #f8f9fa;
    padding: 15px;
    border-left: 4px solid #296218;
    margin-top: 15px;
    border-radius: 0 8px 8px 0;
}

.help-card-actions {
    padding: 15px 20px;
    background: #f8f9fa;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.btn-primary { background: #296218; color: white; }
.btn-primary:hover { background: #1e4612; }

.btn-warning { background: #ffc107; color: #212529; }
.btn-warning:hover { background: #e0a800; }

.btn-danger { background: #dc3545; color: white; }
.btn-danger:hover { background: #c82333; }

.btn-outline { background: transparent; color: #296218; border: 1px solid #296218; }
.btn-outline:hover { background: #296218; color: white; }

</style>

<script>
function deleteRequest(id) {
    if (confirm('Are you sure you want to delete this help request?')) {
        fetch(`/client/help/${id}`, {
            method: 'DELETE',
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
        .catch(error => console.error('Error:', error));
    }
}
</script>