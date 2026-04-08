<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Market extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'location', 'description', 'image'];

    public function shops() { return $this->hasMany(Shop::class); }
    public function sellPurchaseEntries() { return $this->hasMany(SellPurchaseEntry::class); }
    public function constructionItems() { return $this->hasMany(ConstructionItem::class); }
    public function ownerLedgers() { return $this->hasMany(OwnerLedger::class); }
}
