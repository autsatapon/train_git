<?php

class City extends Eloquent
{

	public $timestamps = false;

	public function province()
	{
		return $this->belongsTo('Province');
	}
   
   public function districts()
	{
		return $this->hasMany('District');
	}

	public function deliveryArea()
	{
		return $this->belongsTo('DeliveryArea');
	}

}
