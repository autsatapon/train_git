<?php

class ApiCheckoutController extends ApiBaseController {

    protected $cart;
    protected $checkout;
    protected $wetrust;

    public function __construct(CartRepositoryInterface $cart, CheckoutRepositoryInterface $checkout)
    {
        parent::__construct();

        $this->cart = $cart;
        $this->checkout = $checkout;

        $this->wetrust = App::make('wetrust');


        App::make('PCMSPromotionCart');
    }

    /**
     * @api {get} /checkout Checkout Information
     * @apiName Get Checkout Data
     * @apiGroup Checkout
     *
     * @apiParam {String} customer_ref_id Customer ID (User = sso id, Non-user = random id).
     * @apiParam {String} customer_type (user / non-user).
     *
     * @apiSuccess {Array} data Checkout Information.
     */
    public function getIndex(PApp $app)
    {
        $data = array(
            'app_id' => $app->getKey(),
            'customer_type' => Input::get('customer_type'),
            'customer_ref_id' => Input::get('customer_ref_id')
        );

        if (Input::has('recheck_trueyou'))
        {
            $this->cart->applyTrueyou($data);
        }

        $cart = $this->cart->getCart($data);

        if ($cart === null)
        {
            return API::createResponse('Cart not found', 404);
        }

        $order = $this->checkout->buildOrder($app, $cart);

        return API::createResponse($order, 200);
    }

    /**
     * @api {post} /checkout/update-items Update Items
     * @apiName Update Checkout Items
     * @apiGroup Checkout
     *
     * @apiParam {String} customer_ref_id Customer ID (User = sso id, Non-user = random id).
     * @apiParam {String} customer_type (user / non-user).
     * @apiParam {Array} items Array of items and their quantity to be updated
     *     [
     *         {
     *             "inventory_id" => :inventory_id,
     *             "qty" => :qty
     *         },
     *         {
     *             "inventory_id" => :inventory_id,
     *             "qty" => :qty
     *         }
     *     ]
     *
     *
     * @apiSuccess {Array} data Updated Checkout Information.
     */
    public function postUpdateItems(PApp $app)
    {
        // prepare data
        $items = array();
        foreach (Input::get('items') as $id => $qty)
        {
            $items[] = array(
                'inventory_id' => $id,
                'qty' => $qty
            );
        }

        $data = array(
            'app_id' => $app->getKey(),
            'customer_type' => Input::get('customer_type'),
            'customer_ref_id' => Input::get('customer_ref_id'),
            'items' => json_encode($items)
        );

        // update
        $response = API::post('api/'.$app->pkey.'/cart/update-item', $data);

        // return error if can not update
        if ($response['code'] != 200)
        {
            return $response;
        }

        // internal call to GET: /checkout
        return API::get('api/'.$app->pkey.'/checkout', $data);
    }

    /**
     * @api {post} /checkout/remove-item Remove Checkout Item
     * @apiName Remove Checkout Item
     * @apiGroup Checkout
     *
     * @apiParam {String} customer_ref_id Customer ID (User = sso id, Non-user = random id).
     * @apiParam {String} customer_type (user / non-user).
     * @apiParam {Number} inventory_id Inventory ID of item to be removed.
     *
     * @apiSuccess {Array} data Checkout Information.
     */
    public function postRemoveItem(PApp $app)
    {
        // prepare data
        $data = array(
            'app_id' => $app->getKey(),
            'customer_type' => Input::get('customer_type'),
            'customer_ref_id' => Input::get('customer_ref_id'),
            'inventory_id' => Input::get('inventory_id')
        );

        // delete
        API::post('api/'.$app->pkey.'/cart/remove-item', $data);

        // internal call to GET: /checkout
        return API::get('api/'.$app->pkey.'/checkout', $data);
    }

    /**
     * @api {post} /checkout/set-customer-info Set Customer Info
     * @apiName Set Customer Info
     * @apiGroup Checkout
     *
     * @apiParam {String} customer_ref_id Customer ID (User = sso id, Non-user = random id).
     * @apiParam {String} customer_type (user / non-user).
     * @apiParam {String} [customer_name] Customer Name.
     * @apiParam {String} [customer_address] Customer Address.
     * @apiParam {Number} [customer_district_id] District ID (see Address API).
     * @apiParam {Number} [customer_city_id] City ID (see Address API).
     * @apiParam {Number} [customer_province_id] Province ID (see Address API).
     * @apiParam {String} [customer_postcode] Postcode.
     * @apiParam {String} [customer_tel] Telephone or Mobile (allow only Thai land line and mobile number).
     * @apiParam {String} [customer_email] Email.
     * @apiParam {Number} [customer_address_id] Address Record's ID that will be updated.
     *
     * @apiSuccess {Array} data Checkout Information.
     */
    public function postSetCustomerInfo(PApp $app)
    {
        $data = array(
            'app_id' => $app->getKey(),
            'customer_type' => Input::get('customer_type'),
            'customer_ref_id' => Input::get('customer_ref_id')
        );

        $cart = $this->cart->getCart($data);

        // only these input are allowed to update
        $inputs = Input::only('customer_name', 'customer_address', 'customer_district_id', 'customer_city_id', 'customer_province_id', 'customer_postcode', 'customer_tel', 'customer_email', 'customer_address_id');

        // remove null value
        $inputs = array_filter($inputs);

        // update
        $this->checkout->update($cart, $inputs);

        Event::fire('Cart.setUnConfirm', array($cart));

        // build
        $order = $this->checkout->buildOrder($app, $cart);

        return API::createResponse($order, 200);
    }

