<?php

namespace Wetrust;

use Teepluss\Api\Api;
use Illuminate\Config\Repository;
use Order;

class Wetrust {

    protected $config;
    protected $api;
    protected $appConfig;

    public function __construct(Repository $config, Api $api)
    {
        $this->config = $config;

        $this->api = $api;

        //sd($config);
    }

    // protected function buildXML(\Order $order)
    public function buildXML(\Order $order)
    {
        // sample_order
//		$xml = file_get_contents(app_path('libraries/Wetrust/sample_order.xml'));

        /* Order Data */
        $order->load('app', 'shipments', 'shipments.method', 'shipments.shipmentItems', 'payment');

        // $order = Order::with('app', 'shipments', 'shipments.method', 'shipments.shipmentItems')->find(43);
        // $this->c->calculateMargin($order);
        // die();


        /* Prepare Data */
        $data = array();
        $data['rowproduct'] = array(
            'shopid' => '',
            'items' => '',
        );

        // if installment
        if (strtolower($order->payment->code) === Order::COD)
        {
            $installment = json_decode($order->installment);
            $data['sof_channel'] = $order->payment->code.'#'.$installment->period;
        }
        else
        {
            $data['sof_channel'] = $order->payment->code; /* Payment Method */
        }

        $data['referenceId'] = $order->id; /* Order ID */
        $data['rowuser'] = array(
            'ssoid' => '',
            'trueid' => '',
            'fullname' => $order->customer_name,
            'address' => $order->customer_address,
            'district' => '',
            'province' => $order->customer_province,
            'zip' => $order->customer_postcode,
            'country' => '',
            'mphone' => $order->customer_tel,
            'citizenid' => '',
        );
        $data['billing'] = array(
            'fullname' => $order->customer_name,
            'address' => $order->customer_address,
            'district' => '',
            'province' => $order->customer_province,
            'zip' => $order->customer_postcode,
            'country' => ''
        );

//        $fgUrl = $order->app->foreground_url.'?'.http_build_query(array(
//                'method' => $order->payment->code,
//                'order_id' => $order->order_id,
//        ));
//
//        if (strtolower($order->payment->channel) === 'offline')
//        {
//            $fgUrl .= '&success=1';
//        }

        $endpoints = \Config::get('endpoints.itruemart');
        $fgUrl = $endpoints.'/checkout/complete';

        $data['extraparam'] = array(
            'response_url' => $fgUrl,
            'back_url' => $fgUrl,
            'note' => ''
        );

        // Use Config
        $config = $this->config->get('wetrust::config');
        $data['rowproduct']['shopid'] = $config['shopId'];

        $i = 0;
        if (!$order->shipments->isEmpty())
        {
            foreach ($order->shipments as $shipment)
            {
                if (!$shipment->shipmentItems->isEmpty())
                {
                    foreach ($shipment->shipmentItems as $item)
                    {
                        $topic = $item->brand.' : '.$item->name;

                        $itemTransaction = $item->orderTransactions;

                        $data['rowproduct']['items']["item{$i}"] = array(
                            'pid' => $item->inventory_id,
                            'productid' => $item->inventory_id,
                            'topic' => $topic,
                            'quantity' => $itemTransaction->qty,
                            'totalPrice' => $itemTransaction->customer_pay,
                            'shopidref' => $item->shop_id,
                            'margin_price' => $itemTransaction->margin,
                        );

                        // $data['rowproduct']['items']["item{$i}"] = array(
                        //     'pid' => $item->inventory_id,
                        //     'productid' => $item->inventory_id,
                        //     'topic' => $topic,
                        //     'quantity' => $item->quantity,
                        //     'totalPrice' => $item->price,
                        //     'shopidref' => $item->shop_id,
                        //     'margin_price' => floor($item->total_margin / $item->quantity),
                        // );

                        $i++;
                    }
//					d($shipment->shipping_method, $shipment->method); die();
//					d($shipment->method); die();
                    // installment then not send shipment
                    if (strtolower($order->payment->code) != \Order::INSTALLMENT_WETRUST_CODE)
                    {
                        $data['rowproduct']['items']["item{$i}"] = array(
                            'pid' => 'delivery',
                            'productid' => 'delivery',
                            //						'topic'        => \ShippingMethod::wherePkey($shipment->shipping_method)->first()->name,
                            'topic' => $shipment->method->name,
                            'quantity' => 1,
                            'totalPrice' => $shipment->shipping_fee,
                            'shopidref' => ($shipment->stock_type == 6) ? $shipment->shop_id : $order->app->stock_code,
                        );
                    }
                    else
                    {
                        $data['rowproduct']['items']["item0"]['totalPrice'] += $shipment->shipping_fee;
                    }

                    $i++;
                }
            }
        }

        return Format::factory($data)->toXML($data, NULL, 'request');
    }

