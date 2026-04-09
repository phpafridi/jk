<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Owner extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'phone', 'cnic', 'address', 'email', 'notes'];

    public function shops()       { return $this->hasMany(Shop::class); }
    public function rentEntries() { return $this->hasMany(RentEntry::class); }
    public function ledgers()     { return $this->hasMany(OwnerLedger::class); }
}
