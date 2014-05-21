<?php

class CheckoutRepository implements CheckoutRepositoryInterface {

    protected $order, $shippingMethod, $stock;

    public function __construct(OrderRepositoryInterface $order, ShippingMethodRepositoryInterface $shippingMethod, StockRepositoryInterface $stock)
    {
        $this->order = $order;
        $this->shippingMethod = $shippingMethod;
        $this->stock = $stock;
    }
    

    public function getDeliveryAreaId($city_id, $province_id)
    {
        // หาว่าที่ส่งอยู่ใน delivery_area ไหน
        $city = City::find($city_id);

        if ($city != false && $city->delivery_area_id != false)
        {
            $area_id = $city->delivery_area_id;
        }
        else
        {
            $province = Province::find($province_id);

            $area_id = $province->delivery_area_id;
        }

        return intval($area_id);
    }

    public function getShippingMethodsByArea($area_id)
    {
        // หา shipping_method ที่ส่งไป delivery_area นั้นได้
        $area = DeliveryArea::findOrFail($area_id);
        return $area->shippableMethods();
    }

    public function getShippingMethodsByCartDetail(CartDetail $cartDetail)
    {
        // if Product has own Shipping Method, return Available Shipping Method of this product.
        $product = $cartDetail->product;
        if (!empty($product))
        {
            $shippingMethods = $this->shippingMethod->getByProduct($product);
            
            if (Input::has('cod1'))
            {
                d('product', $shippingMethods);
            }

            if (!empty($shippingMethods))
            {
                return $shippingMethods;
            }
        }

        // if Vendor of this product has own Shipping Method, return Available Shipping Method of this Vendor.
        $vendor = $cartDetail->vvendor;
        if (!empty($vendor))
        {
            $shippingMethods = $this->shippingMethod->getByVendor($vendor);

            if (Input::has('cod1'))
            {
                d('vendor', $shippingMethods);
            }

            
            if (!empty($shippingMethods))
            {
                return $shippingMethods;
            }
        }

        // return Available Shipping Method by Stock Type.
        $variant = $cartDetail->variant;

        if (!empty($variant))
        {
            // SafeDebug::d($variant->stock_type, 'CheckoutRepository-buildOrder');
            $stockType = Stock::getStockType($variant->stock_type);
            // SafeDebug::d($stockType, 'CheckoutRepository-buildOrder');
            $shippingMethods = $this->shippingMethod->getByStockType($stockType);
            // SafeDebug::d($shippingMethods, 'CheckoutRepository-buildOrder');
            
            if (Input::has('cod1'))
            {
                d('stock', $shippingMethods);
            }


            if (!empty($shippingMethods))
            {
                return $shippingMethods;
            }
        }

        return FALSE;
    }

    public function isExceedDimensionLimit(ShippingMethod $method, $dimension_max, $dimension_mid, $dimension_min)
    {
        // ไม่มีการกำหนดขนาดสูงสุดที่ส่งได้ = ส่งได้
        if ($method->dimension_max == false || $method->dimension_mid == false || $method->dimension_min == false)
            return false;

        // ถ้ามีการกำหนดขนาดสูงสุดที่ส่งได้ และมีด้านใดด้านหนึ่งเกินขนาดของสินค้า = ส่งไม่ได้
        if ($method->dimension_max < $dimension_max || $method->dimension_mid < $dimension_mid || $method->dimension_min < $dimension_min)
            return true;

        return false;
    }

    public function isExceedWeightLimit(ShippingMethod $method, $shipment_weight)
    {
        // ถ้ามีการกำหนดน้ำหนักสูงสุดที่ส่งได้ไว้ และน้ำหนักของ shipment เกินน้ำหนักที่กำหนด = ส่งไม่ได้
        if ($method->max_weight != false && $method->max_weight < $shipment_weight)
            return true;

        return false;
    }

    public function calculateShippingFee($shippingMethodId, $deliveryAreaId, $shipmentWeight)
    {
        // คำนวณหาน้ำหนักของ item ทั้ง shipment ตามสูตร
        // ดูว่าอยู่ในช่วงไหน
        // ....
        // Get Shipping Fee Range
        $shippingFee = ShippingFee::with('shippingBox')
                        ->where('shipping_method_id', $shippingMethodId)
                        ->where('delivery_area_id', $deliveryAreaId)
                        ->where('product_weight_min', '<=', $shipmentWeight)
                        ->where('product_weight_max', '>=', $shipmentWeight)->first();

        // if (empty($shippingFee) or empty($shippingFee->shippingBox))
        if (empty($shippingFee))
        {
            $shippingFee = ShippingFee::with('shippingBox')
                            ->where('shipping_method_id', $shippingMethodId)
                            ->where('delivery_area_id', $deliveryAreaId)
                            ->where('product_weight_min', '<=', $shipmentWeight)
                            ->where('product_weight_max', '=', 0)->first();

            // Error, Cannot find Shipping Fee in this Weight Range.
            if (empty($shippingFee))
            {
                return FALSE;
            }
        }

        // Cannot find Shipping Box.
        if (empty($shippingFee->shippingBox))
        {
            $boxPrice = 0;
        }
        else
        {
            $boxPrice = $shippingFee->shippingBox->price;
        }

        $shippingPrice = $shippingFee->shipping_fee + $boxPrice;

        // return shipping fee + box price
        return $shippingPrice;
    }

