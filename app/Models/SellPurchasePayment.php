<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellPurchasePayment extends Model
{
    protected $fillable = [
        'sell_purchase_entry_id',
        'amount',
        'payment_method',
        'date',
        'received_by',
        'notes',
        'invoice_path',
        'invoice_name',
    ];

    protected $casts = [
        'date'   => 'date',
        'amount' => 'decimal:2',
    ];

    public function entry()
    {
        return $this->belongsTo(SellPurchaseEntry::class, 'sell_purchase_entry_id');
    }
}