    /**
     * @api {post} /checkout/set-payment-info Set Payment Info
     * @apiName Set Payment Info
     * @apiGroup Checkout
     *
     * @apiParam {String} customer_ref_id Customer ID (User = sso id, Non-user = random id).
     * @apiParam {String} customer_type (user / non-user).
     * @apiParam {String} payment_method pkey of payment.
     * @apiParam {Number} [installment=false] Month period.
     *
     * @apiSuccess {Array} data Checkout Information.
     */
    public function postSetPaymentInfo(PApp $app)
    {
        $data = array(
            'app_id' => $app->getKey(),
            'customer_type' => Input::get('customer_type'),
            'customer_ref_id' => Input::get('customer_ref_id')
        );

        $cart = $this->cart->getCart($data);

        // only these input are allowed to update
        $inputs = Input::only('payment_channel', 'payment_method', 'installment');

        // get Payment method id
        $selectedPaymentMethod = PaymentMethod::wherePkey($inputs['payment_method'])->first();

        // if installment
        if ($selectedPaymentMethod != false && strtolower($selectedPaymentMethod->code) === strtolower(Order::INSTALLMENT_WETRUST_CODE))
        {
            // $installment = (array) $cart->cartDetails()->first()->variant->installment->periods;
            $variant = $cart->cartDetails()->first()->variant;
            if ($variant->allow_installment)
            {
                $installment = (array) $variant->installment->periods;
            }
            else if ($variant->product->allow_installment)
            {
                $installment = (array) $variant->product->installment->periods;
            }
            else
            {
                $installment = array();
            }

            // if month set is not in allow defined, force to use first
            if (!in_array($inputs['installment'], $installment))
            {
                $inputs['installment'] = json_encode(array('period' => $installment[0]));
            }
            else
            {
                $inputs['installment'] = json_encode(array('period' => $inputs['installment']));
            }
        }
        else
        {
            $inputs['installment'] = null;
        }

        // remove null value
        $inputs = array_filter($inputs);

        // if select payment => COD
        if (strtolower($selectedPaymentMethod->code) === Order::COD)
        {
            $order = $this->checkout->buildOrder($app, $cart);
            // AND there is available COD payment method
            if (count($order['shipments']) === 1 && in_array($selectedPaymentMethod->pkey, $order['available_payment_methods']))
            {
                $allow_COD = true;

                // force shipping_method of shipment to COD
                $codShipping = ShippingMethod::getCOD();
                if ($codShipping != false)
                {
                    $shipmentsData = array();
                    foreach ($order['shipments'] as $key => $shipment)
                        ;
                    {
                        if (Stock::isNonStock($shipment['stock_type']))
                        {
                            $allow_COD = false;
                            break;
                        }
                        $shipmentsData[$key] = $codShipping->getKey();
                    }

                    if ($allow_COD)
                    {
                        $inputs['shipments'] = json_encode($shipmentsData);
                    }
                }
            }
        }

        $inputs['payment_channel'] = $selectedPaymentMethod->channel;
        $inputs['payment_method'] = $selectedPaymentMethod->getKey();

        // update
        $this->checkout->update($cart, $inputs);

        // build
        $order = $this->checkout->buildOrder($app, $cart);


        return API::createResponse($order, 200);
    }

    /**
     * @api {post} /checkout/select-shipment-methods Select Shipment Methods
     * @apiName Select Shipment Methods
     * @apiGroup Checkout
     *
     * @apiParam {String} customer_ref_id Customer ID (User = sso id, Non-user = random id).
     * @apiParam {String} customer_type (user / non-user).
     * @apiParam {Array} shipments Array of shipment id and payment pkey.
     *
     * @apiExample Example array:
     *  shipments = {
     *      '1' => 141013832031620,
     *      '1049' => 141013832031620
     *  }
     *
     * @apiSuccess {Array} data Checkout Information.
     */
    public function postSelectShipmentMethods(PApp $app)
    {
        $data = array(
            'app_id' => $app->getKey(),
            'customer_type' => Input::get('customer_type'),
            'customer_ref_id' => Input::get('customer_ref_id')
        );

        // get cart
        $cart = $this->cart->getCart($data);

        // get submitted shipments
        $subbmittedShipments = array_filter(Input::get('shipments', array()));

        if (!empty($subbmittedShipments))
        {
            // convert pkey to id
            $shipments = array();
            foreach (Input::get('shipments') as $shipment => $pkey)
            {
                $shipmentMethod = ShippingMethod::wherePkey($pkey)->first();
                $shipments[$shipment] = ($shipmentMethod)?$shipmentMethod->getKey():null;
            }

            // prepare data
            $data = array(
                'shipments' => $shipments
            );

            // update
            $this->checkout->update($cart, $data);

            Event::fire('Cart.setUnConfirm', array($cart));
        }

        // build
        $order = $this->checkout->buildOrder($app, $cart);

        return API::createResponse($order, 200);
    }

