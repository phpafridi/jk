<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SellShop extends Model
{
    protected $fillable = ['sell_market_id', 'shop_number', 'type', 'status', 'area_sqft', 'notes'];

    public function sellMarket() { return $this->belongsTo(SellMarket::class); }
}
