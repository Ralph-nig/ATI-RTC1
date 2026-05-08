<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'org_unit',          
        'is_section_head',   
        'can_create',
        'can_read',
        'can_update',
        'can_delete',
        'can_stock_in',
        'can_stock_out',
        'can_request',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = ['created_date', 'avatar_url'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_section_head'   => 'boolean',   // NEW
            'can_create'        => 'boolean',
            'can_read'          => 'boolean',
            'can_update'        => 'boolean',
            'can_delete'        => 'boolean',
            'can_stock_in'      => 'boolean',
            'can_stock_out'     => 'boolean',
            'can_request'       => 'boolean',
        ];
    }

    public function getCreatedDateAttribute()
    {
        return $this->created_at ? $this->created_at->format('d F, Y') : 'N/A';
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return \Storage::url($this->avatar);
        }
        return asset('assets/img/noprofile.jpg');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isRequestor(): bool
    {
        return $this->role === 'requestor';
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isAdmin()) return true;

        return match($permission) {
            'create'    => $this->can_create,
            'read'      => $this->can_read,
            'update'    => $this->can_update,
            'delete'    => $this->can_delete,
            'stock_in'  => $this->can_stock_in,
            'stock_out' => $this->can_stock_out,
            'request'   => $this->can_request,
            default     => false,
        };
    }

    public function helpRequests()
    {
        return $this->hasMany(HelpRequest::class);
    }

    public function assignedHelpRequests()
    {
        return $this->hasMany(HelpRequest::class, 'assigned_to');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class)->orderBy('created_at', 'desc');
    }

    public function unreadNotifications()
    {
        return $this->hasMany(Notification::class)->where('is_read', false);
    }

    public function auditTrails()
    {
        return $this->hasMany(AuditTrail::class)->orderBy('created_at', 'desc');
    }

    public function recentAuditTrails(int $limit = 10)
    {
        return $this->auditTrails()->limit($limit)->get();
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function recentStockMovements(int $limit = 10)
    {
        return $this->stockMovements()->orderBy('created_at', 'desc')->limit($limit)->get();
    }
}