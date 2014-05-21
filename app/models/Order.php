<?php

class Order extends PCMSModel {

    protected $table = 'orders';
//	protected $appends = array('items');

    protected $dates = array('transaction_time', 'sla_time_at');

    const OFFLINE = 'offline';
    const ONLINE = 'online';
    const COD = 'cod';
    const FREE_SHIPPING = 'free_shipping';

    const INSTALLMENT_WETRUST_CODE = 'CCinstM';
    
    const PAYMENT_CODE_INSTALLMENT = 'CCinstM';
    const PAYMENT_CODE_ATM = 'ATM';

    const PAYMENT_WAITING = 'waiting';
    const PAYMENT_SUCCESS = 'success';
    const PAYMENT_FAILED = 'failed';
    const PAYMENT_RECONCILE = 'reconcile';
    const PAYMENT_EXPIRE = 'expired';

    const GIFT_ADDED = 'gift_added';
    const GIFT_EMPTY = 'no_gift';
    const GIFT_CONFIRMED = 'gift_confirmed';
    const GIFT_REJECTED = 'gift_rejected';

    const CUSTOMER_CONFIRMED = 'confirmed';
    const CUSTOMER_REJECTED = 'reject_order';
    const CUSTOMER_NO_ANSWER = 'no_answer';
    const CUSTOMER_CALL_LATER = 'call_later';

    const SHIPPING_PREPARING = 'preparing';
    const SHIPPING_PACKING = 'packing';
    const SHIPPING_READY = 'ready';
    const SHIPPING_UNSHIPPABLE = 'unshippable';
    const SHIPPING_SENDING = 'sending';
    const SHIPPING_SENT = 'sent';
    const SHIPPING_DELIVERED = 'delivered';
    const SHIPPING_RETURNED = 'returned';

    const STATUS_WAITING = 'draft';
    const STATUS_CANCEL = 'cancel';
    const STATUS_EXPIRE = 'expired';
    const STATUS_NEW = 'new';
    const STATUS_PREPARING = 'preparing';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETE = 'completed';
    const STATUS_CLOSE = 'closed';

    public static function boot()
    {
        parent::boot();

        static::creating(function($model)
        {
//            $model->expired_at = date('Y-m-d H:i:s', strtotime('next hour'));
            
//            try
//            {
                if ($model->payment_channel == 'online')
                {
                    $model->expired_at = date('Y-m-d H:i:s', strtotime('+30 minutes'));
                }
                else if ($model->payment_channel == 'offline')
                {
                    $holidayRepository = App::make('HolidayRepositoryInterface');

                    $holidays = $holidayRepository->getFromNow();

                    $targetDay = date('Y-m-d', strtotime('+ 4 days'));

                    // if it is sat or sun or holiday +1 day
                    while (date('N', strtotime($targetDay)) >= 6 || in_array($targetDay, $holidays))
                    {
                        $targetDay = date('Y-m-d', strtotime('+ 1 day', strtotime($targetDay)));
                    }

                    $targetDay .= ' 23:59:59';

                    $model->expired_at = $targetDay;
                }
                else if (strtoupper($model->payment->code) == strtoupper(Order::COD))
                {
                    $model->expired_at = null;
                }
//            }
//            catch (Exception $e)
//            {
//                throw new Exception($e->getMessage().'<br />Cannot complete creating order, please check Order::boot()');
//            }
        });

        static::updating(function($model)
                {
                    $dirty = $model->getDirty();
                    if (isset($dirty['order_status']))
                    {
                        $new_status = $dirty['order_status'];

                        // set sla_time_at based on status changed
                        if ($new_status === 'paid' || ($new_status === 'new' && $model->payment_channel === 'COD'))
                        {
                            $today_ten_oclock = strtotime('today 10:15 am');
                            if (time() > $today_ten_oclock)
                            {
                                $model->sla_time_at = date('Y-m-d H:i:s', strtotime('next weekday 10:30 am'));
                            }
                            else
                            {
                                $model->sla_time_at = date('Y-m-d H:i:s', strtotime('today 10:30 am'));
                            }
                        }
                        elseif ($new_status === 'checked')
                        {
                            $model->sla_time_at = date('Y-m-d H:i:s', strtotime('today 11:30 am'));
                            $model->customer_sla_time_at = date('Y-m-d H:i:s', strtotime('today 11:30 am'));
                        }
                        elseif ($new_status === 'unshippable')
                        {
                            $model->sla_time_at = null;
                            $model->customer_sla_time_at = date('Y-m-d H:i:s', strtotime('+30 minutes'));
                        }
                        elseif ($new_status === 'waiting')
                        {
                            $model->sla_time_at = date('Y-m-d H:i:s', strtotime('next weekday 11:30 am'));
                        }
                        elseif ($new_status === 'ready')
                        {
                            $model->sla_time_at = date('Y-m-d H:i:s', strtotime('today 1:30 pm'));
                        }
                        elseif ($new_status === 'shipping')
                        {
                            $model->sla_time_at = date('Y-m-d H:i:s', strtotime('next weekday 12:00 pm'));
                        }
                        elseif ($new_status === 'sent')
                        {
                            $shipments = $model->shipments;
                            $fastest_shipment_estimated_time = 0;

                            foreach ($shipments as $i => $shipment)
                            {
                                if ($i == 0)
                                    $fastest_shipment_estimated_time = strtotime($shipment->sla_time_at);
                                else
                                    $fastest_shipment_estimated_time = min(strtotime($shipment->sla_time_at), $fastest_shipment_estimated_time);
                            }

                            $model->sla_time_at = date('Y-m-d H:i:s', $fastest_shipment_estimated_time);
                        }
                        elseif ($new_status === 'delivered' || $new_status === 'refund')
                        {
                            $model->sla_time_at = date('Y-m-d H:i:s', strtotime('next weekday 6:00 pm'));
                        }
                        elseif ($new_status === 'completed' || $new_status === 'done')
                        {
                            $model->sla_time_at = null;
                        }

                        // log
                        $originals = $model->getOriginal();
                        $logData = array(
                            'order_id' => $model->id,
                            'order_status' => $model->order_status,
                            'previous_status' => $originals['order_status'],
                            'previous_sla_time_at' => $originals['sla_time_at'],
                        );
                        $log = new OrderTrackingLog($logData);
                        $log->save();
                    }

                    if (isset($dirty['customer_status']))
                    {
                        $new_customer_status = $dirty['customer_status'];

                        if ($new_customer_status === 'address confirmed')
                        {
                            $model->customer_sla_time_at = null;
                        }
                        elseif ($new_customer_status === 'unreachable' || $new_customer_status === 'call later')
                        {
                            $model->customer_sla_time_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
                        }

                        // log
                        $originals = $model->getOriginal();
                        $logData = array(
                            'order_id' => $model->id,
                            'customer_status' => $model->customer_status,
                            'previous_status' => $originals['customer_status'],
                            'previous_sla_time_at' => $originals['customer_sla_time_at'],
                        );
                        $log = new OrderTrackingLog($logData);
                        $log->save();
                    }

                    if (isset($dirty['customer_name']) || isset($dirty['customer_address']) || isset($dirty['customer_province']) || isset($dirty['customer_postcode']) || isset($dirty['customer_tel']))
                    {
                        $model->customer_info_modified_at = date('Y-m-d H:i:s');
                    }
                });
    }

