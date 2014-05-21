<?php

class DiscountCampaign extends PCMSModel {

    public static $rules = array(
        'app_id' => 'required',
        'type' => 'required',
        'code' => 'required',
        'name' => 'required',
        'description' => 'required',
        'discount' => 'required|integer',
        'discount_type' => 'required',
        'started_at' => 'required',
        'ended_at' => 'required',
        'status' => 'required'
    );

    protected $dates = array('started_at', 'ended_at', 'created_at', 'updated_at');

    public function pApp()
    {
        return $this->belongsTo('PApp', 'app_id', 'id');
    }

    public function specialDiscounts()
    {
        return $this->hasMany('SpecialDiscount');
    }

    public function getDiscountAttribute($discount)
    {
        return number_format($discount, 0);
    }

}