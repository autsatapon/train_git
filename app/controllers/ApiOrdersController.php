<?php

class ApiOrdersController extends ApiBaseController {

    protected $order;

    public function __construct(OrderRepositoryInterface $order)
    {
        parent::__construct();

        $this->order = $order;


//        App::make('PCMSPromotionCart');
    }

    // not sure it's been used
    public function postCreate(PApp $app)
    {
        $order = json_decode(Input::get('order', '{}'), true);

        $validator = $this->order->validateCreate($order);

        if ($validator->result === false)
        {
            return API::createResponse($validator->message, 400);
        }

        $this->order->create($app, $order);

        return API::createResponse(null, 200);
    }

    public function postReconcile(PApp $app)
    {
        $order = json_decode(Input::get('order', '{}'), true);

        $validator = $this->order->validateReconcile($order);

        if ($validator->result === false)
        {
            return API::createResponse($validator->message, 400);
        }

        try
        {
            $this->order->reconcile($order);
        }
        catch (Exception $e)
        {
            return API::createResponse('Order Ref not found or has been updated', 404);
        }

        return API::createResponse(null, 200);
    }

    /**
     * @api {get} /orders/save-foreground Save foreground data from WeTrust
     * @apiName Save foreground
     * @apiGroup Orders
     *
     * @apiParam {String} ref1 Ref1 from WeTrust.
     * @apiParam {String} ref2 Ref2 from WeTrust.
     * @apiParam {String} ref3 Ref3 from WeTrust.
     * @apiParam {String} orderid OrderId from WeTrust.
     * @apiParam {String} [barcode] Barcode from WeTrust.
     *
     * @apiSuccess (200) data App Foreground URL (thank you page of caller app).
     */
    public function postSaveForeground(PApp $app)
    {
        if (!Input::has('ref1') || !Input::has('ref2') || !Input::has('ref3') || !Input::has('orderid'))
        {
            throw new Exception('Parameters ref1, ref2, ref3 and orderid are required', 400);
//           return API::createResponse('Parameters ref1, ref2, ref3 and orderid are required', 400);
        }

        $order = Order::find(Input::get('ref3'));

        if (!$order)
        {
            throw new Exception('Order not found or has been updated', 404);
//           return API::createResponse('Order not found or has been updated', 404);
        }

        $data = array(
            'ref1' => Input::get('ref1'),
            'ref2' => Input::get('ref2'),
            'ref3' => Input::get('ref3'),
            'order_ref' => Input::get('orderid'),
            'payment_order_id' => Input::get('orderid'),
            'barcode' => Input::get('barcode'),
        );

        $this->order->update($order, $data);

        $tmp = new Tmp;
        $tmp->key = 'save-foreground';
        $tmp->value = json_encode($data);
        $tmp->save();

        $fgUrl = $order->app->foreground_url.'?'.http_build_query(array(
                'method' => $order->payment->code,
                'order_id' => $order->order_id,
        ));

//        if (strtolower($order->payment->channel) === Order::OFFLINE)
//        {
//            $fgUrl .= '&success=1';
//        }

        if ($order->sub_total == 0)
        {
            $payment = App::make('PaymentRepositoryInterface');

            $payment->saveReconcile($order, $data);
        }

        return API::createResponse(array('foreground_url' => $fgUrl), 200);
    }

    /**
     * @api {get} /orders Get Order Detail
     * @apiName Get Order Detail
     * @apiGroup Orders
     *
     * @apiParam {String} order_key Order Key
     * @apiParam {String} code Hash code of Order Key
     *
     * @apiSuccess (200) data Order Detail.
     */
    public function getIndex(PApp $app)
    {
        $pkey = Input::get('order_key');
        $code = Input::get('code');

        if (md5(Config::get('email_template.md5_salt').$pkey) !== $code)
        {
            return API::createResponse('Invalid order', 404);
        }

        $order = $this->order->getOrderByPkey($pkey);
        $inventoryIds = $order->shipmentItems->lists('inventory_id');
        $variants = ProductVariant::with('product.mediaContents')->whereIn('inventory_id', $inventoryIds)->get();

        $order->shipments->each(function($shipment) use ($variants)
            {

                $shipment->shipmentItems->each(function($item) use ($variants)
                    {

                        $variant = $variants->filter(function($variant) use ($item)
                                {
                                    return $variant->inventory_id == $item->inventory_id;
                                })->first();

                        $item->images = $variant->product->image;
                    });
            });

        unset($order->shipmentItems);

        return API::createResponse($order, 200);
    }

    /**
     * @api {get} /orders/hash-code Get Order Hash Code
     * @apiName Get Order Hash Code
     * @apiGroup Orders
     *
     * @apiParam {String} order_id Order Id
     *
     * @apiSuccess (200) data Order Hash Code.
     */
    public function getHashCode(PApp $app)
    {
        $orderId = Input::get('order_id');

        $order = Order::select('id', 'pkey')->find($orderId);

        if (! $order)
        {
            return API::createResponse('Invalid order id', 404);
        }

        $code = md5(Config::get('email_template.md5_salt').$order->pkey);

        $response = array(
            'orderKey' => $order->pkey,
            'hashCode' => $code
        );

        return API::createResponse($response, 200);
    }

}

