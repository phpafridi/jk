<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ShopPayment extends Model
{
    protected $fillable = ['shop_id','user_id','amount','payment_date','payment_method','notes','receipt_number'];
    protected $casts    = ['payment_date' => 'date', 'amount' => 'decimal:2'];

    public function shop()       { return $this->belongsTo(Shop::class); }
    // receipt view uses $payment->recorder — map both names
    public function recordedBy() { return $this->belongsTo(User::class, 'user_id'); }
    public function recorder()   { return $this->belongsTo(User::class, 'user_id'); }
}
