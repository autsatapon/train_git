<?php

class OrderTransaction extends Eloquent {

	public $timestamps = false;

    public function order()
    {
        return $this->belongsTo('Order');
    }

	public function orderShipmentItem()
	{
		return $this->belongsTo('OrderShipmentItem','order_shipment_item_id');
	}

 	public function Shipment()
	{
		return $this->belongsTo('OrderShipment','order_shipment_id');
	}

}