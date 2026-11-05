<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $fillable = ['name', 'username', 'password', 'permissions', 'profile_photo'];
    protected $hidden = ['password', 'remember_token'];
    protected function casts(): array
    {
        return ['password' => 'hashed', 'permissions' => 'array'];
    }
    public function canAccess(string $permission): bool
    {
        if ($this->username === 'admin' || strtolower($this->username) === 'atnan') return true;
        $permissions = $this->permissions ?? [];
        if (in_array($permission, $permissions, true)) return true;
        if ($permission === 'display.view') return true; // TV display dapat dibuka oleh semua user login
        if (($permission === 'audit.view' || $permission === 'backup.manage') && in_array('user.manage', $permissions, true)) return true;
        if ($permission === 'schedule.view' && (in_array('schedule.manage', $permissions, true) || in_array('schedule.export', $permissions, true))) return true;
        if ($permission === 'shift.manage' && in_array('shift.export', $permissions, true)) return true;
        return false;
    }
}
