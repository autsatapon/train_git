<?php

class VendorShippingMethod extends Eloquent {

	protected $softDelete = false;

	public function vvendor()
	{
		return $this->belongsTo('VVendor');
	}

	public function shippingMethod()
	{
		return $this->belongsTo('ShippingMethod');
	}

}