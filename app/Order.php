<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    public function product(){
        return $this->belongsTo('App\Product');
    }

    public function payment(){
        return $this->hasMany('App\Payment');
    }
}