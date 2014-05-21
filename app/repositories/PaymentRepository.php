<?php

class PaymentRepository implements PaymentRepositoryInterface
{

    public function __construct()
    {
        
    }

    public function checkRequery(Order $order)
    {
        $wetrust = App::make('wetrust');
        $wetrustData = $wetrust->requery($order->id);

        if ($wetrustData['respdesc'] === 'Approved')
        {
            $reconcileData = array(
                'ref1' => array_get($wetrustData, 'ref1'),
                'ref2' => array_get($wetrustData, 'ref2'),
                'ref3' => array_get($wetrustData, 'ref3'),
                'payment_order_id' => array_get($wetrustData, 'orderidwls'),
                'transaction_time' => array_get($wetrustData, 'pay_date')
            );

            $paymentRepo = App::make('PaymentRepositoryInterface');
            $paymentRepo->saveReconcile($order, $reconcileData);

            return true;
        }

        return false;
    }

    public function checkReconcile(Order $order)
    {
        $wetrust = App::make('wetrust');
        $payment = $wetrust->reconcile($order);

        $log = new OrderReconcileLog;
        $log->order_id = $order->getKey();
        $log->data = json_encode($payment);
        $log->save();

        if (array_get($payment, 'results.0.pay') === 'r')
        {
            $reconcileData = array(
                'ref3' => $order->ref3,
                'payment_order_id' => array_get($payment, 'results.0.orderid'),
                'transaction_time' => array_get($payment, 'results.0.cpg_reconcile_date')
            );

            $this->saveReconcile($order, $reconcileData);
        }
    }

    public function saveReconcile(Order $order, $reconcileData = array())
    {
        $orderIdWLS = $reconcileData['payment_order_id'];
        $data = array(
            'ref1' => array_get($reconcileData, 'ref1', $order->ref1),
            'ref2' => array_get($reconcileData, 'ref2', $order->ref2),
            'ref3' => $reconcileData['ref3'],
            'transaction_time' => array_get($reconcileData, 'transaction_time')?:date('Y-m-d H:i:s'),
            'payment_order_id' => $orderIdWLS,
            'payment_status' => Order::PAYMENT_RECONCILE,
        );
        $orderRepo = App::make('OrderRepositoryInterface');

        // if current order status = waiting
        if (strtolower($order->order_status) === Order::STATUS_WAITING)
        {
            // set order status = new
            $data['order_status'] = Order::STATUS_NEW;
            $orderRepo->update($order, $data);

            try
            {


                // customer invoice
                $receipt_api_url = Config::get('receipt.receipt_url');
                $receipt_params = $this->anyJsonOrderItems($order);

                //$curl = new Curl;
                //$receipt_response = $curl->simple_post($receipt_api_url, $receipt_params);
                $receipt_response = execcurlurl($receipt_api_url, $receipt_params, 'post', false, 120);
               
                
            } catch (Exception $e)
            {
                $error = new Tmp;
                $error->key = 'receipt error';
                $error->value = $e->getMessage();
                $error->save();
            }

  
            // Cut Stock
            $stockRepo = App::make('StockRepositoryInterface');
            $cutStock = $stockRepo->cutStock($order, $orderIdWLS);
        } else
        {
            $orderRepo->update($order, $data);
        }
    }

