<?php
ini_set('memory_limit','1024M');
set_time_limit(0);
//ini_set('display_errors', 1);

class MigrateOrderController extends BaseController {

    public function getOrder()
    {
        // $first = DB::table('raw_order_transaction')->where('migrate_status', 'no')->pluck('order_id');
        // echo 'start at order_id = ' . $first ;
        // echo '<br><br>';
        $raws = DB::table('raw_order_transaction')->where('migrate_status', 'no')->take(100)->get();

        foreach ($raws as $key => $val)
        {
            try
            {
                $order = new Order;

                // Auto
                // $order->id = ;
                // $order->pkey = ;

                // OK
                $order->app_id = 1;
                $order->order_ref = $val->order_ref;
                $order->ref1 = $val->ref_1;
                $order->ref2 = $val->ref_2;
                $order->ref3 = $val->ref_3;

                // Set payment_order_id = null (Temporarily)
                $order->payment_order_id = NULL;
                $order->barcode = $val->barcode;

                // Right ?
                $order->customer_ref_id = $val->member_ref_id;

                // OK
                $order->customer_name = $val->customer_name;
                $order->customer_address = $val->customer_address;
                $order->customer_district = $val->customer_district;
                $order->customer_district_id = (int) DB::table('districts')->where('name', '=', $val->customer_district)->pluck('id');
                $order->customer_city = $val->customer_city;
                $order->customer_city_id = (int)  DB::table('cities')->where('name', '=', $val->customer_city)->pluck('id');
                $provinceId = (int) DB::table('cities')->where('name', '=', $val->customer_city)->pluck('province_id');
                if ($provinceId > 0)
                {
                    $order->customer_province = DB::table('provinces')->where('id', $provinceId)->pluck('name');
                    $order->customer_province_id = $provinceId;
                }
                $order->customer_postcode = $val->customer_postcode;
                $order->customer_tel = $val->customer_tel;
                $order->customer_email = $val->customer_email;

                // Where ?
                $order->customer_info_modified_at = NULL;

                $order->payment_channel = $val->payment_channel;

                if ( !empty($val->payment_method_code) )
                {
                    if ( $val->payment_method_code == 'CCinstM#3' or $val->payment_method_code == 'CCinstM#6' )
                    {
                        $val->payment_method_code = 'CCinstM';
                    }

                    $order->payment_method = DB::table('payment_methods')->where('code', '=', $val->payment_method_code)->pluck('id');
                }

                $order->installment = $val->installment;
                $order->transaction_time = $val->transaction_time;
                $order->total_price = $val->total_price;

                // Calculate Later ?
                $order->discount = 0;

                // OK ?
                $order->discount_text = NULL;
                $order->total_shipping_fee = 0;

                // right ?
                $order->sub_total = $order->total_price - $order->discount;

                $orderStatus = array(
                    // 'delivering'      => Order::SHIPPING_SENDING,
                    'order-complete'  => Order::STATUS_NEW,
                    'waiting-payment' => Order::STATUS_WAITING,
                    // 'success'         => Order::STATUS_COMPLETE
                );

                if ( !empty($val->order_status) )
                {
                    if ( Str::slug($val->order_status) == 'delivering' or Str::slug($val->order_status) == 'success' )
                    {
                        continue;
                    }

                    $order->order_status = $orderStatus[Str::slug($val->order_status)];
                }

                $paymentStatus = array(
                    'success'         => Order::PAYMENT_RECONCILE,
                    'processing'      => Order::PAYMENT_WAITING,
                    'waiting-approve' => Order::PAYMENT_SUCCESS,
                    'failed'          => Order::PAYMENT_FAILED
                );

                if ( !empty($val->payment_status) )
                {
                    $order->payment_status = $paymentStatus[Str::slug($val->payment_status)];
                }

                // ???
                $order->gift_status = NULL;
                $order->sla_time_at = NULL;
                $order->customer_status = NULL;
                $order->customer_sla_time_at = NULL;

                // ???
                $order->expired_at = $val->expired_at;
                $order->invoice = $val->invoice;
                // Default
                $order->analytics_status = ( empty($val->analytic_status) ) ? 'N' : $val->analytic_status ;
                $order->created_at = ( empty($val->created_at) ) ? date('Y-m-d H:i:s') : $val->created_at ;
                $order->updated_at = ( empty($val->updated_at) ) ? date('Y-m-d H:i:s') : $val->updated_at ;
                $order->deleted_at = $val->deleted_at;

                $order->save();

                // Mapping
                DB::table('order_maps')->insert(
                    array(
                        'itruemart_id' => $val->order_id,
                        'pcms_id' => $order->id,
                        'pkey' => $order->pkey,
                    )
                );

                // In this migration data, 1 order has only one shipments,
                // create shipment for this order
                $orderShipment = new OrderShipment;
                $orderShipment->order_id = $order->id;

                // null ?
                $orderShipment->shipment_ref = '';

                // Fix !
                $orderShipment->shipping_method = ShippingMethod::where('name', '=', 'iTruemart Free Shipping')->pluck('id');
                $orderShipment->shipping_fee = 0;

                // Fix ?
                $orderShipment->shipment_status = 'delivered';

                // We'll calculate this later.
                $orderShipment->total_price = 0;

                $orderShipment->stock_type = NULL;
                $orderShipment->vendor_id = NULL;
                $orderShipment->shop_id = NULL;
                $orderShipment->tracking_number = NULL;
                $orderShipment->sla_time_at = NULL;
                $orderShipment->created_at = $val->created_at;
                $orderShipment->updated_at = $val->updated_at;
                $orderShipment->deleted_at = $val->deleted_at;

                $orderShipment->save();



                $rawItems = DB::table('raw_order_item')->where('order_id', $val->order_id)->get();

                foreach ($rawItems as $k => $item)
                {
                    try
                    {
                        if ( empty($item->order_id) or empty($item->inventory_id) or empty($item->name) or empty($item->quantity) or empty($item->price) )
                        {
                            continue;
                        }

                        // $osi is mean '$orderShipmentItem'
                        $osi = new OrderShipmentItem;
                        $osi->shipment_id = $orderShipment->id;
                        $osi->order_id = $order->id;
                        $osi->material_code = $item->material_code;
                        $osi->inventory_id = $item->inventory_id;
                        $osi->name = $item->name;
                        // $osi->category = ;
                        // $osi->brand = ;
                        $osi->quantity = $item->quantity;
                        // $osi->price = $item->price;
                        // $osi->margin = $item->margin;
                        // $osi->discount = $item->discount;
                        // $osi->total_price = $item->total_price;

                        // itruemart logic ??? WTF !?
                        $osi->price = $item->total_price;
                        $osi->total_price = $osi->price * $osi->quantity;
                        $osi->margin = $item->margin;
                        $osi->total_margin = $item->total_margin;
                        $osi->discount = $item->discount;

                        $osi->vendor_id = (int) $item->vendor_id;
                        $osi->shop_id = (int) $item->shop_id;
                        $osi->options = $item->options;
                        // default
                        // $osi->item_status = ;
                        // $osi->is_gift_item = ;
                        $osi->tracking_number = $item->tracking_number;
                        $osi->created_at = $item->created_at;
                        $osi->updated_at = $item->updated_at;
                        $osi->deleted_at = $item->deleted_at;

                        $osi->save();

                        DB::table('raw_order_item')->where('id', $item->id)->update( array('migrate_status' => 'yes') );
                    }
                    catch (Exception $e)
                    {
                        d($e);
                        DB::table('raw_order_item')->where('id', $item->id)->update( array('migrate_status' => 'error') );
                        die();
                    }
                }

                DB::table('raw_order_transaction')->where('id', $val->id)->update( array('migrate_status' => 'yes') );
            }
            catch (Exception $e)
            {
                d($e);
                DB::table('raw_order_transaction')->where('id', $val->id)->update( array('migrate_status' => 'error') );
                die();
            }
        }

        $count = count($raws);
        echo "migrate complete. ( {$count} rows )";

        echo '<script>';
        echo 'var delay = 5000;';
        echo 'setTimeout(function(){';
        echo 'window.location.reload();';
        echo '}, delay);';
        echo '</script>';
    }







