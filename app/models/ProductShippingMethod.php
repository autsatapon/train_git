<?php

class ProductShippingMethod extends Eloquent {

	protected $softDelete = false;

	public function product()
	{
		return $this->belongsTo('Product');
	}

	public function shippingMethod()
	{
		return $this->belongsTo('ShippingMethod');
	}

}