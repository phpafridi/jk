<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ConstructionDocument extends Model
{
    protected $fillable = ['construction_item_id', 'name', 'path', 'type'];

    public function constructionItem()
    {
        return $this->belongsTo(ConstructionItem::class);
    }
}
