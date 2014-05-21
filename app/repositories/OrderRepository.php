<?php

class OrderRepository implements OrderRepositoryInterface {

    public function create($app, $data)
    {
        $order = new Order;

        $order->order_ref            = $data['order_ref']? : '';
        $order->app_id               = $app->id;
        $order->ref2                 = $data['ref2']? : null;
        $order->ref3                 = $data['ref3']? : null;
        $order->customer_ref_id      = $data['customer_ref_id']? : null;
        $order->customer_name        = $data['customer_name']? : null;
        $order->customer_address     = $data['customer_address']? : null;
        $order->customer_district    = @$data['customer_district']? : null;
        $order->customer_district_id = @$data['customer_district_id']? : null;
        $order->customer_city        = @$data['customer_city']? : null;
        $order->customer_city_id     = @$data['customer_city_id']? : null;
        $order->customer_province    = @$data['customer_province']? : null;
        $order->customer_province_id = @$data['customer_province_id']? : null;
        $order->customer_postcode    = $data['customer_postcode']? : null;
        $order->customer_tel         = $data['customer_tel']? : null;
        $order->customer_email       = $data['customer_email']? : null;
        $order->payment_channel      = $data['payment_channel']? : null;
        $order->payment_method       = $data['payment_method'] ? PaymentMethod::wherePkey($data['payment_method'])->first()->getKey() : null;
        // $order->barcode              = $data['barcode']? : null;
        $order->installment          = (is_null($data['installment']) || $data['installment'] == 'null')?null:json_encode($data['installment']);
        $order->transaction_time     = null; // not sent from api
        $order->total_price          = $data['total_price']? : null;
        $order->total_shipping_fee   = $data['total_shipping_fee']? : 0;
        $order->discount             = $data['discount']? : 0;
        $order->discount_text        = $data['discount_text']? : null;
        $order->sub_total            = $data['sub_total']? : null;
        $order->payment_status       = $data['payment_status']? : 'waiting'; // not sent from api
        $order->order_status         = $data['order_status']? : 'draft'; // not sent from api
        $order->customer_status      = null; // not sent from api

        $order->save();

        foreach ($data['shipments'] as $value)
        {
            $shipment = new OrderShipment;

            $shippingMethodId = ShippingMethod::where('pkey', array_get($value, 'shipping_method'))->pluck('id');

            $shipment->order_id           = $order->getKey();
            $shipment->shipment_ref       = array_get($value, 'shipment_ref');
            // $shipment->shipping_method = array_get($value, 'shipping_method');
            $shippingMethodId             = ShippingMethod::where('pkey', array_get($value, 'shipping_method'))->pluck('id');
            $shipment->shipping_method    = $shippingMethodId;
            $shipment->shipping_fee       = array_get($value, 'shipping_fee', 0);
            $shipment->shipment_status    = array_get($value, 'shipment_status', 'preparing'); // not sent from api
            $shipment->total_price        = array_get($value, 'total_price');
            $shipment->stock_type         = array_get($value, 'stock_type');
            $shipment->vendor_id          = array_get($value, 'vendor_id');
            $shipment->shop_id            = array_get($value, 'shop_id');

            $shipment->save();

            foreach ($value['items'] as $value)
            {
                $item = new OrderShipmentItem;

                $item->shipment_id  = $shipment->getKey();
                $item->order_id     = $order->getKey();
                $item->inventory_id = array_get($value, 'inventory_id');
                $item->material_code = array_get($value, 'inventory_id');
                $item->name         = array_get($value, 'name');
                $item->category     = array_get($value, 'collection'); // not sent from api
                $item->brand        = array_get($value, 'brand');
                $item->quantity     = array_get($value, 'quantity');
                $item->price        = array_get($value, 'price');
                $item->margin       = array_get($value, 'margin');
                $item->discount     = array_get($value, 'discount');
                $item->total_price  = array_get($value, 'total_price');
                $item->total_margin = array_get($value, 'total_margin');
                $item->vendor_id    = array_get($value, 'vendor_id');
                $item->shop_id      = array_get($value, 'shop_id');
                $item->options      = array('color' => array_get($value, 'color'), 'size' => array_get($value, 'size')); // not sent from api
                $item->item_status  = null; // not sent from api

                $item->save();
            }
        }

        // if payment channel == offline payment channel generate barcode
        // if (strtolower(($order->payment_channel)) === Order::OFFLINE && $order->barcode != null)
        // {

        //     $file_name      = $order->barcode;
        //     $folder         = date_format($order->created_at, 'Y-m-d');
        //     // $write_path  = Config::get('up::uploader.baseDir');
        //     $upload_barcode = 'uploads'.DIRECTORY_SEPARATOR.'barcode'.DIRECTORY_SEPARATOR.$folder;
        //     $image_format   = '.jpg';

        //     // create folder by Y-m-d
        //     if (!is_dir($upload_barcode))
        //     {
        //         @mkdir($upload_barcode);
        //         @chmod($upload_barcode, 0777);
        //     }
        //     // path file barcode
        //     $upload_path = $upload_barcode.DIRECTORY_SEPARATOR.$file_name.$image_format;
        //     // generate barcode
        //     $barcode = new Barcode($file_name);
        //     $barcode->draw($upload_path);
        // }

        return $order;
    }

