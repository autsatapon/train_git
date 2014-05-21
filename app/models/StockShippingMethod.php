<?php

class StockShippingMethod extends Eloquent {

	protected $softDelete = false;

	public function stock()
	{
		return $this->belongsTo('Stock');
	}

	public function shippingMethod()
	{
		return $this->belongsTo('ShippingMethod');
	}

}