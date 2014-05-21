<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title></title>
</head>
<body>
<table style="border: solid 1px #22cc2f; border-radius: 5px; padding: 10px; font-size: 12px"
    width="698">
    <tr>
        <td colspan="2" style="font-family: arial, sans-serif; margin: 0px;">
            เรียน คุณ <?php echo $order->customer_name; ?>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="font-family: arial, sans-serif; margin: 0px;">
            &nbsp;
        </td>
    </tr>
    <tr>
        
        <?php if (strtolower($order->payment->code) === 'atm'){ ?>
        <td colspan="2" style="font-family: arial, sans-serif; margin: 0px;">
            <span style="padding-left: 5em;">คุณได้ทำการสั่งซื้อสินค้า <strong style="color: #ff9900">
                หมายเลขการสั่งซื้อ <?php echo $order->payment_order_id; ?>(<?php echo $order->order_id; ?>)</strong> และชำระเงินด้วย <strong style="color: #ff9900">
                    <?php echo $paymentMethodThai; ?>(<?php echo $paymentMethod; ?>)</strong> ตามรายละเอียดข้างล่าง กรุณาชำระเงิน 
                เพื่อที่ร้านค้าจะได้ดำเนินการจัดส่งสินค้าให้แก่คุณ 
                <!-- โดยท่านสามารถตรวจสอบสถานะของรายการสั่งซื้อนี้
                กรุณา<span class="Apple-converted-space">&nbsp;</span><a href="<?php //echo $link; ?>"
                    style="color: rgb(17, 85, 204);" target="_blank">คลิกที่นี่</a></span> -->
            หรือสั่งพิมพ์รายละเอียดการชำระเงิน <a href="<?php echo $thank_link; ?>">คลิกที่นี่</a>
        </td>

        <?php }else if (strtolower($order->payment->code) === 'banktrans'){ ?>
        <td colspan="2" style="font-family: arial, sans-serif; margin: 0px;">
            <span style="padding-left: 5em;">คุณได้ทำการสั่งซื้อสินค้า <strong style="color: #ff9900">
                หมายเลขการสั่งซื้อ <?php echo $order->payment_order_id; ?>(<?php echo $order->order_id; ?>)</strong> และชำระเงินด้วย <strong style="color: #ff9900">
                    <?php echo $paymentMethodThai; ?>(<?php echo $paymentMethod; ?>)</strong> ตามรายละเอียดข้างล่าง ซึ่งอยู่ในระหว่างรอการยืนยันการชำระเงินจากธนาคาร
                เพื่อที่ร้านค้าจะได้ดำเนินการจัดส่งสินค้าให้แก่คุณ 
                <!-- โดยท่านสามารถตรวจสอบสถานะของรายการสั่งซื้อนี้
                กรุณา<span class="Apple-converted-space">&nbsp;</span><a href="<?php //echo $link; ?>"
                    style="color: rgb(17, 85, 204);" target="_blank">คลิกที่นี่</a></span> -->
            หรือสั่งพิมพ์รายละเอียดการชำระเงิน <a href="<?php echo $thank_link; ?>">คลิกที่นี่</a>
        </td>

        <?php }else if (strtolower($order->payment->code) === 'ccw'){ ?>
        <td colspan="2" style="font-family: arial, sans-serif; margin: 0px;">
            <span style="padding-left: 5em;">คุณได้ทำการสั่งซื้อสินค้า <strong style="color: #ff9900">
                หมายเลขการสั่งซื้อ <?php echo $order->payment_order_id; ?>(<?php echo $order->order_id; ?>)</strong> และชำระเงินด้วย <strong style="color: #ff9900">
                    <?php echo $paymentMethodThai; ?>(<?php echo $paymentMethod; ?>)</strong> ตามรายละเอียดข้างล่าง ซึ่งอยู่ในระหว่างรอการยืนยันการชำระเงินจากธนาคาร
                เพื่อที่ร้านค้าจะได้ดำเนินการจัดส่งสินค้าให้แก่คุณ 
                <!-- โดยท่านสามารถตรวจสอบสถานะของรายการสั่งซื้อนี้
                กรุณา<span class="Apple-converted-space">&nbsp;</span><a href="<?php //echo $link; ?>"
                    style="color: rgb(17, 85, 204);" target="_blank">คลิกที่นี่</a></span> -->
            หรือสั่งพิมพ์รายละเอียดการชำระเงิน <a href="<?php echo $thank_link; ?>">คลิกที่นี่</a>
        </td>

        <?php }else if (strtolower($order->payment->code) === 'cs'){ ?>
        <td colspan="2" style="font-family: arial, sans-serif; margin: 0px;">
            <span style="padding-left: 5em;">คุณได้ทำการสั่งซื้อสินค้า <strong style="color: #ff9900">
                หมายเลขการสั่งซื้อ <?php echo $order->payment_order_id; ?>(<?php echo $order->order_id; ?>)</strong> และชำระเงินด้วย <strong style="color: #ff9900">
                    <?php echo $paymentMethodThai; ?>(<?php echo $paymentMethod; ?>)</strong> ตามรายละเอียดข้างล่าง กรุณาชำระเงิน 
                เพื่อที่ร้านค้าจะได้ดำเนินการจัดส่งสินค้าให้แก่คุณ 
                <!-- โดยท่านสามารถตรวจสอบสถานะของรายการสั่งซื้อนี้
                กรุณา<span class="Apple-converted-space">&nbsp;</span><a href="<?php //echo $link; ?>"
                    style="color: rgb(17, 85, 204);" target="_blank">คลิกที่นี่</a></span> -->
            หรือสั่งพิมพ์รายละเอียดการชำระเงิน <a href="<?php echo $thank_link; ?>">คลิกที่นี่</a>
        </td>

        <?php } else if (strtolower($order->payment->code) === 'ibank'){ ?>
        <td colspan="2" style="font-family: arial, sans-serif; margin: 0px;">
            <span style="padding-left: 5em;">คุณได้ทำการสั่งซื้อสินค้า <strong style="color: #ff9900">
                หมายเลขการสั่งซื้อ <?php echo $order->payment_order_id; ?>(<?php echo $order->order_id; ?>)</strong> และชำระเงินด้วย <strong style="color: #ff9900">
                    <?php echo $paymentMethodThai; ?>(<?php echo $paymentMethod; ?>)</strong> ตามรายละเอียดข้างล่าง กรุณาชำระเงิน 
                เพื่อที่ร้านค้าจะได้ดำเนินการจัดส่งสินค้าให้แก่คุณ 
                <!-- โดยท่านสามารถตรวจสอบสถานะของรายการสั่งซื้อนี้
                กรุณา<span class="Apple-converted-space">&nbsp;</span><a href="<?php //echo $link; ?>"
                    style="color: rgb(17, 85, 204);" target="_blank">คลิกที่นี่</a></span> -->
            หรือสั่งพิมพ์รายละเอียดการชำระเงิน <a href="<?php echo $thank_link; ?>">คลิกที่นี่</a>
        </td>

        <?php }else{ ?>
        <td colspan="2" style="font-family: arial, sans-serif; margin: 0px;">
            <span style="padding-left: 5em;">คุณได้ทำการสั่งซื้อสินค้า <strong style="color: #ff9900">
                หมายเลขการสั่งซื้อ <?php echo $order->payment_order_id; ?>(<?php echo $order->order_id; ?>)</strong> และชำระเงินด้วย <strong style="color: #ff9900">
                    <?php echo $paymentMethodThai; ?>(<?php echo $paymentMethod; ?>)</strong> ตามรายละเอียดข้างล่าง กรุณาชำระเงิน 
                เพื่อที่ร้านค้าจะได้ดำเนินการจัดส่งสินค้าให้แก่คุณ 
                <!-- โดยท่านสามารถตรวจสอบสถานะของรายการสั่งซื้อนี้
                กรุณา<span class="Apple-converted-space">&nbsp;</span><a href="<?php //echo $link; ?>"
                    style="color: rgb(17, 85, 204);" target="_blank">คลิกที่นี่</a></span> -->
            หรือสั่งพิมพ์รายละเอียดการชำระเงิน <a href="<?php echo $thank_link; ?>">คลิกที่นี่</a>
        </td> 
        <?php } ?>

    </tr>
    <tr>
        <td colspan="2" style="font-family: arial, sans-serif; margin: 0px;">
            &nbsp;
        </td>
    </tr>
    <tr>
        <td style="font-family: arial, sans-serif; margin: 0px;" width="100" align="center">
            <img src="<?php echo Config::get('email_template.front_url'); ?>themes/itruemart/assets/images/itruemart-logo.jpg" height="66">
            <?php //echo HTML::image('/themes/admin/assets/images/itruemart-logo.jpg', 'itruemart', array('height'=>'66')); ?>
        </td>
        <td style="font-family: arial, sans-serif; margin: 0px;">
            <div style="margin-left: 5px;">
                <table>
                    <tr>
                        <th style="font-family: arial, sans-serif; margin: 0px;" width="120" align="right">
                            <span>ชื่อผู้สั่งซื้อสินค้า :</span>
                        </th>
                        <td style="font-family: arial, sans-serif; margin: 0px;">
                            <span><?php echo $order->customer_name; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th style="font-family: arial, sans-serif; margin: 0px;" align="right">
                            <span>เบอร์ติดต่อ :</span>
                        </th>
                        <td style="font-family: arial, sans-serif; margin: 0px;">
                            <span><?php echo $order->customer_tel; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th style="font-family: arial, sans-serif; margin: 0px;" align="right">
                            <span>อีเมล :</span>
                        </th>
                        <td style="font-family: arial, sans-serif; margin: 0px;">
                            <span><a href="mailto:somyot_pon@truecorp.co.th" style="color: rgb(17, 85, 204);"
                                target="_blank"><?php echo $order->customer_email; ?></a></span>
                        </td>
                    </tr>
                    <tr>
                        <th style="font-family: arial, sans-serif; margin: 0px;" align="right">
                            <span>ที่อยู่ในการจัดส่ง :</span>
                        </th>
                        <td style="font-family: arial, sans-serif; margin: 0px;">
                            <span><?php echo $order->customer_address; ?> <?php echo $order->customer_district; ?> <?php echo $order->customer_city; ?> <?php echo $order->customer_province; ?>  <?php echo $order->customer_postcode; ?></span>
                        </td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="font-family: arial, sans-serif; margin: 0px;">
            &nbsp;
        </td>
    </tr>
    <tr>
        <td colspan="2" style="font-family: arial, sans-serif; margin: 0px;">
            &nbsp;
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <table>
                <tr>
                    <th style="font-family: arial, sans-serif; margin: 0px;" align="right">
                        <span>เลขที่การสั่งซื้อ(Order Number) :</span>
                    </th>
                    <td style="font-family: arial, sans-serif; margin: 0px;" width="250">
                        <span style="margin-left: 10px;"><?php echo $order->payment_order_id;?>(<?php echo $order->order_id;?>)</span>
                    </td>
                </tr>

                <?php if (strtolower($order->payment->code) !== 'cod') : ?>
                <tr>
                    <th style="font-family: arial, sans-serif; margin: 0px;" align="right">
                        <span>Ref No. 1 :</span>
                    </th>
                    <td style="font-family: arial, sans-serif; margin: 0px;" width="250"> 
                        <span style="margin-left: 10px;"><?php echo $order->ref1; ?></span>
                    </td>
                </tr>
                <tr>
                    <th style="font-family: arial, sans-serif; margin: 0px;" align="right">
                        <span>Ref No. 2 :</span>
                    </th>
                    <td style="font-family: arial, sans-serif; margin: 0px;" width="250"> 
                        <span style="margin-left: 10px;"><?php echo $order->ref2; ?></span>
                    </td>
                </tr>
                <?php endif; ?>

                <tr>
                    <th style="font-family: arial, sans-serif; margin: 0px;" align="right">
                        <span>วันที่สั่งซื้อ (Order Date) :</span>
                    </th>
                    <td style="font-family: arial, sans-serif; margin: 0px;">
                        <span style="margin-left: 10px;"><?php echo $order->created_at;?></span>
                    </td>
                </tr>
                <tr>
                    <th style="font-family: arial, sans-serif; margin: 0px;" align="right">
                        <span>ช่องทางการชำระเงิน :</span>
                    </th>
                    <td style="font-family: arial, sans-serif; margin: 0px;">
                        <span style="margin-left: 10px;"><?php echo $paymentMethodThai; ?>(<?php echo $paymentMethod; ?>)</span>
                    </td>
                </tr>
                <tr>
                    <th style="font-family: arial, sans-serif; margin: 0px;" align="right">
                        <span>สถานะการชำระเงิน :</span>
                    </th>
                    <td style="font-family: arial, sans-serif; margin: 0px;">
                        <span style="margin-left: 10px;"><?php echo $payment_status;?></span>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="font-family: arial, sans-serif; margin: 0px;">
            &nbsp;
        </td>
    </tr>
    <tr>
        <td colspan="2" style="font-family: arial, sans-serif; margin: 0px;">
            &nbsp;
        </td>
    </tr>
    <?php
    foreach ($order->shipments as $shipment) :
        $shipmentItems = $shipment->shipmentItems;
    ?>

    <tr>
        <td colspan="2" style="font-family: arial, sans-serif; margin: 0px;">
            <table cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 10px">
                <thead>
                    <tr>
                        <td colspan="4" bgcolor="#CDCB30" align="left" style="padding: 10px 0 10px 10px; color: #000">
                            สินค้าของร้าน <strong style="color: #000;">
                            <?php
                            $vendor = VVendor::find($shipment->vendor_id);
                            $shop = Shop::find($vendor->shop_id);
                            echo $shop->name;
                            ?>
                            (<?php echo $vendor->name; ?>) 
                            </strong> (<?php echo count($shipment);?> ชิ้น)
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" align="center" bgcolor="#CDCB30">
                            <div style="border-top: solid 1px #fff; height: 1px; line-height: 1px; margin: 0 10px">
                            </div>
                        </td>
                    </tr>
                    <tr align="center" bgcolor="#CDCB30" height="30">
                        <td style="font-family: arial, sans-serif; margin: 0px; color: #000;">
                            รายการ
                        </td>
                        <td style="font-family: arial, sans-serif; margin: 0px; color: #000;" width="90">
                            ราคา/หน่วย
                        </td>
                        <td style="font-family: arial, sans-serif; margin: 0px; color: #000;" width="50">
                            จำนวน
                        </td>
                        <td style="font-family: arial, sans-serif; margin: 0px; color: #000;" width="90">
                            รวม (฿)
                        </td>
                    </tr>
                </thead>
                <?php

                $discount = 0;
                foreach ($shipmentItems as $item):
                $variant = ProductVariant::where('inventory_id', $item->inventory_id)->first();
                //d($variant, $variant->product, $variant->product->image);
                //$thumb = $item->variant->product->image;
                ?>

                <tr>
                    <td style="font-family: arial, sans-serif; margin: 0px; padding-top: 5px; padding-bottom: 5px;">
                        <img src="<?php echo $variant->image; ?>"
                            width="100px" style="float: left; margin: 5px 20px 5px 5px" />
                        <p style="overflow: hidden;">
                            รหัสสินค้า: <?php echo $item->material_code . '/' . $item->inventory_id;?><br />
                            <?php echo $item->name; ?><span class="Apple-converted-space">&nbsp;</span><br />
                            <!-- <span style="color: gray;">(color : '.$item->options['color'].')</span> --> </p>
                    </td>
                    <td align="center" style="font-family: arial, sans-serif; margin: 0px;">
                        <?php echo number_format($item->total_price,2); ?>
                    </td>
                    <td align="center" style="font-family: arial, sans-serif; margin: 0px;">
                        <?php echo $item->quantity; ?>
                    </td>
                    <td align="center" style="font-family: arial, sans-serif; margin: 0px;">
                        <?php echo number_format($item->total_price,2); ?>
                    </td>
                </tr>
                <?php
                $discount = $discount + $item->discount;
                endforeach;?>
                
                <tr bgcolor="#dddddd">
                    <th colspan="3" style="font-family: arial, sans-serif; margin: 0px; padding: 5px" align="right">
                        วิธีการจัดส่ง :
                    </th>
                    <td align="center" style="font-family: arial, sans-serif; margin: 0px;">
                        <?php echo $shipment->method->name; ?>(<?php echo $shipment->method->description; ?>)
                    </td>
                </tr>
                <tr bgcolor="#dddddd">
                    <th colspan="3" style="font-family: arial, sans-serif; margin: 0px; padding: 5px" align="right">
                        ค่าบริการจัดส่ง :
                    </th>
                    <td align="center" style="font-family: arial, sans-serif; margin: 0px;">
                        <?php
                            $shipping_fee = $shipment->shipping_fee;
                            echo number_format($shipping_fee,2);
                        ?>
                    </td>
                </tr>
                <tr bgcolor="#dddddd">
                    <th colspan="3" style="font-family: arial, sans-serif; margin: 0px; padding: 5px"
                        align="right">
                        ส่วนลด
                    </th>
                    <td align="center" style="font-family: arial, sans-serif; margin: 0px;">
                        <?php echo number_format($discount,2); ?>
                    </td>
                </tr>
                <tr bgcolor="#dddddd">
                    <th colspan="3" style="font-family: arial, sans-serif; margin: 0px; padding: 5px"
                        align="right">
                       ราคารวม
                    </th>
                    <td align="center" style="font-family: arial, sans-serif; margin: 0px;">
                        <?php
                            if($shipping_fee>0)
                            {
                                if($discount>0)
                                {
                                    echo number_format(($shipment->total_price+$shipping_fee)-$discount,2);
                                }
                                else
                                {
                                    echo number_format($shipment->total_price+$shipping_fee,2);
                                }
                            }
                            else
                            {
                                if($discount>0)
                                {
                                    echo number_format(($shipment->total_price+$shipping_fee)-$discount,2);
                                }
                                else
                                {
                                    echo number_format($shipment->total_price+$shipping_fee,2);
                                }
                            }
                        ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <?php
        endforeach;
    ?>
                <tr>
                      <td colspan="2" style="font-family: arial, sans-serif; margin: 0px;">
                        &nbsp;
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-family: arial, sans-serif; margin: 0px;">
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-family: arial, sans-serif; margin: 0px;">
                        &nbsp;
                    </td>
                </tr>
                
                <!-- Start Comment -->
                <tr>
                    <?php #sd($order); ?>
                    <?php 
                        if ((strtolower($order->payment->code) == 'banktrans') || (strtolower($order->payment->code) == 'cs') || (strtolower($order->payment->code) == 'atm') || (strtolower($order->payment->code) == 'ibank') ):
                            $pay_date = date("d/m/Y เวลา H:i:s น.", strtotime($order->expired_at.' -1 day'));
                        endif;
                    ?>
                    <td colspan="2" style="font-family: arial, sans-serif; margin: 0px;">
                        <?php if (strtolower($order->payment->code) === 'atm'){ ?>
                        <div class="col-sm-12">
                            <span class="payment-remark">หมายเหตุสำหรับท่านที่ชำระเงินทาง ATM</span>
                            <div class="panel payment-remark-desc text-red-1">
                                <div class="panel-body">
                                    <p>ลูกค้าต้องชำระเงินภายในวันที่ <?php echo $pay_date;?> มิฉะนั้นลูกค้าอาจไม่ได้รับสินค้าตามการสั่งซื้อ</p>
                                    <ul class="pm-rm-notice">
                                        <li>
                                            การชำระเงินผ่านตู้ ATM ในกรุงเทพฯและปริมณทล จะเสียค่าธรรมเนียมประมาณ 20-25 บาท 
                                            <span style="color: red">*</span>
                                        </li>
                                        <li>
                                            การชำระเงินผ่านตู้ ATM ต่างจังหวัด จะเสียค่าธรรมเนีมประมาณ 35-40 บาท 
                                            <span style="color: red">*</span></li>
                                        <li><span style="color: red">*</span>อัตราค่าธรรมเนียมขึ้นอยู่กับธนาคารที่รับชำระ</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <?php }else if (strtolower($order->payment->code) === 'banktrans'){ ?>
                        <div class="col-sm-12">
                            <span class="payment-remark">หมายเหตุสำหรับท่านที่ชำระเงินทางเคาร์เตอร์ธนาคาร (Payment Counter)</span>
                            <div class="panel payment-remark-desc text-red-1">
                                <div class="panel-body">
                                    <p>ลูกค้าต้องชำระเงินภายในวันที่ <?php echo $pay_date;?> มิฉะนั้นลูกค้าอาจไม่ได้รับสินค้าตามการสั่งซื้อ</p>
                                    <ul class="pm-rm-notice">
                                        <li>การโอนเงินในกรุงเทพฯและปริมณทล จะเสียค่าธรรมเนียมประมาณ 20-25 บาท<span style="color: red">*</span></li>
                                        <li>
                                            การโอนเงินต่างจังหวัด จะเสียค่าธรรมเนีมประมาณ 35-40 บาท 
                                            <span style="color: red"> *</span>
                                        </li>
                                        <li><span style="color: red">*</span>อัตราค่าธรรมเนียมขึ้นอยู่กับธนาคารที่รับชำระ</li>
                                    </ul>
                                    <p>
                                        ดาวน์โหลด และพิมพ์แบบฟอร์มสำหรับชำระเงิน 
                                        <a href="<?php echo $front_url.'/checkout/print'; ?>?order_id=<?php echo array_get($order,'order_id'); ?>" target="_blank">>> คลิกที่นี่ <<</a>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <?php }else if (strtolower($order->payment->code) === 'cs'){ ?>
                        <div class="col-sm-12">
                            <span class="payment-remark">หมายเหตุสำหรับท่านที่ชำระเงินทางเคาร์เตอร์เซอร์วิส (Counter Service)</span>
                            <div class="panel payment-remark-desc text-red-1">
                                <div class="panel-body">
                                    <p>ลูกค้าต้องชำระเงินภายในวันที่ <?php echo $pay_date;?> มิฉะนั้นลูกค้าอาจไม่ได้รับสินค้าตามการสั่งซื้อ</p>
                                    <ul class="pm-rm-notice">
                                        <li>
                                            การชำระสินค้าผ่าน เคาน์เตอร์เซอร์วิส ใบชำระเงินมีอายุ 4 วัน นับจากเวลาที่ทำรายการ และควรชำระภายในวัน เวลาดังกล่าว ไม่เช่นนั้น รายการของท่านจะโดนยกเลิกโดยอัตโนมัติ
                                            <span style="color: red">*</span>
                                        </li>
                                        <li><span style="color: red">*</span>การชำระเงินผ่านเคาน์เตอร์เซอร์วิส มีค่าธรรมเนียมการชำระเงิน 15 บาท</li>
                                    </ul>
                                    <p>ชำระเงินที่ Counter Service ในร้าน 7-Eleven ทุกสาขาทั่วประเทศ (ไม่ต้องแจ้งผลการชำระเงิน)</p>
                                    <?php if ( array_get($order,'barcode') != null) : ?>
                                    <img src="http://www.weloveshopping.com/wetrust/assets/barcode_image.php?barcode_data=<?php echo array_get($order,'barcode');?>" />
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <?php } else if (strtolower($order->payment->code) === 'ibank'){ ?>
                        <div class="col-sm-12">
                            <span class="payment-remark">หมายเหตุสำหรับท่านที่ชำระเงินทาง iBanking</span>
                            <div class="panel payment-remark-desc text-red-1">
                                <div class="panel-body">
                                    <p>ลูกค้าต้องชำระเงินภายในวันที่ <?php echo $pay_date;?> มิฉะนั้นลูกค้าอาจไม่ได้รับสินค้าตามการสั่งซื้อ</p>
                                    <ul class="pm-rm-notice">
                                        <li>
                                            สามารถตรวจสอบวิธีการชำระผ่าน iBanking service ของธนาคารกสิกรไทย,ธนาคารไทยพาณิชย์
                                            และธนาคารกรุงเทพ ได้จากหน้ายืนยันการสั่งซื้อและชำระเงิน
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </td>
                </tr>
                <!-- End Comment -->
        
                <tr>
                    <td colspan="2" style="font-family: arial, sans-serif; margin: 0px;">
                        หากท่านมีข้อสงสัย หรือต้องการสอบถามข้อมูลเพิ่<wbr>มเติม กรุณาติดต่อ
                        <span class="Apple-converted-space">&nbsp;</span>
                        <a href="mailto:iTrueMart@gmail.com" style="color: rgb(17, 85, 204);" target="_blank">iTrueMart@gmail.com</a>
                        <span class="Apple-converted-space">&nbsp;</span>หรือ เบอร์ CALL CENTER : 02 900 9999
                    </td>
                </tr>
            </table>

</body>
</html>