    public function setDeliveryAddress(Cart $cart, $address, $city_id, $province_id, $postcode)
    {
        // บันทึก address ใหม่
        $data = array(
            'customer_address' => $address,
            'customer_city_id' => $city_id,
            'customer_province_id' => $province_id,
            'customer_postcode' => $postcode
        );
        $this->update($cart, $data);
    }

    // public function setPaymentChannel(Cart $cart, $payment_enum)
    // {
    //     // use $this->update();
    // }

    public function setCustomerInfo(Cart $cart, $name, $tel = null, $email = null)
    {
        $data = array(
            'customer_name' => $name,
            'customer_tel' => $tel,
            'customer_email' => $email
        );
        $this->update($cart, $data);
    }

    public function update(Cart $cart, $data)
    {
        foreach ($data as $key => $value)
        {
            $cart->{$key} = $value;
        }

        return $cart->save();
    }

    public function buildOrder(PApp $app, Cart $cart)
    {
        $order = array();

        //$order['order_ref'] = time(); // don't know where to get
        $order['order_ref'] = null; // don't know where to get
        $order['app_id'] = $app->id;
        $order['ref1'] = null;
        $order['ref2'] = null; // don't know where to get
        $order['ref3'] = null; // don't know where to get
        $order['customer_ref_id'] = $cart->customer_ref_id;
        $order['customer_address_id'] = $cart->customer_address_id;
        $order['customer_name'] = $cart->customer_name;
        $order['customer_address'] = $cart->customer_address;
        $order['customer_district_id'] = $cart->customer_district_id;
        $order['customer_city_id'] = $cart->customer_city_id;
        $order['customer_province_id'] = $cart->customer_province_id;
        $order['customer_district'] = empty($cart->district) ? null : $cart->district->name;
        $order['customer_city'] = empty($cart->city) ? null : $cart->city->name;
        $order['customer_province'] = empty($cart->province) ? null : $cart->province->name;
        $order['customer_postcode'] = $cart->customer_postcode;
        $order['customer_tel'] = $cart->customer_tel;
        $order['customer_email'] = $cart->customer_email;
        $order['payment_channel'] = $cart->payment_channel;
        $order['payment_method'] = $cart->payment_method;
        $order['payment_method'] = empty($cart->payment_method) ? null : PaymentMethod::find($cart->payment_method)->pkey;
        $order['type'] = $cart->type;
        $order['installment'] = json_decode($cart->installment);
        $order['transaction_time'] = $cart->transaction_time;
        $order['total_price'] = 0;
        $order['total_discount'] = 0;
        $order['total_shipping_fee'] = 0;
        $order['discount'] = $cart->discount;
        $order['discount_text'] = $cart->discount_text;
        $order['sub_total'] = 0;
        $order['order_status'] = 'draft';
        $order['payment_status'] = 'waiting';
        $order['customer_status'] = $cart->customer_status;
        $order['items_count'] = 0;
        $order['promotions'] = $cart->promotionData;
        $order['discount_campaigns'] = $cart->discountCampaignData;
        $order['cash_voucher'] = $cart->cashVoucher;
        $order['confirm_checkout'] = $cart->confirm_checkout;


        // SafeDebug::d($order, 'CheckoutRepository-buildOrder');

        $shipping_method_ids = array();

        // SafeDebug::d($order['customer_province_id'], 'CheckoutRepository-buildOrder');
        // หา shipping method จาก area ถ้ามีการเลือกที่อยู่มาแล้ว
        if ($order['customer_province_id'] != false)
        {
            $area_id = $this->getDeliveryAreaId($order['customer_city_id'], $order['customer_province_id']);
            // SafeDebug::d($area_id, 'CheckoutRepository-buildOrder');
            $shippingMethodsByArea = $this->getShippingMethodsByArea($area_id);
            // SafeDebug::d($shippingMethodsByArea, 'CheckoutRepository-buildOrder');
            $shipping_method_ids = $shippingMethodsByArea->lists('pkey', 'pkey');
            // SafeDebug::d($shipping_method_ids, 'CheckoutRepository-buildOrder');
        }

        $order['shipments'] = array();

        if (count($cart->cartDetails) > 0)
        {
            $hasCODProduct = false;
            
            foreach ($cart->cartDetails as $item)
            {
                if (Stock::isNonStock($item->variant->stock_type)) // non-stock
                {
                    $key = $item->vendor_id;
                }
                else
                {
                    // true corp
                    $key = 1;
                }

                if (!array_key_exists($key, $order['shipments']))
                {
                    $order['shipments'][$key] = array();

                    $order['shipments'][$key]['order_id'] = null; // OrderRepo got this
                    $order['shipments'][$key]['shipment_ref'] = time(); // don't know where to get <- สร้างเลขเอง
                    $order['shipments'][$key]['shipping_method'] = null;
                    $order['shipments'][$key]['shipping_fee'] = 0;
                    $order['shipments'][$key]['shipment_status'] = null;
                    $order['shipments'][$key]['total_price'] = 0;
                    $order['shipments'][$key]['total_discount'] = 0;
                    $order['shipments'][$key]['discount'] = 0;
                    $order['shipments'][$key]['sub_total'] = 0;
                    $order['shipments'][$key]['stock_type'] = $item->variant->stock_type;
                    $order['shipments'][$key]['vendor_id'] = $item->vvendor ? $item->vvendor->vendor_id : '';
                    $order['shipments'][$key]['vendor_name'] = $item->vvendor ? $item->vvendor->name : '';
                    $order['shipments'][$key]['shop_id'] = $item->shop_id;
                    $order['shipments'][$key]['shop_name'] = $item->shop->name;
                    $order['shipments'][$key]['shipment_weight'] = 0; // next step
                    $order['shipments'][$key]['shipment_highest_dimension_max'] = 0; // next step
                    $order['shipments'][$key]['shipment_highest_dimension_mid'] = 0; // next step
                    $order['shipments'][$key]['shipment_highest_dimension_min'] = 0; // next step

                    $order['shipments'][$key]['items'] = array();
                    $order['shipments'][$key]['items_count'] = 0;
                    $order['shipments'][$key]['available_shipping_methods'] = $shipping_method_ids;
                    // SafeDebug::d($order['shipments'][$key]['available_shipping_methods'], 'CheckoutRepository-buildOrder');
                }

                $data = array();

                $data['shipment_id'] = null; // OrderRepo got this
                $data['order_id'] = null; // OrderRepo got this
                $data['product_pkey'] = $item->variant->product->pkey;
                $data['inventory_id'] = $item->inventory_id;
                $data['material_code'] = $item->variant->material_code;
                $data['thumbnail'] = @$item->image;
                $data['name'] = $item->title;
                $data['category'] = null; // don't know where to get <- ปล่อยว่างไปเลย ไม่ต้องสนใจ ใช้สำหรับ iTruemart เท่านั้น
                $data['brand'] = null; // don't know where to get <- 1) หาจาก variant->product->brand->name 2) เพิ่ม field brand_id แล้วไปหาจาก variant->product->brand_id
                $data['quantity'] = $item->quantity;
                $data['price'] = $item->price;
                $data['margin'] = 0; // don't know where to get <- price - cost
                $data['discount'] = 0;
                $data['total_price'] = $item->quantity * $item->price;
                $data['total_discount'] = $item->total_discount;
                $data['total_margin'] = 0; // don't know where to get
//                $data['sub_total'] = max($data['price'] - $data['discount'], 0);

                $data['vendor_id'] = $item->vendor_id;
                $data['shop_id'] = $item->vendor_id;
                $data['options'] = null; // don't know where to get <- ไม่ต้องสนใจ ใช้สำหรับ iTruemart เท่านั้น
                $data['item_status'] = null;

                $order['shipments'][$key]['items'][] = $data;
                $order['shipments'][$key]['items_count'] += $item->quantity;
                $order['items_count'] += $item->quantity;

                $order['shipments'][$key]['shipment_weight'] += intval($item->variant->shipping_weight * $item->quantity);
                $order['shipments'][$key]['shipment_highest_dimension_max'] = max($order['shipments'][$key]['shipment_highest_dimension_max'], $item->variant->dimension_max);
                $order['shipments'][$key]['shipment_highest_dimension_mid'] = max($order['shipments'][$key]['shipment_highest_dimension_mid'], $item->variant->dimension_mid);
                $order['shipments'][$key]['shipment_highest_dimension_min'] = max($order['shipments'][$key]['shipment_highest_dimension_min'], $item->variant->dimension_min);

                $shippingMethodsOfItem = $this->getShippingMethodsByCartDetail($item);
                if (Input::has('cod'))
                {
                    d($order['shipments'][$key]['available_shipping_methods'], $shippingMethodsOfItem->lists('pkey', 'pkey'));
                }
                // SafeDebug::d($shippingMethodsOfItem, 'CheckoutRepository-buildOrder');
                $order['shipments'][$key]['available_shipping_methods'] = array_intersect_assoc($order['shipments'][$key]['available_shipping_methods'], $shippingMethodsOfItem->lists('pkey', 'pkey'));
                // SafeDebug::d($order['shipments'][$key]['available_shipping_methods'], 'CheckoutRepository-buildOrder');
                
                if (Input::has('cod'))
                {
                    d($order['shipments'][$key]['available_shipping_methods']);
                }
                
                if ($item->variant->product->allow_cod == 1 && ! Stock::isNonStock($item->variant->stock_type))
                {
                    $hasCODProduct = true;
                }

                // คำนวนราคาสินค้ารวมใน shipment
                $order['shipments'][$key]['total_price'] += $item->quantity * $item->price;
                $order['shipments'][$key]['total_discount'] += $item->total_discount;
                $order['shipments'][$key]['discount'] += $item->discount;
                $order['shipments'][$key]['sub_total'] = max($order['shipments'][$key]['total_price'] - $order['shipments'][$key]['total_discount'] - $order['shipments'][$key]['discount'], 0);

                // คำนวนราคาสินค้ารวมของทั้ง order
//                $order['total_price'] += $item->price - $item->total_discount;
                $order['total_price'] += $item->quantity * $item->price;
                $order['total_discount'] += $item->total_discount;
            }

            $shippingCOD = ShippingMethod::where('slug', Order::COD)->firstOrFail();
            $paymentCOD = PaymentMethod::where('code', Order::COD)->firstOrFail();
            $allowCOD = false;
            $shipmentAllowCOD = true;
            $areaAllowCOD = false;
            $priceAllowCod = false;
            
            // ถ้า user เลือก shippment method แล้ว assign เข้า $order
            if (!empty($cart->shipments))
            {
                foreach ($cart->shipments as $key => $shippingMethodId)
                {
                    if (array_key_exists($key, $order['shipments']))
                    {
                        if (is_null($shippingMethodId))
                        {
                            $order['shipments'][$key]['shipping_method'] = null;
                        }
                        else
                        {
                            try
                            {
                                $order['shipments'][$key]['shipping_method'] = ShippingMethod::findOrFail($shippingMethodId)->pkey;
                            }
                            catch (Exception $e)
                            {
                                $order['shipments'][$key]['shipping_method'] = null;
                            }
                            // $order['shipments'][$key]['shipping_method'] = $shippingMethodId;
                        }
                    }
                }
            }

            // จัดการเงื่อนไข available_shipping_methods
            if ($app->free_shipping == 'disabled')
            {
                if ($order['customer_province_id'] != false)
                {
                    // มีหลาย shipments แปลว่าต้องมี non-stock ซึ่งไม่ allow COD
                    if (count($order['shipments']) > 1)
                    {
                        $shipmentAllowCOD = false;
                    }

                    foreach ($order['shipments'] as $key => $shipment)
                    {
                        // if there is non-stock; not allow COD
                        if (Stock::isNonStock($shipment['stock_type']))
                        {
                            $shipmentAllowCOD = false;
                        }
                        // if stock shipment and sub total > 15
//                        else if ($shipment['sub_total'] > 15)
                        else if ($shipment['total_price'] - $shipment['total_discount'] > 15)
                        {
                            $priceAllowCod = true;
                        }

                        foreach ($shippingMethodsByArea as $shippingMethod)
                        {
                            if (strtolower($shippingMethod->slug) == Order::COD)
                            {
                                $areaAllowCOD = true;
                            }

                            // ตัดวิธีการส่งที่น้ำหนักเกินออก
                            // ตัดวิธีการส่งถ้า max , mid , min demension เกินขนาดที่กำหนด
                            if ($this->isExceedWeightLimit($shippingMethod, array_get($shipment, 'shipment_weight')) || $this->isExceedDimensionLimit($shippingMethod, array_get($shipment, 'shipment_highest_dimension_max'), array_get($shipment, 'shipment_highest_dimension_mid'), array_get($shipment, 'shipment_highest_dimension_min')))
                            {
                                if (isset($order['shipments'][$key]['available_shipping_methods'][$shippingMethod->pkey]))
                                {
                                    // SafeDebug::d('exceed', 'CheckoutRepository-buildOrder');
                                    unset($order['shipments'][$key]['available_shipping_methods'][$shippingMethod->pkey]);
                                }
                            }
                            // คำนวณค่าส่งถ้าไม่ถูกตัดออก
                            else
                            {
                                $fee = floatval($this->calculateShippingFee($shippingMethod->id, $area_id, array_get($shipment, 'shipment_weight')));
                                // SafeDebug::d($fee, 'CheckoutRepository-buildOrder');

                                if (isset($order['shipments'][$key]['available_shipping_methods'][$shippingMethod->pkey]))
                                {
                                    $order['shipments'][$key]['available_shipping_methods'][$shippingMethod->pkey] = array(
                                        'name' => $shippingMethod->name,
                                        'fee' => $fee,
                                        'description' => $shippingMethod->description
                                    );
                                    // SafeDebug::d($order['shipments'][$key]['available_shipping_methods'][$shippingMethod->pkey], 'CheckoutRepository-buildOrder');
                                }

                                // assign ค่า fee เค้า shippment
                                if ($order['shipments'][$key]['shipping_method'] == $shippingMethod->pkey)
                                {
                                    $order['shipments'][$key]['shipping_fee'] = $fee;
                                }
                            }
                            // SafeDebug::d($order['shipments'][$key]['available_shipping_methods'], 'CheckoutRepository-buildOrder');
                        }

                        // ถ้า shippment method ที่ user เลือกมาไม่มีใน available_shipping_methods, จะเป็น null
    //                    if ( ! in_array($order['shipments'][$key]['shipping_method'], array_keys($order['shipments'][$key]['available_shipping_methods'])))
    //                    {
    //                        $order['shipments'][$key]['shipping_method'] = null;
    //                    }

                        // รวมค่า shipping fee ใส่ order
                        $order['total_shipping_fee'] += $order['shipments'][$key]['shipping_fee'];
                    }
                    
//                    sd($shipmentAllowCOD, $hasCODProduct, $areaAllowCOD);

                    // ถ้าผ่านทุกเงื่อนไข allowCOD
                    // $hasCODProduct: ถ้ามี product ใด allow COD, shipment จะ allow COD
                    if (Input::has('cod'))
                    {
                        d($shipmentAllowCOD, $hasCODProduct, $areaAllowCOD, $priceAllowCod);
                    }
                    
                    $shipment = &$order['shipments'][1];
                    
                    if ($shipmentAllowCOD && $hasCODProduct && $areaAllowCOD && $priceAllowCod)
                    {
                        $fee = floatval($this->calculateShippingFee($shippingCOD->id, $area_id, array_get($shipment, 'shipment_weight')));

                        $shipment['available_shipping_methods'][$shippingCOD->pkey] = array(
                            'name' => $shippingCOD->name,
                            'fee' => $fee,
                            'description' => $shippingCOD->description
                        );

                        $allowCOD = true;
                    }
                    else
                    {
                        unset($shipment['available_shipping_methods'][$shippingCOD->pkey]);
                    }
                }
            }
            else
            {
                $freeShipping = ShippingMethod::whereSlug(Order::FREE_SHIPPING)->first();
                
                foreach ($order['shipments'] as &$shipment)
                {
                    $shipment['available_shipping_methods'] = array();
                    $shipment['available_shipping_methods'][$freeShipping->pkey] = array(
                        'name' => $freeShipping->name,
                        'fee' => 0,
                        'description' => $freeShipping->description
                    );
                }
            }
            
            if (Input::has('cod'))
            {
                d($allowCOD);
                d($shipment['available_shipping_methods']);
            }
            
            // รวม sub_total
            $order['sub_total'] = max(
                (
                    ($order['total_price'] - $order['discount'] - $order['total_discount'])
                     + $order['total_shipping_fee']
                ) - $order['cash_voucher']
            , 0);
            
            // จัดการเงื่อนไข available_payment_methods
            if ($order['customer_province_id'] != false)
            {
                $payments = PaymentMethod::all();

                if ($cart->type == 'normal')
                {
                    // remove installment
                    $payments = $payments->filter(function($payment) {

                        return $payment->code != Order::PAYMENT_CODE_INSTALLMENT;

                    });
                }

                if ($order['sub_total'] == 0)
                {
                    // only ATM
                    $payments = $payments->filter(function($payment) {

                        return $payment->code == Order::PAYMENT_CODE_ATM;

                    });
                }
                else if ($order['sub_total'] <= 15)
                {
                    // only online
                    $payments = $payments->filter(function($payment) {

                        return $payment->channel == Order::ONLINE;

                    });
                }
                
                if ( ! $allowCOD)
                {
                    // not COD
                    $payments = $payments->filter(function($payment) {

                        return strtolower($payment->code) != Order::COD;

                    });
                }

                foreach ($payments as $payment)
                {
                    // if installment
                    if (strtolower($payment->code) === strtolower(Order::INSTALLMENT_WETRUST_CODE))
                    {
                        $variant = $cart->cartDetails()->first()->variant;

                        if ($variant->allow_installment)
                        {
                            $installment = $variant->installment;
                        }
                        else if ($variant->product->allow_installment)
                        {
                            $installment = $variant->product->installment;
                        }
                        else
                        {
                            $installment = null;
                        }

                        if ($installment != false)
                        {
                            $order['available_payment_methods'][$payment->pkey] = array(
                                'name' => $payment->name,
                                'periods' => $installment->periods
                            );
                        }
                    }
                    else if ($allowCOD && strtolower($payment->code) === Order::COD)
                    {
                        $order['available_payment_methods'][$payment->pkey] = array(
                            'name' => $payment->name,
                        );
                    }
                    else
                    {
                        $order['available_payment_methods'][$payment->pkey] = array(
                            'name' => $payment->name
                        );
                    }
                }
            }
            
            // ถ้า user เลือก shipping_method เป็น COD (COD สามารถเลือกได้เฉพาะ stock)
            // แล้ว payment จะเหลือแต่ COD
            if (isset($order['shipments'][1]))
            {
                if ($order['shipments'][1]['shipping_method'] == $shippingCOD->pkey)
                {
                    foreach (array_keys($order['available_payment_methods']) as $pkey)
                    {
                        if ($pkey != $paymentCOD->pkey)
                        {
                            unset($order['available_payment_methods'][$pkey]);
                        }
                    }
                }
                else // ถ้า shipping_method ที่เลือก ไม่เป็น COD, payment จะไม่มี COD ให้เลือก
                {
                    unset($order['available_payment_methods'][$paymentCOD->pkey]);
                }
            }
            
            // $t = current(array_keys($order['shipments']));
            // SafeDebug::d($order['shipments'][$t]['available_shipping_methods'], 'CheckoutRepository-buildOrder');
        }
        
        if (Input::has('cod'))
        {
            d($order['shipments'][1]['available_shipping_methods']);
        }
        
        if (Input::has('cod'))
        {
            die;
        }

        return $order;
    }

