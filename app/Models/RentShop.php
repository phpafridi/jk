<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class RentShop extends Model
{
    protected $fillable = [
        'rent_market_id','shop_number','tenant_name','tenant_phone','tenant_cnic',
        'status','rent_amount','notes'
    ];

    public function rentMarket()  { return $this->belongsTo(RentMarket::class); }
    public function rentEntries() { return $this->hasMany(RentEntry::class); }
    public function documents()   { return $this->morphMany(EntryDocument::class, 'documentable'); }
}