    public function giftItems()
    {
        return $this->hasMany('OrderShipmentItem')->where('is_gift_item', '1');
    }

    public function orderNotes()
    {
        return $this->hasMany('OrderNote');
    }

    public function shipments()
    {
        return $this->hasMany('OrderShipment');
    }

    public function shipmentItems()
    {
        return $this->hasMany('OrderShipmentItem');
    }

    public function payment()
    {
        return $this->belongsTo('PaymentMethod', 'payment_method');
    }

    public function app()
    {
        return $this->belongsTo('PApp');
    }

	public function city()
    {
        return $this->belongsTo('City','customer_city_id');
    }

    public function orderLogs()
    {
        return $this->hasMany('OrderTrackingLog')->whereNull('shipment_id')->whereNull('item_id');
    }

    public function orderTransactions()
    {
        return $this->hasMany('OrderTransaction');
    }

    /**
     * Polymorphic Relations for ValidPromotion
     * @return object
     */
    public function validPromotions()
    {
        return $this->morphMany('ValidPromotion', 'promotionable');
    }

//    public function setPaymentChannelAttribute($value)
//    {
//        $this->attributes['payment_channel'] = strtoupper($value);
//    }
//
//    public function setPaymentMethodAttribute($value)
//    {
//        $this->attributes['payment_method'] = strtoupper($value);
//    }

    public function setOrderStatusAttribute($value)
    {
        $this->attributes['order_status'] = strtolower($value);
    }

    public function getOrderIdAttribute()
    {
        return $this->id;
    }

//    public function getPaymentChannelAttribute($value)
//    {
//        return strtoupper($value);
//    }
//
//    public function getPaymentMethodAttribute($value)
//    {
//        return strtoupper($value);
//    }

    /*
    public function getOrderStatusAttribute($value)
    {
        return strtolower($value);
    }
    */

    /*
    public function getPaymentStatusAttribute()
    {
        if ($this->order_status == 'paid')
            return 'paid';
        else if (strtoupper($this->payment_channel) === 'ONLINE')
            return 'checking';
        return 'waiting';
    }
    */

    public function getPaymentExpiredAtAttribute()
    {
        if (strtoupper($this->payment_channel) === 'ONLINE')
            return $this->asDateTime(strtotime($this->created_at . ' +30 minutes'));
        return $this->asDateTime(strtotime($this->created_at . ' +3 days'));
    }

	public function orderAddressLog()
    {
        return $this->hasMany('OrderAddressLog');
    }

	/*
	public function getOrderStatusAttribute($val)
	{
		if (is_numeric($val))
		{
			return strtolower(DB::table('payment_status')->where('id', '=', $val)->pluck('english'));
		}

		return strtolower($val);
	}
	*/

	public function getOrderStatusThAttribute()
	{
		/*
		if (is_numeric($this->order_status))
		{
			return DB::table('payment_status')->where('id', '=', $this->order_status)->pluck('thai');
		}
		*/
		return StatusDict::where('status_type','order_status')->where('english','=', $this->order_status)->pluck('thai');
	}

	/*
	public function getPaymentStatusAttribute($val)
	{
        if ($this->order_status == 'paid' || $val == 3)
            return 'paid';
		else if ($this->order_status == 'refund' || $val == 34)
            return 'refund';
        else if (strtoupper($this->payment_channel) === 'ONLINE' || $val == 2)
            return 'checking';
        return 'waiting';
	}
	*/

	public function getPaymentStatusThAttribute()
	{
        return StatusDict::where('status_type','payment_status')->where('english','=', $this->payment_status)->pluck('thai');
	}

    public function scopeNotExpire($query, $timeStamp = null)
    {
        return $query->where('expired_at', '>', $timeStamp ?: date('Y-m-d H:i:s'));
    }
}

