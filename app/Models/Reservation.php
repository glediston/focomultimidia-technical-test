<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{


    protected $fillable = [
    'id',
    'hotel_id',
    'room_id',
    'customer_first_name',
    'customer_last_name',
    'arrival_date',
    'departure_date',
    'total_price'
];

  
    public $incrementing = false;

public function hotel() {
 return $this->belongsTo(Hotel::class);
}

public function room() {
    return $this->belongsTo(Room::class);
}
}
