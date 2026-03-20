<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{


    protected $fillable = [
        'id', 
        'hotel_id', 
        'name', 
        'inventory_count'
    ];

    public $incrementing = false;


public function hotel() {
return $this->belongsTo(Hotel::class);
}
}
