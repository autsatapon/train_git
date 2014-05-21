<?php

class ApiPaymentController extends ApiBaseController {

    private $order;
    private $wetrust;
    protected $message;

    public function __construct(OrderRepositoryInterface $order, MessageRepository $message) {
        parent::__construct();

        $this->order = $order;
        $this->message = $message;
        $this->wetrust = App::make('wetrust');


        App::make('PCMSPromotionCart');
    }

    // not sure it's been used
    public function getProcess() {
        $html = $this->wetrust->generateHTMLSubmitForm(Order::find(1));

        return API::createResponse(array('html' => $html), 200);
    }

    // Foreground from App
    public function getCheckStatus(PApp $app) {
        $orderId = Input::get('order_id');

        // get order with where app_id
        $order = Order::whereId($orderId)->whereAppId($app->getKey())->firstOrFail();

        // if order_status is not draft, return
        if ($order->order_status != 'draft') {
            return API::createResponse(array('payment_status' => 'approved'), 200);
        } else {
            return API::createResponse(array('payment_status' => 'unknown'), 200);
        }
    }

    public function getDetail(PApp $app) {

        $orderDetail = array();
        
        $orderId         = Input::get('order_id');
        $customer_ref_id = Input::get('customer_ref_id');
        $customerType    = Input::get('customer_type');
        $ssoId           = Input::get('sso_id');
        $lang            = Input::get('lang');
        $base_url        = Input::get('base_url');


        // not login
        //if ($customerType == 'non-user') return API::createResponse($orderDetail, 404);

        $order = Order::with(array('messages', 'payment'))->whereId($orderId)->whereAppId($app->getKey())->where('customer_ref_id', $customer_ref_id)->firstOrFail();

        //check customer_ref_id
//        if ($order->customer_ref_id !== $customer_ref_id)
//            return API::createResponse($orderDetail, 404);

        $this->message->thanksForYourPayment($order);
        $this->message->setCustomerType($customerType);
        $this->message->send($lang, $base_url);

        // d($responseThankyou);
        // die();

        $barcode = array();
        // if payment channel == offline payment channel generate barcode
        if (strtolower(($order->payment_channel)) === Order::OFFLINE && $order->barcode != null) {

            $file_name = $order->barcode;
            $folder = date_format($order->created_at, 'Y-m-d');
            $write_path  = Config::get('up::uploader.baseDir');
            //$upload_barcode = 'uploads' . DIRECTORY_SEPARATOR . 'barcode' . DIRECTORY_SEPARATOR . $folder;
            $upload_barcode = $write_path . DIRECTORY_SEPARATOR . 'barcode-' . $folder . DIRECTORY_SEPARATOR;
            $image_format = '.jpg';

            // create folder by Y-m-d
            if (!is_dir($upload_barcode)) 
            {
                @mkdir($upload_barcode);
                @chmod($upload_barcode, 0777);
            }
            // path file barcode
            $upload_path = $upload_barcode . $file_name . $image_format;
            // generate barcode
            $barcode = new Barcode($file_name);
            $barcode->draw($upload_path);
        }

        // read path barcode
        if (strtolower(($order->payment_channel)) === Order::OFFLINE && $order->barcode != null) {
            $read_path = Config::get('up::uploader.baseUrl');
            $folder = date_format($order->created_at, 'Y-m-d');
            $barcode_path = $read_path . DIRECTORY_SEPARATOR . 'barcode-' . $folder . DIRECTORY_SEPARATOR . $order->barcode . '.jpg';
            $barcode = array('barcode' => $barcode_path);
        }


        $orderDetail = array(
            'order_id' => $order->order_id,
            'payment_order_id' => $order->payment_order_id,
            'payment_status' => $order->payment_status,
            'payment_channel' => strtolower($order->payment_channel),
            'payment_status' => strtolower($order->payment_status),
            'payment_method' => strtolower($order->payment_method),
            'payment_method_code' => strtolower($order->payment->code),
            'payment_method_name' => $order->payment->name,
            'customer_email' => $order->customer_email,
            'customer_tel' => $order->customer_tel,
            'ordered_date' => $order->created_at,
            'sub_total' => floatval($order->sub_total),
            'customer_name' => $order->customer_name,
            'customer_address' => $order->customer_address,
            'customer_district' => $order->customer_district,
            'customer_city' => $order->customer_city,
            'customer_province' => $order->customer_province,
            'customer_postcode' => $order->customer_postcode,
            'customer_tel' => $order->customer_tel,
            'ref1' => $order->ref1,
            'ref2' => $order->ref2,
            'ref3' => $order->ref3,
            'order_expired' => $order->payment_expired_at,
            'analytics_status' => $order->analytics_status
        );

        $view = array_merge($orderDetail, $barcode);
        return API::createResponse($view, 200);
    }

    public function getItem(PApp $app) {
        $orderItem = array();

        $orderId = Input::get('order_id');
        $customer_ref_id = Input::get('customer_ref_id');
        $customerType = Input::get('customer_type');
        $ssoId = Input::get('sso_id');

        $order = Order::with('city')->whereId($orderId)->whereAppId($app->getKey())->firstOrFail();

        $order_items = OrderShipmentItem::whereOrderId($orderId)->get();
        # d($order_items);
        $shop_name = NULL;
        foreach ($order_items as $order_item) {
            $orderItem['order_item'][] = array(
                'order_id' => $order_item->order_id,
                'inventory_id' => $order_item->inventory_id,
                'sku_code' => $order_item->sku_vendor,
                'name' => $order_item->name,
                'category' => $order_item->category,
                'brand' => $order_item->brand,
                'quantity' => $order_item->quantity,
                'price_per_unit' => $order_item->price,
                'total_price' => $order_item->total_price
            );
            $shop_name[] = $order_item->shop_id;
        }
        #d($order);
        $orderItem['order'] = array(
            'order_id' => $order->order_id,
            'customer_city' => !empty($order->city->name) ? $order->city->name : '',
            'sub_total' => floatval($order->sub_total),
            'shipping_fee' => floatval($order->total_shipping_fee),
            'customer_province' => $order->customer_province,
            'shop_name' => implode($shop_name, '/'),
            'country' => 'ประเทศไทย'
        );
        return API::createResponse($orderItem, 200);
    }

