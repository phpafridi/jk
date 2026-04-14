<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class RentEntry extends Model
{
    protected $fillable = [
        'rent_shop_id','shop_number','rent','date',
        'customer_id','received_by','paid_to','authorized_by',
        'payment_method','amount_paid','notes'
    ];
    protected $casts = [
        'date'        => 'date',
        'rent'        => 'decimal:2',
        'amount_paid' => 'decimal:2',
    ];

    public function rentShop() { return $this->belongsTo(RentShop::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function documents(){ return $this->morphMany(\App\Models\EntryDocument::class, 'documentable'); }
}
