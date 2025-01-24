<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'address',
        'city',
        'state',
        'zip',
        'country',
        'is_default',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getFullNameAttribute()
    {
        return "{$this->fitst_name} {$this->last_name}";
    }
}