    public function postRequery(PApp $app) {
        $orderId = Input::get('order_id');

        try
        {
            // get order with where app_id
            $order = Order::whereId($orderId)->whereAppId($app->getKey())->firstOrFail();

            // if order_status is not draft, return
            // if ($order->payment_status != Order::PAYMENT_WAITING || $order->payment_channel != Order::ONLINE) {
            //     return API::createResponse(array('payment_status' => 'approved'), 200);
            // }

            $paymentRepo = App::make('PaymentRepositoryInterface');

            if ($paymentRepo->checkRequery($order))
            {
                return API::createResponse(array('payment_status' => 'approved'), 200);
            }
            return API::createResponse(array('payment_status' => 'unknown'), 200);
        }
        catch (Exception $e)
        {
            return API::createResponse($e->getMessage(), 500);
        }

        /*
        '<?xml version="1.0" encoding="UTF-8"?><request><app_id>040</app_id><app_password>pa22pcmsDev</app_password><ref3>6608</ref3></request>';
         * 
         */

        //success
        /*
        '<?xml version="1.0" encoding="UTF-8"?>
<paymentgateway>
    <payment cpgtransactionid="131111779513" transactionid="27300761" respcode="0" respdesc="Approved" payment_channel="CCW" amount="1.00" pay_date="11112013204807" channeltype="WEB" orderidwls="9330337" ref1="90000000" ref2="269793303372" ref3="21" ref4=""  loginuser="emailtesting54321@gmail.com" ssoid="4620761" approved_code="374359"/>
</paymentgateway>';
         * 
         */
    }

    public function anyJsonOrderItems($orderId) {
        $invoice_discount = PromotionCodeLog::with('promotion')->whereOrderId($orderId)->get()->toArray();
        $order_trans = OrderTransaction::with('orderShipmentItem.vendor')->with('Shipment.vendor')->whereOrderId($orderId)->get()->toArray();
        #d($order_trans);
        #exit();
        $real_invoice_item = array();

        if (!empty($order_trans)) {
            $inven_check = array();
            $total_discount = 0;
            $effect_parent = '';
            $effect = array();
            foreach ($order_trans as $item) {
                if (!empty($item['order_shipment_item'])) {
                    $real_invoice_item[] = array(
                        'item_type' => 'product',
                        'item_code' => $item['order_shipment_item']['inventory_id'],
                        'item_name' => $item['order_shipment_item']['name'],
                        'qty' => $item['order_shipment_item']['quantity'],
                        'price' => $item['order_shipment_item']['price'],
                        'shop_id' => $item['order_shipment_item']['shop_id'],
                        'vendor_code' => $item['order_shipment_item']['vendor_id'],
                        'vendor_type' => $item['order_shipment_item']['vendor']['stock_type'],
                        'discount' => $item['affected_discount'],
                        'effect_parent' => ''
                    );
                }

                if (!empty($item['shipment']) && empty($item['order_shipment_item'])) {
                    $real_invoice_item[] = array(
                        'item_type' => 'shipping',
                        'item_code' => '',
                        'item_name' => ($item['price'] == 0) ? 'Free Shipping' : 'Shipping',
                        'qty' => $item['qty'],
                        'price' => $item['price'],
                        'shop_id' => $item['shipment']['shop_id'],
                        'vendor_code' => $item['shipment']['vendor_id'],
                        'vendor_type' => $item['shipment']['vendor']['stock_type'],
                        'discount' => $item['affected_discount'],
                        'effect_parent' => ''
                    );
                }
                $total_discount += $item['affected_discount'];
                if (!empty($item['order_shipment_item'])) {
                    $effect[] = $item['order_shipment_item']['inventory_id'] . ',' . $item['affected_discount'];
                }
                $effect_parent = implode('|', $effect);
            }

            if (!empty($invoice_discount)) {
                foreach ($invoice_discount as $discount) {
                    $real_invoice_item[] = array(
                        'item_type' => 'discount',
                        'item_code' => '',
                        'item_name' => $discount['promotion']['name'],
                        'qty' => 1,
                        'price' => $total_discount,
                        'shop_id' => '',
                        'vendor_code' => '',
                        'vendor_type' => '',
                        'discount' => 0,
                        'discount_promotion_type' => $discount['promotion']['promotion_category'],
                        'discount_type_amount' => $discount['promotion']['effects']['discount']['type'],
                        'effect_parent' => $effect_parent
                    );
                }
            }
        }

        $order = Order::findOrFail($orderId)->toArray();
        $app_id = Config::get('receipt.receipt_app_id');
        $receipt_params = array(
            'app_key' => $app_id,
            'order_ref1' => $orderId,
            'order_ref2' => $order['ref2'],
            'order_item' => json_encode($real_invoice_item),
            'customer_name' => $order['customer_name'],
            'shipping_address' => $order['customer_address'] .
            ' ตำบล ' . $order['customer_district'] .
            ' อำเภอ ' . $order['customer_city'] .
            ' จังหวัด ' . $order['customer_province'] .
            ' ' . $order['customer_postcode']
            ,
            'total_price' => $order['sub_total'],
            'reconcile_date' => $order['sla_time_at']
        );
        return $receipt_params;
    }

}