    public function generateHTMLSubmitForm(\Order $order)
    {
        // get inner config
        $config = $this->config->get('wetrust::config');

        // define action URL
        $submitURL = $config['submitURL'];

        // define appId
        $appId = $config['appId'];

        // buildXML, encrpyt with RC4
        $xml = $this->buildXML($order);

        $xmlOrder = RC4::EncryptRC4($config['rc4key'], $xml);

        // $temp = new Tmp;
        // $temp->key = 'send wetrust xml';
        // $temp->value = json_encode($xml);
        // $temp->save();

        // define chkSum
        $chkSum = md5($xml.'|'.$config['appPassword'].'|'.$config['privateKey']);

        // build HTML
        $html = \View::make('wetrust::submit_form', compact('submitURL', 'appId', 'xmlOrder', 'chkSum'))->render();

        return $html;
    }

    public function generateSubmitData(\Order $order)
    {
        // get inner config
        $config = $this->config->get('wetrust::config');

        // define action URL
        $submitURL = $config['submitURL'];

        // define appId
        $appId = $config['appId'];

        // buildXML, encrpyt with RC4
        $xml = $this->buildXML($order);
        $xmlOrder = RC4::EncryptRC4($config['rc4key'], $xml);

        // define chkSum
        $chkSum = md5($xml.'|'.$config['appPassword'].'|'.$config['privateKey']);

        $data = array(
            'order_id' => $order->getKey(),
            'postUrl' => $submitURL,
            'app_id' => $appId,
            'xml_order' => $xmlOrder,
            'chkSum' => $chkSum
        );

        return $data;
    }

    public function BGCallbackResponse($raw)
    {
        // get inner config
        $config = $this->config->get('wetrust::config');

        // decrypt RC4 to Array
        $decrypted = RC4::DecryptRC4($config['rc4key'], $raw);
        $array = Format::factory($decrypted, 'xml')->toArray();

        return $array['payment']['@attributes'];
    }

    public function FGCallbackResponse($xml)
    {
        return Format::factory($xml, 'xml')->toArray();
    }

    public function requery($orderId)
    {
        // get inner config
        $config = $this->config->get('wetrust::config');

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'http://www.weloveshopping.com/wetrust/cpgrequery/rqRefChkCartorder');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '<?xml version="1.0" encoding="UTF-8"?><request><app_id>'.$config['appId'].'</app_id><app_password>'.$config['appPassword'].'</app_password><ref3>'.$orderId.'</ref3></request>');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/plain'));

        $xml = curl_exec($ch);

        // XML to Array
        $array = Format::factory($xml, 'xml')->toArray();

        return $array['payment']['@attributes'];
    }

    public function reconcile(\Order $order)
    {
        // get inner config
        $config = $this->config->get('wetrust::config');

        if ($order->payment_order_id == false)
            return array();

        $data = array(
            'apikey' => '1234',
            'p' => 1,
            'rate' => 'order',
            'orderid' => $order->payment_order_id,
            'showdelivery' => 'y',
        );

        $xml = $this->api->get($config['reconcileURL'].'?'.http_build_query($data));

        // XML to Array
        $array = Format::factory($xml, 'xml')->toArray();

        return $array;
    }

    public function process()
    {
        $config = $this->config->get('wetrust::config');

        return new Process($config);
    }

}

