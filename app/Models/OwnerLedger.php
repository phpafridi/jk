<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class OwnerLedger extends Model
{
    protected $fillable = ['owner_id','market_id','shop_id','transaction_type','amount','date','description','reference'];
    protected $casts    = ['date' => 'date', 'amount' => 'decimal:2'];

    public function owner()  { return $this->belongsTo(User::class, 'owner_id'); }
    public function market() { return $this->belongsTo(Market::class); }
    public function shop()   { return $this->belongsTo(Shop::class); }
}
