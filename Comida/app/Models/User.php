<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'rut', 'role', 'phone', 'address', 'commune_id', 'is_active', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Relationship with Commune.
     */
    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }

    /**
     * Relationship with Orders.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Relationship with Tasks.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class, 'employee_id');
    }

    /**
     * Relationship with CashRegisters.
     */
    public function cashRegisters()
    {
        return $this->hasMany(CashRegister::class);
    }

    // Role helper checks
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isChef(): bool
    {
        return $this->role === 'chef';
    }

    public function isWaiter(): bool
    {
        return $this->role === 'waiter';
    }

    public function isDelivery(): bool
    {
        return $this->role === 'delivery';
    }

    public function isAccountant(): bool
    {
        return $this->role === 'accountant';
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    public function hasRole(array|string $roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }
        return $this->role === $roles;
    }


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
