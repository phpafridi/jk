<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RentMarket extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'location', 'description'];

    public function shops() { return $this->hasMany(RentShop::class); }
}
