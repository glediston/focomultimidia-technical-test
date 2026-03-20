<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{


    protected $fillable = ['id', 'name'];

  
    public $incrementing = false;


 public function rooms() {
 return $this->hasMany(Room::class);
}

public function rates() {
    return $this->hasMany(Rate::class);
}
}
