<?php

class ShippingMethod extends PCMSModel {

    public static $labels = array(
        'name' => 'Method Name',
        'description' => 'Description',
        'tracking_url' => 'Tracking URL',
        'max_weight' => 'Max Weight',
        'allow_nonstock' => 'Can ship non-stock items?',
    );
    //set rule.
    public static $rules = array(
        'name' => 'required',
        'tracking_url' => 'url',
        'allow_nonstock' => 'numeric',
        'max_weight' => 'integer',
        'dimension_max' => 'numeric',
        'dimension_mid' => 'numeric',
        'dimension_min' => 'numeric',
    );

    public function deliveryAreas()
    {
        return $this->softBelongsToMany('DeliveryArea', 'shipping_method_areas', 'shipping_method_id', 'delivery_area_id')
                ->withPivot('id', 'delivery_area_id');
    }

    public static function getCOD()
    {
        return Cache::remember('shippingMethod-COD', 120, function()
                {
                    return static::where('slug', strtoupper(Order::COD))->first() ? : null;
                });
    }

}

