<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SellMarket extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'location', 'description'];

    public function shops() { return $this->hasMany(SellShop::class); }
    public function sellPurchaseEntries() { return $this->hasMany(SellPurchaseEntry::class, 'sell_market_id'); }
}
