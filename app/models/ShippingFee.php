<?php

class ShippingFee extends Harvey {

    public static $rules = array(
        'shipping_method_area_id' => 'required|numeric',
        'delivery_area_id' => 'required|numeric',
        'shipping_method_id' => 'required|numeric',
        'weight_min' => 'required|numeric',
        'weight_max' => 'required|numeric',
        'shipping_box_id' => 'required|numeric',
        'product_weight_min' => 'required|numeric',
        'product_weight_max' => 'required|numeric',
        'shipping_fee' => 'required|numeric',
    );

    public static $labels = array(
        'shipping_method_area_id' => 'Shipping Method Area ID',
        'delivery_area_id' => 'Delivery Area ID',
        'shipping_method_id' => 'Shipping Method ID',
        'weight_min' => 'Min Weight',
        'weight_max' => 'Max Weight',
        'shipping_box_id' => 'Shipping Box ID',
        'product_weight_min' => 'Product Min Weight',
        'product_weight_max' => 'Product Max Weight',
        'shipping_fee' => 'Shipping Fee',
    );

    public function shippingBox()
    {
        return $this->belongsTo('ShippingBox');
    }
} 