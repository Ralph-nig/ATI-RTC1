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
        <div class="form-container">
            <div class="form-header">
                <h2><ion-icon name="create-outline"></ion-icon> Edit Help Request</h2>
                <a href="{{ route('help.show', $helpRequest->id) }}" class="btn btn-secondary">
                    <ion-icon name="arrow-back-outline"></ion-icon> Back
                </a>
            </div>

            @if(auth()->user()->isAdmin())
                {{-- Admin Edit Form --}}
                <form action="{{ route('help.update', $helpRequest->id) }}" method="POST" class="help-form">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Subject</label>
                            <input type="text" value="{{ $helpRequest->subject }}" readonly class="readonly">
                        </div>
                        <div class="form-group">
                            <label>Priority</label>
                            <input type="text" value="{{ ucfirst($helpRequest->priority) }}" readonly class="readonly">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea readonly class="readonly" rows="4">{{ $helpRequest->description }}</textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="status">Status *</label>
                            <select id="status" name="status" required>
                                <option value="pending" {{ $helpRequest->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="in_progress" {{ $helpRequest->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="resolved" {{ $helpRequest->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="closed" {{ $helpRequest->status === 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="assigned_to">Assign To</label>
                            <select id="assigned_to" name="assigned_to">
                                <option value="">Select Admin</option>
                                @foreach(\App\Models\User::where('role', 'admin')->get() as $admin)
                                    <option value="{{ $admin->id }}" {{ $helpRequest->assigned_to === $admin->id ? 'selected' : '' }}>
                                        {{ $admin->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="admin_response">Admin Response</label>
                        <textarea id="admin_response" name="admin_response" rows="6" placeholder="Provide response to the user...">{{ $helpRequest->admin_response }}</textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Update Request</button>
                        <a href="{{ route('help.show', $helpRequest->id) }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            @else
                {{-- User Edit Form (only for pending requests) --}}
                <form action="{{ route('help.update', $helpRequest->id) }}" method="POST" class="help-form">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group">
                        <label for="subject">Subject *</label>
                        <input type="text" id="subject" name="subject" value="{{ old('subject', $helpRequest->subject) }}" required>
                        @error('subject')<span class="error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="priority">Priority *</label>
                        <select id="priority" name="priority" required>
                            <option value="low" {{ old('priority', $helpRequest->priority) === 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('priority', $helpRequest->priority) === 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('priority', $helpRequest->priority) === 'high' ? 'selected' : '' }}>High</option>
                        </select>
                        @error('priority')<span class="error">{{ $message }}</span>@enderror
                    </div>
        x
                    <div class="form-group">
                        <label for="description">Description *</label>
                        <textarea id="description" name="description" rows="6" required>{{ old('description', $helpRequest->description) }}</textarea>
                        @error('description')<span class="error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Update Request</button>
                        <a href="{{ route('help.show', $helpRequest->id) }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            @endif
        </div>
    <style>
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .form-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e0e0e0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #296218;
        }
        .error {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .empty-state ion-icon {
            font-size: 64px;
            color: #ccc;
            margin-bottom: 20px;
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

        .btn-secondary { background: #296218; color: white; }
        .btn-secondary:hover { background: #1e4612; }
    </style>