<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'father_name', 'phone', 'cnic', 'address', 'email', 'shop_id', 'notes'];

    public function shop()      { return $this->belongsTo(Shop::class); }
    public function documents() { return $this->hasMany(CustomerDocument::class); }
}
