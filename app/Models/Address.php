<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;
    protected $fillable = ['event_id','address_line1', 'address_line2', 'city','state','country','pincode'];


    public function country()
    {
        return $this->hasOne(Country::class,'id','country');
    }
    public function state()
    {
        return $this->hasOne(State::class,'id','state');
    }
    public function city()
    {
        return $this->hasOne(city::class,'id','city');
    }
}

