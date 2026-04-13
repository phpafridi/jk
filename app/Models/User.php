<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'father_name',
        'email',
        'cnic',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // Shops owned by this user
    public function ownedShops()
    {
        return $this->hasMany(Shop::class, 'owner_id');
    }

    // Customer profile linked to this user
    public function customerProfile()
    {
        return $this->hasOne(Customer::class, 'linked_user_id');
    }

    // Owner ledger entries
    public function ledgerEntries()
    {
        return $this->hasMany(OwnerLedger::class, 'owner_id');
    }
}