    public function anyJsonOrderItems($order)
    {
        $invoice_discount = PromotionCodeLog::with('promotion')->whereOrderId($order->id)->get()->toArray();
        $order_trans = OrderTransaction::with('orderShipmentItem.vendor')->with('Shipment.vendor')->whereOrderId($order->id)->get()->toArray();

        $real_invoice_item = array();

        if (!empty($order_trans))
        {

            $inven_check = array();
            $total_discount = 0;
            $effect_parent = '';
            $effect = array();
           
            foreach ($order_trans as $item)
            {
                if (!empty($item['order_shipment_item']))
                {
                    $options_item = '';
                    $new_option = array();
                    $options = array();
                    
                    if (!empty($item['order_shipment_item']['options']))
                    {
                        $options = $item['order_shipment_item']['options'];

                        if (!empty($options['color']) || !empty($options['size']))
                        {
                            foreach ($options as $key => $option)
                            {
                                if ($key == 'color' && !empty($option))
                                {
                                    $new_option[] = 'สี :' . $option;
                                }
                                if ($key == 'size' && !empty($option))
                                {
                                    $new_option[] = 'ขนาด :' . $option;
                                }
                            }

                            $options_item = '(' . implode(',', $new_option) . ')';
                        }
                    }

                    $real_invoice_item[] = array(
                        'item_type' => 'product',
                        'item_code' => $item['order_shipment_item']['inventory_id'],
                        'item_name' => $item['order_shipment_item']['name'] . ' ' . $options_item,
                        'qty' => $item['order_shipment_item']['quantity'],
                        'price' => $item['order_shipment_item']['price'],
                        'shop_id' => $item['order_shipment_item']['shop_id'],
                        'vendor_code' => $item['order_shipment_item']['vendor_id'],
                        'vendor_type' => $item['order_shipment_item']['vendor']['stock_type'],
                        'discount' => $item['affected_discount'],
                        'effect_parent' => ''
                    );
                }

                if (!empty($item['shipment']) && empty($item['order_shipment_item']))
                {
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
                if (!empty($item['order_shipment_item']))
                {
                    $effect[] = $item['order_shipment_item']['inventory_id'] . ',' . $item['affected_discount'];
                }
                $effect_parent = implode('|', $effect);
            }

            if (!empty($invoice_discount))
            {
                foreach ($invoice_discount as $discount)
                {
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
        #d($real_invoice_item);
        //$order = Order::findOrFail($orderId)->toArray();
        $app_id = Config::get('receipt.receipt_app_id');
        $receipt_params = array(
            'app_key' => $app_id,
            'order_ref1' => $order->id,
            'order_ref2' => $order->ref1,
            'order_item' => json_encode($real_invoice_item),
            'customer_name' => $order->customer_name,
            'shipping_address' => $order->customer_address .
            ' ตำบล ' . $order->customer_district .
            ' อำเภอ ' . $order->customer_city .
            ' จังหวัด ' . $order->customer_province .
            ' ' . $order->customer_postcode,
            'total_price' => $order->sub_total,
            'reconcile_date' => $order->transaction_time
        );

        return $receipt_params;
    }

    /*
      public function getPaymentData()
      {
      $order = Order::with('app', 'shipments', 'shipments.shipmentItems')->find(43);

      // d($order->toArray());

      $data = array();

      }
     */
    // public function buildXmlData(Order $order)
    // {
    // 	/* Order Data */
    // 	$order->load('app', 'shipments', 'shipments.method', 'shipments.shipmentItems', 'payment');
    // 	$order = Order::with('app', 'shipments', 'shipments.method', 'shipments.shipmentItems')->find(43);
    // 	$this->c->calculateMargin($order);
    // 	die();
    // 	/* Prepare Data */
    // 	$data = array();
    // 	$data['rowproduct'] = array(
    // 		'shopid' => '',
    // 		'items'  => '',
    // 	);
    // 	$data['sof_channel'] = $order->payment->code; /* Payment Method */
    // 	$data['referenceId'] = $order->id; /* Order ID */
    // 	$data['rowuser'] = array(
    // 		'ssoid'     => '',
    // 		'trueid'    => '',
    // 		'fullname'  => $order->customer_name,
    // 		'address'   => $order->customer_address,
    // 		'district'  => '',
    // 		'province'  => $order->customer_province,
    // 		'zip'       => $order->customer_postcode,
    // 		'country'   => '',
    // 		'mphone'    => $order->customer_tel,
    // 		'citizenid' => '',
    // 	);
    // 	$data['billing'] = array(
    // 		'fullname' => $order->customer_name,
    // 		'address'  => $order->customer_address,
    // 		'district' => '',
    // 		'province' => $order->customer_province,
    // 		'zip'      => $order->customer_postcode,
    // 		'country'  => ''
    // 	);
    // 	$data['extraparam'] = array(
    // 		'response_url' => 'https://pcms.igetapp.com/payment/fg-response',
    // 		'back_url'     => 'https://pcms.igetapp.com/payment/fg-response',
    // 		'note'         => ''
    // 	);
    // 	// $data['rowproduct']['shopid'] = $order->app->stock_code;
    // 	// Use Config
    // 	$data['rowproduct']['shopid'] = '320697';
    // 	$i = 0;
    // 	if ( !$order->shipments->isEmpty() )
    // 	{
    // 		foreach ($order->shipments as $shipment)
    // 		{
    // 			if ( !$shipment->shipmentItems->isEmpty() )
    // 			{
    // 				foreach ($shipment->shipmentItems as $item)
    // 				{
    // 					$topic = $item->brand . ' : ' . $item->name;
    // 					$data['rowproduct']['items']["item{$i}"] = array(
    // 						'pid'          => $item->inventory_id,
    // 						'productid'    => $item->inventory_id,
    // 						'topic'        => $topic,
    // 						'quantity'     => $item->quantity,
    // 						'totalPrice'   => $item->price,
    // 						'shopidref'    => $item->shop_id,
    // 						'margin_price' => floor($item->total_margin / $item->quantity),
    // 					);
    // 					$i++;
    // 				}
    // 				// d($shipment->shipping_method, $shipment->method); die();
    // 				$data['rowproduct']['items']["item{$i}"] = array(
    // 					'pid'          => 'delivery',
    // 					'productid'    => 'delivery',
    // 					'topic'        => $shipment->method->name,
    // 					'quantity'     => 1,
    // 					'totalPrice'   => $shipment->shipping_fee,
    // 					'shopidref'    => ($shipment->stock_type == 6) ? $shipment->shop_id : $order->app->stock_code ,
    // 				);
    // 				$i++;
    // 			}
    // 		}
    // 	}
    // 	return Wetrust\Format::factory($data)->toXML($data, NULL, 'request');
    // }
}