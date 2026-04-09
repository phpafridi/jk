<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntryDocument extends Model
{
    protected $fillable = ['name', 'path', 'type', 'documentable_id', 'documentable_type'];

    public function documentable()
    {
        return $this->morphTo();
    }
}
