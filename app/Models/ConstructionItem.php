<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ConstructionItem extends Model
{
    protected $fillable = ['market_id','project_name','item_name','quantity','unit','measurement','unit_price','total','date','notes'];
    protected $casts    = ['date' => 'date', 'quantity' => 'decimal:2', 'unit_price' => 'decimal:2', 'total' => 'decimal:2'];

    public function market() { return $this->belongsTo(Market::class); }
}
