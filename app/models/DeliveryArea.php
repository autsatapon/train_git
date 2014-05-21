<?php

class DeliveryArea extends PCMSModel
{

	public static $autoKey= false;
	protected $softDelete = true;

	public function shippingMethods()
	{
		return $this->softBelongsToMany('ShippingMethod', 'shipping_method_areas', 'delivery_area_id', 'shipping_method_id');
	}

	public function shippableMethods()
	{
		return $this->shippingMethods()->where('delivery_area_id', $this->id)->get();
	}

}