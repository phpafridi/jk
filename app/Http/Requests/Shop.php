<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shop extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'market_id','shop_number','owner_id','customer_id','type',
        'date_of_payment','total_amount','paid_amount','rent_amount','status',
    ];
    protected $casts = [
        'date_of_payment' => 'date',
        'total_amount'    => 'decimal:2',
        'paid_amount'     => 'decimal:2',
        'rent_amount'     => 'decimal:2',
    ];

    public function market()    { return $this->belongsTo(Market::class); }
    public function owner()     { return $this->belongsTo(Owner::class, 'owner_id'); }
    public function payments()  { return $this->hasMany(ShopPayment::class); }
    public function documents() { return $this->hasMany(ShopDocument::class); }
    public function rentEntries(){ return $this->hasMany(RentEntry::class); }
    public function customers() { return $this->hasMany(Customer::class); }
}
