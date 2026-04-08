<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class RentEntry extends Model
{
    protected $fillable = ['shop_id','shop_number','rent','date','owner_id','received_by','amount_paid','notes'];
    protected $casts    = ['date' => 'date', 'rent' => 'decimal:2', 'amount_paid' => 'decimal:2'];

    public function shop()  { return $this->belongsTo(Shop::class); }
    public function owner() { return $this->belongsTo(User::class, 'owner_id'); }
}