    public function calculateMargin(Order $order, $pickupItems)
    {
        // This Order doesn't have any Shipments.
        if ( $order->shipments->isEmpty() )
        {
            break;
        }

        $sumOrderDiscount = $order->discount;
        $affectedDiscount = 0;

        // Calculate Each Shipment.
        foreach ($order->shipments as $shipment)
        {
            // This Shipment doesn't have any ShipmentItems.
            if ( $shipment->shipmentItems->isEmpty() )
            {
                continue;
            }

            foreach ($shipment->shipmentItems as $shipmentItem)
            {
                // Get Cost Price from StockRepository
                $pickupItem = $pickupItems[$shipmentItem->inventory_id];
                // $pickupItem = $this->stock->pickup($order->app_id, $shipmentItem->inventory_id, $shipmentItem->quantity);

                $cost              = (int) $pickupItem['averageCost'];
                $price             = $shipmentItem->price;
                $qty               = $shipmentItem->quantity;
                $totalPrice        = $price * $qty;

                $vendorOwe         = 0;
                $businessLost      = 0;

                $totalCustomerPay  = $totalPrice;
                $margin            = $price - $cost;

                // if Price below than Cost.
                if ($margin < 0)
                {
                    $margin    = 0;
                    $vendorOwe = ($cost - $price) * $qty;
                }

                $totalMargin = $margin * $qty;

                $fee = 0;
                $itemDiscount  = $shipmentItem->discount;
                $orderDiscount = $order->discount;

                if ($itemDiscount > 0)
                {
                    // Item has Discount. ($itemDiscount must not below than $totalPrice)
                    $totalCustomerPay = $totalPrice - $itemDiscount;
                    if ($totalCustomerPay < 0)
                    {
                        $totalCustomerPay = 0;
                    }

                    if ($totalMargin > $itemDiscount)
                    {
                        // ถ้า Margin มากกว่า itemDiscount ... ยังเอามาหักได้อยู่
                        $totalMargin       = $totalMargin - $itemDiscount;
                        $businessLost      = $businessLost + $itemDiscount;
                    }
                    else
                    {
                        // ถ้า totalMargin เหลือให้หักไม่มากพอ ...
                        // - หัก margin จนเหลือ 0
                        // - business lost บวกเพิ่มเท่ากับ totalMargin ที่หายไป
                        // - ส่วนที่เหลือ ที่ยังหักไม่หมดจาก $itemDiscount ... เอาไปทดไว้ที่ $vendorOwe
                        $businessLost = $businessLost + $totalMargin;
                        $diff         = $itemDiscount - $totalMargin;
                        $vendorOwe    = $vendorOwe + $diff;
                        $totalMargin  = 0;
                    }
                }

                // คำนวณส่วนลดจากทั้ง Order (เอามาหักกับ margin)
                $affectedDiscount = 0;
                if ($sumOrderDiscount > 0)
                {
                    // ถ้า Margin เหลือมากกว่า 0 ... ให้เริ่มหักจาก margin ได้
                    if ($totalMargin > 0)
                    {
                        if ($totalMargin > $sumOrderDiscount)
                        {
                            // ถ้า Margin เหลือมากพอให้หักได้ทั้งหมด
                            $totalMargin      = $totalMargin - $sumOrderDiscount;
                            $totalCustomerPay = $totalCustomerPay - $sumOrderDiscount;
                            $businessLost     = $businessLost + $sumOrderDiscount;

                            $affectedDiscount = $sumOrderDiscount;
                            $sumOrderDiscount = 0;
                        }
                        else
                        {
                            // Margin เหลือไม่พอ ... ให้หักไปได้แค่บางส่วน
                            $affectedDiscount = $totalMargin;
                            $sumOrderDiscount = $sumOrderDiscount - $affectedDiscount;

                            $totalMargin      = 0;
                            $totalCustomerPay = $totalCustomerPay - $affectedDiscount;
                            $businessLost     = $businessLost + $affectedDiscount;
                        }
                    }
                }

                // Calculate customer pay (per item)
                if ( ($totalCustomerPay % $qty) > 0 )
                {
                    $customerPay = floor($totalCustomerPay / $qty);
                    $vendorOwe = $vendorOwe + ($totalCustomerPay % $qty) ;
                }
                else
                {
                    $customerPay = $totalCustomerPay / $qty;
                }

                // Get OrderTransaction (Or Create new)
                $ot = OrderTransaction::where('order_id', $order->id)
                        ->where('order_shipment_id', $shipment->id)
                        ->where('order_shipment_item_id', $shipmentItem->id)
                        ->first();
                if (empty($ot))
                {
                    $ot = new OrderTransaction;
                    $ot->order_id = $order->id;
                    $ot->order_shipment_id = $shipment->id;
                    $ot->order_shipment_item_id = $shipmentItem->id;
                    $ot->item_title = $shipmentItem->brand . ' : ' . $shipmentItem->name;
                }
                // Update OrderTransaction
                $ot->price = $price;
                $ot->qty = $qty;
                $ot->total_price = $totalPrice;
                $ot->item_discount = $itemDiscount;
                $ot->order_discount = $orderDiscount;
                $ot->affected_discount = $affectedDiscount;
                $ot->cost = $cost;
                $ot->margin = $margin;
                $ot->total_margin = $totalMargin;
                $ot->total_customer_pay = $totalCustomerPay;
                $ot->customer_pay = $customerPay;
                $ot->vendor_owe = $vendorOwe;
                $ot->fee = $fee;
                $ot->business_lost = $businessLost;
                $ot->save();

                // Update ShipmentItem
                $shipmentItem->margin = $ot->margin;
                $shipmentItem->total_margin = $ot->total_margin;
                $shipmentItem->save();
            }

            // Add or Update Shipping Price detail as an Item in OrderTransation.
            // $rsShippingMethod = ShippingMethod::wherePkey($shipment->shipping_method)->first();
            // $shipmentTitle = $shipment->shipping_method;
            $rsShippingMethod = $shipment->method;
            $shipmentTitle = (!empty($rsShippingMethod) ) ? $rsShippingMethod->name : '';

            $shippingOT = OrderTransaction::where('order_id', $order->id)
                            ->where('order_shipment_id', $shipment->id)
                            ->where('order_shipment_item_id', 0)
                            ->first();
            if (empty($shippingOT))
            {
                $shippingOT = new OrderTransaction;
            }
            $shippingOT->order_id = $order->id;
            $shippingOT->order_shipment_id = $shipment->id;
            $shippingOT->order_shipment_item_id = 0;
            $shippingOT->item_title = $shipmentTitle;
            $shippingOT->price = $shipment->shipping_fee;
            $shippingOT->qty = 1;
            $shippingOT->total_price = $shipment->shipping_fee;
            $shippingOT->customer_pay = $shipment->shipping_fee;
            $shippingOT->total_customer_pay = $shipment->shipping_fee;
            $shippingOT->save();
        }

        // ถ้าวนลูปจนครบทุก item แล้ว $sumOrderDiscount ยังจะเหลือมากกว่า 0 อยู่ .... เอาไปหักต่อที่ total_customer_pay
        if ($sumOrderDiscount > 0)
        {
            foreach ($order->shipments as $shipment)
            {
                // This Shipment doesn't have any ShipmentItems.
                if ( $shipment->shipmentItems->isEmpty() )
                {
                    continue;
                }

                foreach ($shipment->shipmentItems as $shipmentItem)
                {
                    if ($sumOrderDiscount <= 0)
                    {
                        break;
                    }

                    $ot = OrderTransaction::where('order_id', $order->id)
                            ->where('order_shipment_id', $shipment->id)
                            ->where('order_shipment_item_id', $shipmentItem->id)
                            ->first();

                    // ถ้า $totalCustomerPay เหลือมากกว่า $sumOrderDiscount
                    if ($ot->total_customer_pay > $sumOrderDiscount)
                    {
                        // ถ้า totalCustomerPay เหลือมากพอให้หักได้ทั้งหมด
                        $ot->vendor_owe         += $sumOrderDiscount;
                        $ot->affected_discount  += $sumOrderDiscount;
                        $ot->total_customer_pay -= $sumOrderDiscount;
                        $sumOrderDiscount       = 0;
                    }
                    else
                    {
                        // totalCustomerPay เหลือไม่พอ ... ให้หักไปได้แค่บางส่วน
                        $ot->vendor_owe         += $ot->total_customer_pay;
                        $ot->affected_discount  += $ot->total_customer_pay;
                        $sumOrderDiscount       -= $ot->total_customer_pay;
                        $ot->total_customer_pay = 0;
                    }

                    // Calculate customer pay (per item)
                    if ( ($ot->total_customer_pay % $ot->qty) > 0 )
                    {
                        $ot->customer_pay = floor($ot->total_customer_pay / $ot->qty);
                        $ot->vendorOwe    += ($ot->total_customer_pay % $ot->qty);
                    }
                    else
                    {
                        $ot->customer_pay = $ot->total_customer_pay / $ot->qty;
                    }

                    $ot->save();
                }
            }
        }

        // ถ้าวนลูปจนครบทุก item แล้ว $sumOrderDiscount ยังจะเหลือมากกว่า 0 อยู่อีก .... เอาไปหักต่อที่ shipment
        if ($sumOrderDiscount > 0)
        {
            foreach ($order->shipments as $shipment)
            {
                // This Shipment doesn't have any ShipmentItems.
                if ( $shipment->shipmentItems->isEmpty() )
                {
                    continue;
                }

                $shippingOT = OrderTransaction::where('order_id', $order->id)
                            ->where('order_shipment_id', $shipment->id)
                            ->where('order_shipment_item_id', 0)
                            ->first();

                // ถ้า $totalCustomerPay เหลือมากกว่า $sumOrderDiscount
                if ($shippingOT->total_customer_pay > $sumOrderDiscount)
                {
                    // ถ้า totalCustomerPay เหลือมากพอให้หักได้ทั้งหมด
                    $shippingOT->vendor_owe         += $sumOrderDiscount;
                    $shippingOT->affected_discount  += $sumOrderDiscount;
                    $shippingOT->total_customer_pay -= $sumOrderDiscount;
                    $sumOrderDiscount               = 0;
                }
                else
                {
                    // totalCustomerPay เหลือไม่พอ ... ให้หักไปได้แค่บางส่วน
                    $shippingOT->vendor_owe         += $shippingOT->total_customer_pay;
                    $shippingOT->affected_discount  += $shippingOT->total_customer_pay;
                    $sumOrderDiscount               -= $shippingOT->total_customer_pay;
                    $shippingOT->total_customer_pay = 0;
                }

                // Calculate customer pay (per item)
                if ( ($shippingOT->total_customer_pay % $shippingOT->qty) > 0 )
                {
                    $shippingOT->customer_pay = floor($shippingOT->total_customer_pay / $shippingOT->qty);
                    $shippingOT->vendorOwe    += ($shippingOT->total_customer_pay % $shippingOT->qty);
                }
                else
                {
                    $shippingOT->customer_pay = $shippingOT->total_customer_pay / $shippingOT->qty;
                }

                $shippingOT->save();
            }
        }

        /* Calculate Fee */
        $paymentMethod = $order->payment;
        if (!empty($paymentMethod) && $paymentMethod->transaction_fee > 0)
        {
            if (strtolower($paymentMethod->transaction_apply) == 'once')
            {
                $firstOT = OrderTransaction::where('order_id', $order->id)->orderBy('id', 'asc')->first();
                $firstOT->fee = $paymentMethod->transaction_fee;
                // $firstOT->vendor_owe += $firstOT->fee;
                if ($firstOT->total_margin > 0 && $firstOT->total_margin > $firstOT->fee)
                {
                    // $firstOT->total_margin -= $firstOT->fee;
                    $firstOT->business_lost += $firstOT->fee;
                }
                else
                {
                    $firstOT->business_lost += $firstOT->total_margin;
                    $firstOT->vendor_owe += $firstOT->fee - $firstOT->total_margin;
                    // $firstOT->total_margin = 0;
                }
                $firstOT->save();
                /*
                  $shipmentItem = OrderShipmentItem::find($firstOT->order_shipment_item_id);
                  $shipmentItem->total_margin = $firstOT->total_margin;
                  $shipmentItem->save();
                 */
            }
            else
            {
                // when $paymentMethod->transaction_apply == 'All Items'
                $orderTransactions = OrderTransaction::where('order_id', $order->id)->orderBy('id', 'asc')->get();
                foreach ($orderTransactions as $OT)
                {
                    $OT->fee = ($paymentMethod->transaction_fee / 100) * $OT->customer_pay;
                    // $OT->vendor_owe += $OT->fee;
                    if ($OT->total_margin > 0 && $OT->total_margin > $OT->fee)
                    {
                        // $OT->total_margin -= $OT->fee;
                        $OT->business_lost += $OT->fee;
                    }
                    else
                    {
                        $OT->business_lost += $OT->total_margin;
                        $OT->vendor_owe += $OT->fee - $OT->total_margin;
                        // $OT->total_margin = 0;
                    }
                    $OT->save();
                    /*
                      $shipmentItem = OrderShipmentItem::find($OT->order_shipment_item_id);
                      $shipmentItem->total_margin = $OT->total_margin;
                      $shipmentItem->save();
                     */
                }
            }
        }
    }

