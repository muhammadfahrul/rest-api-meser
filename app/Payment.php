<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 't_payments';

    public function order(){
        return $this->belongsTo('App\Order');
    }
}