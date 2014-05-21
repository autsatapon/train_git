<?php

class OrderShipmentItem extends Eloquent {

    protected $table = 'order_shipment_items';

    protected $hidden = array('margin', 'total_margin');

    public static function boot()
    {
        parent::boot();

        static::updating(function($model)
        {
            $dirty = $model->getDirty();

            if(isset($dirty['item_status']))
            {
                // log
                /*
                $originals = $model->getOriginal();
                $shipmentOriginals = $model->shipment->getOriginal();
                $logData = array(
                    'order_id' => $model->order_id,
                    'shipment_id' => $model->shipment_id,
                    'item_id' => $model->item_id,
                    'item_status' => $model->item_status,
                    'previous_status' => $originals['item_status'],
                    'previous_sla_time_at' => $shipmentOriginals['sla_time_at'],
                );
                $log = new OrderTrackingLog($logData);
                $log->save();
                */
            }
        });
    }

    public function order()
    {
        return $this->belongsTo('Order');
    }

//    public function variant()
//    {
//        return $this->hasOne('ProductVariant', 'inventory_id');
//    }

    public function shipment()
    {
        return $this->belongsTo('OrderShipment');
    }

	public function getOptionsAttribute($value)
	{
		// $this->attributes['options'] = json_decode($value, true);
        return json_decode($value, true);
	}

	public function setOptionsAttribute($value)
	{
		$this->attributes['options'] = json_encode($value);
	}

    public function paymentStatus()
    {
        return $this->belongsTo('PaymentStatus', 'item_status');
    }

    public function orderTransactions()
    {
        return $this->hasOne('OrderTransaction');
    }
	
	public function vendor()
	{
		return $this->belongsTo('VVendor','vendor_id');
	}
	
	public function shop()
	{
		return $this->belongsTo('Shop', 'shop_id', 'shop_id');
	}
   
   public function variant()
	{
		return $this->belongsTo('ProductVariant');
//		return $this->belongsTo('ProductVariant', 'inventory_id', 'inventory_id');
	}
}