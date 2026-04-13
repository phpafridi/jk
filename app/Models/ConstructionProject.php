<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConstructionProject extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'market_id', 'notes'];

    public function market()  { return $this->belongsTo(Market::class); }
    public function items()   { return $this->hasMany(ConstructionItem::class, 'project_name', 'name'); }
}