    public function getImportOrderItem()
    {
        $number = Input::get('file', 1);
        $pathFile = "./20140515-excel-migrate-itruemart/order_item_{$number}.xlsx";
        $objPHPExcel = PHPExcel_IOFactory::load($pathFile);

        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
        unset($sheetData[1]);

        // $records = array();
        foreach ($sheetData as $v)
        {
            // $records[] = array(
            $records = array(
                'order_id'       => $v['A'],
                'inventory_id'   => $v['B'],
                'material_code'  => $v['C'],
                'name'           => $v['D'],
                'quantity'       => $v['E'],
                'price'          => $v['F'],
                'margin'         => $v['G'],
                'discount'       => $v['H'],
                'total_price'    => $v['I'],
                'total_margin'   => $v['J'],
                'vendor_id'      => $v['K'],
                'vendor_name'    => $v['L'],
                'stock_type'     => $v['M'],
                'shop_id'        => $v['N'],
                'options'        => $v['O'],
                'tracking_number' => $v['P'],
                'created_at'     => $v['Q'],
                'updated_at'     => $v['R'],
                'deleted_at'     => $v['S'],
            );

            DB::table('raw_order_item')->insert($records);
        }

        // DB::table('raw_order_item')->insert($records);

        echo "Import Data from Excel - Complete";
        echo "<br> Path file - {$pathFile}";
    }

    public function getImportOrderTransaction()
    {
        $number = Input::get('file', 1);
        $pathFile = "./20140515-excel-migrate-itruemart/order_transaction_{$number}.xlsx";
        $objPHPExcel = PHPExcel_IOFactory::load($pathFile);

        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
        unset($sheetData[1]);

        foreach ($sheetData as $v)
        {
            $records = array(
                'order_id'            => $v['A'],
                'order_ref'           => $v['B'],
                'ref_1'               => $v['C'],
                'ref_2'               => $v['D'],
                'ref_3'               => $v['E'],
                'payment_order_id'    => $v['F'],
                'barcode'             => $v['G'],
                'member_ref_id'       => $v['H'],
                'customer_name'       => $v['I'],
                'customer_address'    => $v['J'],
                'customer_district'   => $v['K'],
                'customer_city'       => $v['L'],
                'customer_postcode'   => $v['M'],
                'customer_tel'        => $v['N'],
                'customer_email'      => $v['O'],
                'payment_channel'     => $v['P'],
                'payment_method_code' => $v['Q'],
                'installment'         => $v['R'],
                'transaction_time'    => $v['S'],
                'total_price'         => $v['T'],
                'order_status'        => $v['U'],
                'payment_status'      => $v['V'],
                'expired_at'          => $v['W'],
                'invoice'             => $v['X'],
                'created_at'          => $v['Y'],
                'updated_at'          => $v['Z'],
                'deleted_at'          => $v['AA'],
                'analytic_status'     => $v['AB'],
                'email_confirm'       => $v['AC'],
                'cpg_transaction_id'  => $v['AD'],
            );

            DB::table('raw_order_transaction')->insert($records);
        }

        echo "Import Data from Excel - Complete";
        echo "<br> Path file - {$pathFile}";
    }

}