<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    
    protected $fillable = [
        'id', 
        'hotel_id', 
        'name', 
        'price', 
        'active'
    ];

  
    public $incrementing = false;
}
