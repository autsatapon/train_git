<?php

class Province extends Eloquent {

    public $timestamps = false;

    public function country()
    {
        return $this->belongsTo('Country');
    }

    public function cities()
    {
        return $this->hasMany('City');
    }

    public function deliveryArea()
    {
        return $this->belongsTo('DeliveryArea');
    }

}
