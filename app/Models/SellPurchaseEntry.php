<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SellPurchaseEntry extends Model
{
    protected $fillable = [
        'entry_type','transaction_type','sell_market_id','date','shop_or_item_number',
        'per_sqft_rate','sqft','total','seller_name','seller_cnic','seller_phone',
        'buyer_name','buyer_cnic','buyer_phone','car_make','car_model','car_year',
        'car_registration','notes',
        'seller_customer_id','buyer_customer_id',
        'seller_owner_id','buyer_owner_id',
    ];
    protected $casts = [
        'date'          => 'date',
        'per_sqft_rate' => 'decimal:2',
        'sqft'          => 'decimal:2',
        'total'         => 'decimal:2',
    ];

    public function sellMarket()      { return $this->belongsTo(SellMarket::class, 'sell_market_id'); }
    // keep a "market" alias so existing views that do $entry->market still work
    public function market()          { return $this->belongsTo(SellMarket::class, 'sell_market_id'); }
    public function documents()       { return $this->morphMany(EntryDocument::class, 'documentable'); }
    public function sellerCustomer()  { return $this->belongsTo(Customer::class, 'seller_customer_id'); }
    public function buyerCustomer()   { return $this->belongsTo(Customer::class, 'buyer_customer_id'); }
    public function sellerOwner()     { return $this->belongsTo(Owner::class, 'seller_owner_id'); }
    public function buyerOwner()      { return $this->belongsTo(Owner::class, 'buyer_owner_id'); }
}