    public function createOrder(PApp $app, Cart $cart)
    {
        // build order from cart
        $order = $this->buildOrder($app, $cart);

        // create order
        $order = $this->order->create($app, $order);

        // pickupItems to get Cost and Lots
        $pickingItems = array();
        foreach ($order->shipments as $shipment)
        {
            if ( $shipment->shipmentItems->isEmpty() )
            {
                continue;
            }

            foreach ($shipment->shipmentItems as $shipmentItem)
            {
                $pickingItems[$shipmentItem->inventory_id] = $shipmentItem->quantity;
            }
        }
        $pickupItems = $this->stock->pickupItems($order->app_id, $pickingItems);

        if ($pickupItems != false)
        {
            // Hold Stock

            $holdStock = $this->stock->holdStock($order, $pickupItems);
            // $holdStock = 1;

            // Calculate margin & total_margin from $order data
            $this->calculateMargin($order, $pickupItems);
        }

        // If cannot Hold Stock, Delete Order (Soft Delete), Return FALSE;
        // if (!empty($response['data']) && $response['data']['holdStatus'] != 'success')
        if ( $pickupItems == false || ! $holdStock )
        {
            $order->orderTransactions()->delete();
            $order->shipmentItems()->delete();
            $order->shipments()->delete();
            $order->delete();

            throw new Exception('Cannot hold stock');
        }

        return $order;
    }

}
