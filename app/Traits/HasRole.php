<?php

namespace App\Traits;

trait HasRole
{
    public function hasRole(string $role): bool
    {
        return $this->role?->slug === $role;
    }

    public function getDashboardRoute(): string
    {
        return match ($this->role?->slug) {
            // keep 'admin' as an alias for backward compatibility
            'principal', 'admin' => 'principal.dashboard',
            'teacher' => 'teacher.dashboard',
            'parent' => 'parent.dashboard',
            default => 'home'
        };
    }

    public function role()
    {
        return $this->belongsTo(\App\Models\Role::class);
    }
}