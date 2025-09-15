<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
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
        .form-container {
            background-color: #296218;
            border-radius: 15px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }
        
        .form-header {
            background: rgba(255,255,255,0.1);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            backdrop-filter: blur(10px);
        }
        
        .form-title {
            color: white;
            font-size: 24px;
            font-weight: bold;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-content {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #495057;
        }
        
        .form-label.required::after {
            content: " *";
            color: #dc3545;
        }
        
        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
            box-sizing: border-box;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #296218;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
        }
        
        .form-select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            background: white;
            cursor: pointer;
            box-sizing: border-box;
        }
        
        .form-select:focus {
            outline: none;
            border-color: #296218;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
        }
        
        .input-group {
            position: relative;
        }
        
        .input-group .form-input,
        .input-group .form-select {
            padding-left: 45px;
        }
        
        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 1;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            font-size: 14px;
        }
        
        .btn-success {
            background: #296218;
            color: white;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }
        
        .error-message {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
        }
        
        .alert {
            padding: 12px 16px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-weight: 500;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .back-button {
            display: flex;
            align-items: center;
            gap: 8px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            margin-bottom: 15px;
            transition: color 0.3s ease;
        }
        
        .back-button:hover {
            color: white;
        }
        
        .password-info {
            color: #6c757d;
            font-size: 12px;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        @include('layouts.core.sidebar')
        <div class="details">
            @include('layouts.core.header')
            
            <div class="form-container">
                <div class="form-header">
                    <a href="{{ url('client/users') }}" class="back-button">
                        <i class="fas fa-arrow-left"></i>
                        Back to Users
                    </a>
                    <h1 class="form-title">
                        <i class="fas fa-user-edit"></i>
                        Update User
                    </h1>
                </div>
                
                <div class="form-content">
                    @if(session('success'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                        </div>
                    @endif
                    
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>Please fix the following errors:</strong>
                            <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ url('client/users', $user->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-grid">
                            <!-- Full Name -->
                            <div class="form-group">
                                <label for="name" class="form-label required">Full Name</label>
                                <div class="input-group">
                                    <i class="fas fa-user"></i>
                                    <input type="text" id="name" name="name" class="form-input" 
                                           value="{{ $user->name ?? old('name') }}" required placeholder="Enter user's full name">
                                </div>
                                @error('name')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Email Address -->
                            <div class="form-group">
                                <label for="email" class="form-label required">Email Address</label>
                                <div class="input-group">
                                    <i class="fas fa-envelope"></i>
                                    <input type="email" id="email" name="email" class="form-input" 
                                           value="{{ $user->email ?? old('email') }}" required placeholder="Enter email address">
                                </div>
                                @error('email')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Password -->
                            <div class="form-group">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <i class="fas fa-lock"></i>
                                    <input type="password" id="password" name="password" class="form-input" 
                                           placeholder="Leave blank to keep current password">
                                </div>
                                <div class="password-info">
                                    <i class="fas fa-info-circle"></i>
                                    Leave blank to keep current password
                                </div>
                                @error('password')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Confirm Password -->
                            <div class="form-group">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <div class="input-group">
                                    <i class="fas fa-lock"></i>
                                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" 
                                           placeholder="Confirm new password">
                                </div>
                                @error('password_confirmation')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- User Role -->
                            <div class="form-group">
                                <label for="role" class="form-label required">User Role</label>
                                <div class="input-group">
                                    <i class="fas fa-user-tag"></i>
                                    <select id="role" name="role" class="form-select" required>
                                        <option value="">Select Role</option>
                                        <option value="user" {{ ($user->role ?? old('role')) == 'user' ? 'selected' : '' }}>User</option>
                                        <option value="admin" {{ ($user->role ?? old('role')) == 'admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                </div>
                                @error('role')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Status -->
                            <div class="form-group">
                                <label for="status" class="form-label required">Status</label>
                                <div class="input-group">
                                    <i class="fas fa-toggle-on"></i>
                                    <select id="status" name="status" class="form-select" required>
                                        <option value="">Select Status</option>
                                        <option value="active" {{ ($user->status ?? old('status')) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ ($user->status ?? old('status')) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                                @error('status')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i>
                                Back to Users
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i>
                                Update User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>