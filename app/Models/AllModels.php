<?php
// ShopPayment.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ShopPayment extends Model {
    protected $fillable = ['shop_id','user_id','amount','payment_date','payment_method','notes','receipt_number'];
    protected $casts = ['payment_date' => 'date', 'amount' => 'decimal:2'];
    public function shop() { return $this->belongsTo(Shop::class); }
    public function recorder() { return $this->belongsTo(User::class, 'user_id'); }
}

// ShopDocument.php
class ShopDocument extends Model {
    protected $fillable = ['shop_id','name','path','type'];
    public function shop() { return $this->belongsTo(Shop::class); }
}

// RentEntry.php
class RentEntry extends Model {
    protected $fillable = ['shop_id','shop_number','rent','date','owner_id','received_by','amount_paid','notes'];
    protected $casts = ['date' => 'date', 'rent' => 'decimal:2', 'amount_paid' => 'decimal:2'];
    public function shop() { return $this->belongsTo(Shop::class); }
    public function owner() { return $this->belongsTo(User::class, 'owner_id'); }
}

// SellPurchaseEntry.php
class SellPurchaseEntry extends Model {
    protected $fillable = [
        'entry_type','transaction_type','market_id','date','shop_or_item_number',
        'per_sqft_rate','sqft','total','seller_name','seller_cnic','seller_phone',
        'buyer_name','buyer_cnic','buyer_phone','car_make','car_model','car_year',
        'car_registration','notes'
    ];
    protected $casts = ['date' => 'date', 'per_sqft_rate' => 'decimal:2', 'sqft' => 'decimal:2', 'total' => 'decimal:2'];
    public function market() { return $this->belongsTo(Market::class); }
    public function documents() { return $this->morphMany(EntryDocument::class, 'documentable'); }
}

// ConstructionItem.php
class ConstructionItem extends Model {
    protected $fillable = ['market_id','project_name','item_name','quantity','unit','measurement','unit_price','total','date','notes'];
    protected $casts = ['date' => 'date', 'quantity' => 'decimal:2', 'unit_price' => 'decimal:2', 'total' => 'decimal:2'];
    public function market() { return $this->belongsTo(Market::class); }
}

// OwnerLedger.php
class OwnerLedger extends Model {
    protected $fillable = ['owner_id','market_id','shop_id','transaction_type','amount','date','description','reference'];
    protected $casts = ['date' => 'date', 'amount' => 'decimal:2'];
    public function owner() { return $this->belongsTo(User::class, 'owner_id'); }
    public function market() { return $this->belongsTo(Market::class); }
    public function shop() { return $this->belongsTo(Shop::class); }
}

// Customer.php
class Customer extends Model {
    use \Illuminate\Database\Eloquent\SoftDeletes;
    protected $fillable = ['name','phone','cnic','address','email','linked_user_id','shop_id','notes'];
    public function linkedUser() { return $this->belongsTo(User::class, 'linked_user_id'); }
    public function shop() { return $this->belongsTo(Shop::class); }
    public function documents() { return $this->hasMany(CustomerDocument::class); }
}

// CustomerDocument.php
class CustomerDocument extends Model {
    protected $fillable = ['customer_id','name','path','type'];
    public function customer() { return $this->belongsTo(Customer::class); }
}

// EntryDocument.php
class EntryDocument extends Model {
    protected $fillable = ['name','path','type'];
    public function documentable() { return $this->morphTo(); }
}
