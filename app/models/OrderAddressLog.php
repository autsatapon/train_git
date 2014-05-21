<?php

class OrderAddressLog extends Eloquent {

    protected $table = 'order_address_logs';

    public function order()
    {
        return $this->belongsTo('Order');
    }

	public function user()
    {
        return $this->belongsTo('User' , 'actor_id');
    }

}

