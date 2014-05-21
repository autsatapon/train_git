<?php

class Shop extends PCMSModel {

	public static $autoKey = false;
	protected $softDelete = false;
	protected $primaryKey = 'shop_id';
	protected $fillable = array('shop_id', 'name');

    public static $labels = array(
        'name' => 'Shop Name',
    );

    public function vendors()
    {
        return $this->hasMany('VVendor');
    }

    public function policies()
    {
        return $this->morphMany('PolicyRelate', 'policiable');
    }

}