<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ShopDocument extends Model
{
    protected $fillable = ['shop_id','name','path','type'];
    public function shop() { return $this->belongsTo(Shop::class); }
}