    public function update(Order $order, $data)
    {
        foreach ($data as $key => $value)
        {
            $order->{$key} = $value;
        }

        return $order->save();
    }

    public function reconcile($data)
    {
        $order = Order::whereOrderRef($data['order_ref'])->whereOrderStatus('new')->firstOrFail();

        $order->payment_method = $data['payment_method'];
        $order->payment_channel = $data['payment_channel'];
        $order->transaction_time = $data['transaction_time'];
        $order->order_status = 'paid';

        $order->save();
    }

    public function validateCreate($order)
    {
        $validate = new stdClass;

        $orderRules = array(
            'order_ref' => 'required|unique:orders,order_ref|regex:/\.*/',
            'ref2' => 'regex:/.*/',
            'ref3' => 'regex:/.*/',
            'customer_name' => 'required',
            'customer_address' => 'required',
            'customer_province' => 'required',
            'customer_postcode' => 'required|numeric',
            'customer_tel' => 'regex:/.*/',
            'customer_email' => 'email',
            'total_price' => 'required|numeric',
            'total_shipping_fee' => 'required|numeric',
            'discount' => 'required|numeric',
            'discount_text' => 'regex:/.*/',
            'sub_total' => 'required|numeric',
            'payment_channel' => 'required',
            'payment_method' => 'required',
            'shipments' => 'required'
        );

        $shipmentRules = array(
            'shipment_ref' => 'required',
            'shipping_method' => 'required',
            'total_price' => 'required|numeric',
            'shipping_fee' => 'required|numeric',
            'stock_type' => 'in:1,2,3,4,5,6',
            'vendor_id' => 'numeric',
            'shop_id' => 'numeric',
            'items' => 'required'
        );

        $itemRules = array(
            'name' => 'required',
            'color' => 'required',
            'size' => 'required',
            'inventory_id' => 'required|numeric',
            'collection' => 'required',
            'brand' => 'required',
            'quantity' => 'required|numeric',
            'price' => 'numeric',
            'margin' => 'required|numeric',
            'discount' => 'required|numeric',
            'total_price' => 'required|numeric',
            'total_margin' => 'required|numeric',
            'vendor_id' => 'required|numeric',
            'shop_id' => 'required|numeric'
        );

        // order validate
        $validator = Validator::make($order, $orderRules);

        if ($validator->fails())
        {
            $messages = $validator->messages()->all();

            $validate->result = false;
            $validate->message = reset($messages);

            return $validate;
        }

        // shipments validate
        foreach ($order['shipments'] as $shipment)
        {
            $validator = Validator::make($shipment, $shipmentRules);

            // if stock_type sent
            if (array_get($shipment, 'stock_type', null) !== null)
            {
                // vendor_id required if stock_type is 4 or 6
                $validator->sometimes('vendor_id', 'required|numeric', function() use ($shipment)
                    {

                        return $shipment['stock_type'] == 4 || $shipment['stock_type'] == 6;
                    });

                // also with shop_id
                $validator->sometimes('shop_id', 'required|numeric', function() use ($shipment)
                    {

                        return $shipment['stock_type'] == 4 || $shipment['stock_type'] == 6;
                    });
            }

            if ($validator->fails())
            {
                $messages = $validator->messages()->all();

                $validate->result = false;
                $validate->message = reset($messages);

                return $validate;
            }

            // items validate
            foreach ($shipment['items'] as $item)
            {
                $validator = Validator::make($item, $itemRules);

                if ($validator->fails())
                {
                    $messages = $validator->messages()->all();

                    $validate->result = false;
                    $validate->message = reset($messages);

                    return $validate;
                }
            }
        }

        $validate->result = true;

        return $validate;
    }

