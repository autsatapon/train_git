<?php

class CustomerAddress extends Eloquent {

    protected $with = array('province', 'city', 'district');
    protected $hidden = array('province', 'city', 'district');
    protected $appends = array('text');

    public function province()
    {
        return $this->belongsTo('Province');
    }

    public function city()
    {
        return $this->belongsTo('City');
    }

    public function district()
    {
        return $this->belongsTo('District');
    }

    public function getTextAttribute()
    {
        if ( ! $this->attributes['district_id'] || ! $this->attributes['city_id'] || ! $this->attributes['province_id'])
        {
            return null;
        }
        
        return $this->name.' ['.$this->attributes['address'] . ', ' . $this->district->name . ', ' . $this->city->name . ', ' . $this->province->name . ' ' . $this->attributes['postcode'].']';
    }

}