    /**
     * @api {post} /checkout/confirm Confirm customer and shipment information
     * @apiName Confirm
     * @apiDescription When users update items, customer info, shipment info confirm_checkout will be set to 0 and checkout2 will not allowed to access.
     * @apiGroup Checkout
     *
     * @apiParam {String} customer_ref_id Customer ID (User = sso id, Non-user = random id).
     * @apiParam {String} customer_type (user / non-user).
     *
     * @apiSuccess {Array} data Checkout Information.
     */
    public function postConfirm(PApp $app)
    {
        $data = array(
            'app_id' => $app->getKey(),
            'customer_type' => Input::get('customer_type'),
            'customer_ref_id' => Input::get('customer_ref_id')
        );

        $cart = $this->cart->getCart($data);

        // update
        $this->checkout->update($cart, array('confirm_checkout' => 1));

        // build
        $order = $this->checkout->buildOrder($app, $cart);

        return API::createResponse($order, 200);
    }

    /**
     * @api {post} /checkout/create-order Create order
     * @apiName Create order
     * @apiDescription You can customize out put via parameter "form", it will return html with comtains auto submit form to KBANK if form is 1 else is RC4 code.
     * @apiGroup Checkout
     *
     * @apiParam {String} customer_ref_id Customer ID (User = sso id, Non-user = random id).
     * @apiParam {String} customer_type (user / non-user).
     * @apiParam {Int} form (0 / 1).
     *
     * @apiSuccess {Array} data HTML with auto submit form or RC4 code.
     */
    public function postCreateOrder(PApp $app)
    {
        $data = array(
            'app_id'          => $app->getKey(),
            'customer_type'   => Input::get('customer_type'),
            'customer_ref_id' => Input::get('customer_ref_id'),
            // 'inventory_id' => Input::get('inventory_id')
        );

        // get cart
        $cart = $this->cart->getCart($data);

        if ($cart->totalQty == 0)
        {
            $errResponse = array('message' => "Cart is empty");

            return API::createResponse($errResponse, 400);
        }

        // check coupon
        $response = Event::fire('Checkout.onCreatingOrder', $cart);
        if (in_array('promotion_code_expired', $response, true))
        {
            $errResponse = array('cart' => $cart, 'message' => "Promotion code expired.");

            return API::createResponse($errResponse, 400);
        }

        // create order
//        try
//        {
            $order = $this->checkout->createOrder($app, $cart);
//        }
//        catch (Exception $e)
//        {
//            return API::createResponse($e->getMessage(), 400);
//        }

        $response = Event::fire('Checkout.onCreatedOrder', $order);

        PCMSPromotion::transferValidPromotions($cart, $order);

        // delete cart
        $this->cart->deleteCart($data);

        // if not COD; go to WeTrust
        $order->load('payment');
        if (strtolower($order->payment->code) === Order::COD)
        {
            $order_id = $order->order_id;
            $postUrl = $order->app->foreground_url.'?'.http_build_query(array(
                    'method'   => $order->payment->code,
                    'order_id' => $order_id,
            ));

            $data = compact('postUrl', 'order_id');
            if (Input::has('form'))
            {
                return API::createResponse(array(
                    'html' => View::make('checkouts.cod_form', $data)->render()
                ));
            }
        }
        else
        {
            // ========== Store XML Log ==========
            $xmlLog = new OrderXmlLog;
            $xmlLog->order_id = $order->id;
            $xmlLog->data = $this->wetrust->buildXML($order);
//            How to get $config['rc4key'] ? (in app/libraries/wetrust/config).
//            $xmlLog->data_rc4 = \Wetrust\RC4::EncryptRC4($config['rc4key'], $xmlLog->data);
            $xmlLog->save();
            // ========== Store XML Log ==========

            if (Input::has('form'))
            {
                return API::createResponse(array(
                    'html' => $this->wetrust->generateHTMLSubmitForm($order)
                ));
            }

            // generate returned data
            $data = $this->wetrust->generateSubmitData($order);
        }

        return API::createResponse($data, 200);
    }

}