    public function validateReconcile($order)
    {
        $validate = new stdClass;

        $rules = array(
            'order_ref' => 'required',
            'payment_method' => 'required',
            'payment_channel' => 'required',
            'transaction_time' => 'required|date:Y-m-d H:i:s'
        );

        $validator = Validator::make($order, $rules);

        if ($validator->fails())
        {
            $messages = $validator->messages()->all();

            $validate->result = false;
            $validate->message = reset($messages);

            return $validate;
        }

        $validate->result = true;

        return $validate;
    }

    public function getTask(User $user)
    { // whereBetween
        $with = array('shipments', 'shipmentItems', 'orderNotes');
        $orders = array();

        if ($user->hasAccess('track-Order.act-as-fulfillment-to'))
        {
            $status = array('paid', 'delivered', 'refund');
            $orders = Order::with($with)->where(function($query) use ($status)
                        {
                            foreach ($status as $value)
                            {
                                $query->orWhere('order_status', $value);
                            }
                        })
                    ->orWhere(function($query)
                        {
                            $query->whereOrderStatus('new')->wherePaymentChannel('COD');
                        })
                    ->orderBy('updated_at', 'DESC')->get();
        }
        else if ($user->hasAccess('track-Order.act-as-sourcing-to'))
        {
            $status = array('checked', 'ready', 'unshippable', 'waiting', 'send', 'shipping', 'refund');
            $orders = Order::with($with)->where(function($query) use ($status)
                        {
                            foreach ($status as $value)
                            {
                                $query->orWhere('order_status', $value);
                            }
                        })
                    ->orderBy('updated_at', 'DESC')->get();
        }
        else if ($user->hasAccess('track-Order.act-as-logistic-to'))
        {
            $status = array('checked', 'ready', 'unshippable', 'waiting', 'send', 'shipping');
            $orders = Order::with($with)->where(function($query) use ($status)
                        {
                            foreach ($status as $value)
                            {
                                $query->orWhere('order_status', $value);
                            }
                        })
                    ->orderBy('updated_at', 'DESC')->get();
        }
        else if ($user->hasAccess('track-Order.act-as-callcenter-to'))
        {
            $status = array('checked', 'unshippable', 'refund');
            $orders = Order::with($with)->where(function($query) use ($status)
                        {
                            foreach ($status as $value)
                            {
                                $query->orWhere('order_status', $value);
                            }
                        })
                    ->orderBy('updated_at', 'DESC')->get();
        }

        return $orders;
    }

    public function getSearch()
    {
        $orderStatus   = Input::get('order_status');
        $paymentStatus = Input::get('payment_status');
        $dateStart     = Input::get('date_start') . ' 00:00:00';
        $dateEnd       = Input::get('date_end') . ' 23:59:00';

        $with = array('shipments', 'shipmentItems', 'orderNotes');
        $orders = Order::with($with)
                ->where(function($query) use ($orderStatus)
                    {
                        if(!empty($orderStatus))
                        {
                            $query->where('order_status', Input::get('order_status'));
                        }
                    })
                ->where(function($query) use ($paymentStatus)
                    {
                        if(!empty($paymentStatus))
                        {
                            $query->where('payment_status', Input::get('payment_status'));
                        }
                    })
                ->where(function($query) use ($dateStart,$dateEnd)
                    {
                        if( !empty($dateStart) && !empty($dateEnd) )
                        {
                            $query->whereBetween('created_at', array($dateStart,$dateEnd));
                        }

                    })
                ->orderBy('updated_at', 'DESC')->get();

        return $orders;
    }

