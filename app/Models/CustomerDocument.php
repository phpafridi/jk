<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CustomerDocument extends Model
{
    protected $fillable = ['customer_id','name','path','type'];
    public function customer() { return $this->belongsTo(Customer::class); }
}
