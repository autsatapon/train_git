<?php

class CartTrueyou extends Eloquent {

//	protected $table = 'cart_details';
    protected $softDelete = false;

    public function cart()
    {
        return $this->belongsTo('Cart');
    }

}