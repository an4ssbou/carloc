<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, HasRoles, Notifiable, SoftDeletes;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone',
        'cin_number', 'cin_photo', 'license_photo',
        'profile_photo', 'role', 'has_store', 'is_active', 'password',
    ];

    protected $hidden = ['password', 'remember_token', 'cin_number', 'cin_photo', 'license_photo'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'has_store' => 'boolean',
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    public function agency()
    {
        return $this->hasOne(Agency::class, 'founder_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getProfilePhotoUrlAttribute(): string
    {
        if ($this->profile_photo) {
            return asset('storage/'.$this->profile_photo);
        }

        return asset('images/default-avatar.svg');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAgencyOwner(): bool
    {
        return $this->role === 'agency_owner';
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    public function hasActiveAgency(): bool
    {
        return $this->agency?->status === 'active';
    }
}
