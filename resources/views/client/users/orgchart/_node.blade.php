@php
    /*
     * Expects: $u (User model), $avatarColors (array)
     * Usage: @include('client.users.orgchart._node', ['u' => $user, 'avatarColors' => $avatarColors])
     */
    $role    = $u->role ?? 'user';
    $color   = $avatarColors[$role] ?? '#296218';
    $initials = orgInitials($u->name);
    $status  = $u->status ?? 'active';

    $perms = [
        'can_create'    => (bool) $u->can_create,
        'can_read'      => (bool) $u->can_read,
        'can_update'    => (bool) $u->can_update,
        'can_delete'    => (bool) $u->can_delete,
        'can_stock_in'  => (bool) $u->can_stock_in,
        'can_stock_out' => (bool) $u->can_stock_out,
        'can_request'   => (bool) $u->can_request,
    ];
@endphp

<div class="node-card"
     data-name="{{ $u->name }}"
     data-email="{{ $u->email }}"
     data-role="{{ ucfirst($role) }}"
     data-status="{{ $status }}"
     data-date="{{ $u->created_date ?? 'N/A' }}"
     data-initials="{{ $initials }}"
     data-color="{{ $color }}"
     data-perms='@json($perms)'
     title="{{ $u->name }} — click for details">

    <span class="status-dot status-{{ $status }}"></span>

    <div class="node-avatar" style="background: {{ $color }};">
        {{ $initials }}
    </div>

    <div class="node-name">{{ $u->name }}</div>
    <div class="node-email">{{ $u->email }}</div>

    @if($role === 'admin')
        <span class="role-pill role-admin"><i class="fas fa-crown" style="font-size:9px;"></i> Admin</span>
    @elseif($role === 'requestor')
        <span class="role-pill role-requestor"><i class="fas fa-file-alt" style="font-size:9px;"></i> Requestor</span>
    @else
        <span class="role-pill role-user"><i class="fas fa-user" style="font-size:9px;"></i> User</span>
    @endif
</div>