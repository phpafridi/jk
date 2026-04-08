<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntryDocument extends Model
{
    protected $fillable = ['name', 'path', 'type'];

    public function documentable()
    {
        return $this->morphTo();
    }
}
