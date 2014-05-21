<?php

class OrderShipment extends Eloquent {

    protected $table = 'order_shipments';

    public static function boot()
    {
        parent::boot();

        static::updating(function($model)
                {
                    $dirty = $model->getDirty();

                    if (isset($dirty['shipment_status']))
                    {
                        if ($dirty['shipment_status'] === 'sent')
                        {
                            $orderRepository = new OrderRepository();

                            $shipping_method = $model->shipping_method;
                            $ship_to_bangkok = $orderRepository->postcodeIsBangkok($model->customer_postcode);

                            if ($ship_to_bangkok === true)
                            {
                                if ($shipping_method === 'EMS')
                                    $shipment_estimated_time = date('Y-m-d H:i:s', strtotime('+2 weekdays 6:00 pm'));
                                elseif ($shipping_method === 'RGP')
                                    $shipment_estimated_time = date('Y-m-d H:i:s', strtotime('+1 weekday 6:00 pm', strtotime('+4 weekdays'))); // split to 4 + 1 weekday because of php's bug
                                elseif ($shipping_method === 'D2D' || $shipping_method === 'COD')
                                    $shipment_estimated_time = date('Y-m-d H:i:s', strtotime('+1 weekday 6:00 pm'));
                            }
                            else
                            {
                                if ($shipping_method === 'EMS')
                                    $shipment_estimated_time = date('Y-m-d H:i:s', strtotime('+1 weekday 6:00 pm', strtotime('+4 weekdays')));
                                elseif ($shipping_method === 'RGP')
                                    $shipment_estimated_time = date('Y-m-d H:i:s', strtotime('+3 weekdays 6:00 pm', strtotime('+4 weekdays')));
                                elseif ($shipping_method === 'D2D' || $shipping_method === 'COD')
                                    $shipment_estimated_time = date('Y-m-d H:i:s', strtotime('+2 weekdays 6:00 pm'));
                            }

                            $model->sla_time_at = $shipment_estimated_time;
                        }
                        else
                        {
                            $model->sla_time_at = null;
                        }

                        // log
                        $originals = $model->getOriginal();
                        $logData = array(
                            'order_id' => $model->order_id,
                            'shipment_id' => $model->id,
                            'shipment_status' => $model->shipment_status,
                            'previous_status' => $originals['shipment_status'],
                            'previous_sla_time_at' => $originals['sla_time_at'],
                        );
                        $log = new OrderTrackingLog($logData);
                        $log->save();
                    }
                });

        static::saved(function($model)
                {
                    $all_order_shipments = $model->order->shipments;
                    $order = $model->order;

                    if ($order->order_status === 'checked' || $order->order_status === 'waiting' || $order->order_status === 'unshippable')
                    {
                        $all_shipments_packed = true;
                        foreach ($all_order_shipments as $shipment)
                        {
                            // is unshippable
                            if ($shipment->shipment_status === 'unpackable')
                            {
                                $order->order_status = 'unshippable';
                                $all_shipments_packed = false;
                                $order->save();
                                break;
                            }
                            else if ($shipment->shipment_status === 'waiting')
                            {
                                $order->order_status = 'waiting';
                                $all_shipments_packed = false;
                                $order->save();
                                break;
                            }
                        }

                        if ($all_shipments_packed == true)
                        {
                            $order->order_status = 'ready';
                            $order->save();
                        }
                    }
                    else if ($order->order_status === 'ready')
                    {
                        $all_shipments_shipping = true;
                        foreach ($all_order_shipments as $shipment)
                        {
                            if ($shipment->shipment_status !== 'shipping')
                            {
                                $all_shipments_shipping = false;
                                break;
                            }
                        }

                        if ($all_shipments_shipping == true)
                        {
                            $order->order_status = 'shipping';
                            $order->save();
                        }
                    }
                    else if ($order->order_status === 'shipping')
                    {
                        $all_shipments_sent = true;
                        foreach ($all_order_shipments as $shipment)
                        {
                            if ($shipment->shipment_status !== 'sent')
                            {
                                $all_shipments_sent = false;
                                break;
                            }
                        }

                        if ($all_shipments_sent == true)
                        {
                            $order->order_status = 'sent';
                            $order->save();
                        }
                    }
                    else if ($order->order_status === 'sent' || $order->order_status === 'partially delivered')
                    {
                        $all_shipments_delivered = true;
                        foreach ($all_order_shipments as $shipment)
                        {
                            if ($shipment->shipment_status !== 'delivered')
                            {
                                $all_shipments_delivered = false;
                                break;
                            }
                        }

                        if ($all_shipments_delivered == true)
                        {
                            $order->order_status = 'delivered';
                        }
                        else
                        {
                            $order->order_status = 'partially delivered';
                        }

                        $order->save();
                    }
                });
    }

    public function order()
    {
        return $this->belongsTo('Order');
    }

    public function shipmentItems()
    {
        return $this->hasMany('OrderShipmentItem', 'shipment_id');
    }

    public function method()
    {
        return $this->belongsTo('ShippingMethod', 'shipping_method');
        // return $this->belongsTo('ShippingMethod', 'shipping_method', 'pkey');
    }

//    public function setShippingMethodAttribute($value)
//    {
//        $this->attributes['shipping_method'] = ShippingMethod::wherePkey($value)->firstOrFail()->getKey();
//    }
//
//    public function getShippingMethodAttribute($value)
//    {
//        return ShippingMethod::findOrFail($value)->pkey;
//    }

    public function setShipmentStatusAttribute($value)
    {
        $this->attributes['shipment_status'] = strtolower($value);
    }

    public function getShipmentStatusAttribute($value)
    {
        return strtolower($value);
    }
	
	public function vendor()
	{
		return $this->belongsTo('VVendor','vendor_id', 'vendor_id');
	}

}