    public function getAll()
    {
        $orders = Order::with(array('shipmentItems', 'orderNotes'))->where('order_status', '!=', 'temp')->orderBy('updated_at', 'DESC')->get();

        return $orders;
    }

    public function getOrderByCustomerRefId($appId, $customer_ref_id, $limit = 10, $page = 1)
    {
        $orders = Order::with(array('shipments.shipmentItems', 'shipments.method', 'shipments.vendor', 'orderNotes'))->where('customer_ref_id', '=', $customer_ref_id)->orderBy('updated_at', 'DESC')->skip(($page - 1) * $limit)->take($limit)->get();

        $orders->each(function($order)
        {
            $inventoryIds = array();
            $order->shipments->each(function($shipment) use(&$inventoryIds)
            {
                $shipment->shipmentItems->each(function($item) use(&$inventoryIds)
                {
                    $inventoryIds[] = $item->inventory_id;
                });
            });
            
            $variants = ProductVariant::with('product.mediaContents', 'translates')->whereIn('inventory_id', $inventoryIds)->get();

            $order->shipments->each(function($shipment) use ($variants)
            {
                $shipment->items_count = 0;
                
                $shipment->shipmentItems->each(function($item) use (&$shipment, $variants)
                {
                    $shipment->items_count += $item->quantity;
                    
                    $variant = $variants->filter(function($variant) use ($item)
                    {
                        return $variant->inventory_id == $item->inventory_id;
                    })->first();

                    $item->images = $variant->product->image;
                    
                    // translate
                    $translate = $variant->translates->filter(function($translate)
                    {
                        return $translate->locale == getRequestLocale();
                    })->first();

                    if ( ! is_null($translate))
                    {
                        $item->name = $translate->title;
                    }
                });
            });
        });

        $count = Order::where('app_id', $appId)->where('customer_ref_id', '=', $customer_ref_id)->count();

        return array('orders' => $orders->toArray(), 'total' => $count);
    }

    public function getByStatus($status)
    {
        $orders = Order::with(array('shipmentItems', 'orderNotes'))->where('order_status', '=', $status)->orderBy('updated_at', 'DESC')->get();

        return $orders;
    }

    public function getByDatedata($dateData, $status)
    {
        $order_tracking_logs = OrderTrackingLog::where('order_status', $status)->where('created_at', '>', $dateData.' 00:00:00')->where('created_at', '<', $dateData.' 23:59:59')->orderBy('created_at', 'DESC')->groupBy('order_id')->lists('id');
        if (count($order_tracking_logs) > 0)
        {
            $orders = Order::whereIn('id', $order_tracking_logs)->get();
        }
        else
        {
            $orders = array();
        }

        return $orders;
    }

    public function getPaymentStatus($status_type, $status_id)
    {
        $payment_status = PaymentStatus::where('status_type', '=', $status_type)->where('status_id', '=', $status_id)->get();

        return $payment_status;
    }

    public function getPaymentStatusById($id)
    {
        $payment_status = PaymentStatus::where('id', '=', $id)->get();

        return $payment_status;
    }

    public function getClosed()
    {
        $status = array('completed', 'done');

        $orders = Order::with(array('shipmentItems', 'orderNotes'))->where(function($query) use ($status)
                    {
                        foreach ($status as $value)
                        {
                            $query->orWhere('order_status', $value);
                        }
                    })
                ->orderBy('updated_at', 'DESC')->get();

        return $orders;
    }

    public function postcodeIsBangkok($postCode)
    {
        if (floor($postCode / 1000) === 10)
        {
            switch ($postCode)
            {
                case 10270:
                case 10560:
                case 10540:
                case 10130:
                case 10290:
                    return false;
                    break;
            }

            return true;
        }

        return false;
    }

    public function getOrderBy($attr, $value)
    {
        return Order::with(array('shipments.shipmentItems', 'orderNotes'))->where($attr, $value)->firstOrFail();
    }

    public function getOrderById($id)
    {
        return $this->getOrderBy('id', $id);
    }

    public function getOrderByPkey($pkey)
    {
        return $this->getOrderBy('pkey', $pkey);
    }

}

