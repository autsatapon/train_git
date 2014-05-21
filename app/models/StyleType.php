<?php

class StyleType extends PCMSModel {

    protected $softDelete = false;

    public $timestamps = false;

    public static $rules = array(
        'name' => 'required|unique:style_types'
    );

    public function products()
    {
        return $this->belongsToMany('Product');
    }


    public function styleOptions()
    {
        return $this->hasMany('StyleOption');
    }


    public function variantStyleOption()
    {
        return $this->hasMany('VariantStyleOption');
    }
}