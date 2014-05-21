<?php

class ShippingMethodArea extends Eloquent {

    // protected $table = 'shipping_method_areas';
    
    public function shippingMethod()
    {
        return $this->belongsTo('ShippingMethod');
    }

    public function deliveryArea()
    {
        return $this->belongsTo('DeliveryArea');
    }
} 