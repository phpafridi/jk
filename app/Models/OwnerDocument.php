<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OwnerDocument extends Model
{
    protected $fillable = ['owner_id', 'name', 'path', 'type', 'doc_type'];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function docTypeLabel(): string
    {
        return match ($this->doc_type) {
            'cnic'      => 'CNIC',
            'mou'       => 'MOU',
            'agreement' => 'Agreement',
            'photo'     => 'Photo',
            default     => 'Other',
        };
    }
}
