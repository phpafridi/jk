<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SellPurchaseEntry extends Model
{
    protected $fillable = [
        'entry_type','transaction_type','market_id','date','shop_or_item_number',
        'per_sqft_rate','sqft','total','seller_name','seller_cnic','seller_phone',
        'buyer_name','buyer_cnic','buyer_phone','car_make','car_model','car_year',
        'car_registration','notes',
    ];
    protected $casts = ['date' => 'date', 'per_sqft_rate' => 'decimal:2', 'sqft' => 'decimal:2', 'total' => 'decimal:2'];

    public function market() { return $this->belongsTo(Market::class); }
}
