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
            Dear Mr./Mrs./Miss <?php echo $order->customer_name; ?>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="font-family: arial, sans-serif; margin: 0px;">
            &nbsp;
        </td>
    </tr>
    <tr>

        <?php if (strtolower($order->payment->code) === 'atm'){?>
        <td colspan="2" style="font-family: arial, sans-serif; margin: 0px;">
            <span style="padding-left: 5em;">
            Thank you very much for shopping with itruemart Your order been processed.Your order number is <strong style="color: #ff9900">
            <?php echo $order->payment_order_id; ?>(<?php echo $order->order_id; ?>)</strong> and paid by <strong style="color: #ff9900">
                    <?php echo $paymentMethodThai; ?>(<?php echo $paymentMethod; ?>)</strong>  <!-- <span class="Apple-converted-space">&nbsp;</span><a href="<?php //echo $link; ?>"
                    style="color: rgb(17, 85, 204);" target="_blank">click here</a> to track your order</span> -->
            Or <a href="<?php echo $thank_link; ?>">"Click here"</a>
                         to check payment detail.
        </td>

        <?php } else if (strtolower($order->payment->code) === 'banktrans'){ ?>
        <td colspan="2" style="font-family: arial, sans-serif; margin: 0px;">
            <span style="padding-left: 5em;">
            Thank you very much for shopping with itruemart Your order been processed.Your order number is <strong style="color: #ff9900">
            <?php echo $order->payment_order_id; ?>(<?php echo $order->order_id; ?>)</strong> and paid by <strong style="color: #ff9900">
                    <?php echo $paymentMethodThai; ?>(<?php echo $paymentMethod; ?>)</strong>  <!-- <span class="Apple-converted-space">&nbsp;</span><a href="<?php //echo $link; ?>"
                    style="color: rgb(17, 85, 204);" target="_blank">click here</a> to track your order</span> -->
            Or <a href="<?php echo $thank_link; ?>">"Click here"</a>
                 to check payment detail.
        </td>

        <?php }else if (strtolower($order->payment->code) === 'ccw'){ ?>
        <td colspan="2" style="font-family: arial, sans-serif; margin: 0px;">
            <span style="padding-left: 5em;">
            Thank you very much for shopping with itruemart Your order been processed.Your order number is <strong style="color: #ff9900">
            <?php echo $order->payment_order_id; ?>(<?php echo $order->order_id; ?>)</strong> and paid by <strong style="color: #ff9900">
                    <?php echo $paymentMethodThai; ?>(<?php echo $paymentMethod; ?>)</strong>  <span class="Apple-converted-space">&nbsp;</span> as below detail. <!-- Your order will be shipped as soon as we receive the payment confirmation form the bank
                    <a href="<?php //echo $link; ?>" style="color: rgb(17, 85, 204);" target="_blank">click here</a> to track your order</span> -->
                Or <a href="<?php echo $thank_link; ?>">"Click here"</a>
                 to check payment detail.
        </td>

        <?php }else if (strtolower($order->payment->code) === 'cs'){ ?>
        <td colspan="2" style="font-family: arial, sans-serif; margin: 0px;">
            <span style="padding-left: 5em;">
            Thank you very much for shopping with itruemart Your order been processed.Your order number is <strong style="color: #ff9900">
            <?php echo $order->payment_order_id; ?>(<?php echo $order->order_id; ?>)</strong> and paid by <strong style="color: #ff9900">
                    <?php echo $paymentMethodThai; ?>(<?php echo $paymentMethod; ?>)</strong>  <!-- <span class="Apple-converted-space">&nbsp;</span><a href="<?php //echo $link; ?>"
                    style="color: rgb(17, 85, 204);" target="_blank">click here</a> to track your order</span> -->
            Or <a href="<?php echo $thank_link; ?>">"Click here"</a>
                 to check payment detail.
        </td>

        <?php } else if (strtolower($order->payment->code) === 'ibank'){ ?>
        <td colspan="2" style="font-family: arial, sans-serif; margin: 0px;">
            <span style="padding-left: 5em;">
            Thank you very much for shopping with itruemart Your order been processed.Your order number is <strong style="color: #ff9900">
            <?php echo $order->payment_order_id; ?>(<?php echo $order->order_id; ?>)</strong> and paid by <strong style="color: #ff9900">
                    <?php echo $paymentMethodThai; ?>(<?php echo $paymentMethod; ?>)</strong>  <!-- <span class="Apple-converted-space">&nbsp;</span><a href="<?php //echo $link; ?>"
                    style="color: rgb(17, 85, 204);" target="_blank">click here</a> to track your order</span> -->
                Or <a href="<?php echo $thank_link; ?>">"Click here"</a>
                 to check payment detail.
        </td>

        <?php }else { ?>
        <td colspan="2" style="font-family: arial, sans-serif; margin: 0px;">
            <span style="padding-left: 5em;">
            Thank you very much for shopping with itruemart Your order been processed.Your order number is <strong style="color: #ff9900">
            <?php echo $order->payment_order_id; ?>(<?php echo $order->order_id; ?>)</strong> and paid by <strong style="color: #ff9900">
                    <?php echo $paymentMethodThai; ?>(<?php echo $paymentMethod; ?>)</strong>  <!-- <span class="Apple-converted-space">&nbsp;</span><a href="<?php //echo $link; ?>"
                    style="color: rgb(17, 85, 204);" target="_blank">click here</a> to track your order</span> -->
                Or <a href="<?php echo $thank_link; ?>">"Click here"</a>
                 to check payment detail.
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
                            <span>Name :</span>
                        </th>
                        <td style="font-family: arial, sans-serif; margin: 0px;">
                            <span><?php echo $order->customer_name; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th style="font-family: arial, sans-serif; margin: 0px;" align="right">
                            <span>Mobile Number :</span>
                        </th>
                        <td style="font-family: arial, sans-serif; margin: 0px;">
                            <span><?php echo $order->customer_tel; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th style="font-family: arial, sans-serif; margin: 0px;" align="right">
                            <span>Email :</span>
                        </th>
                        <td style="font-family: arial, sans-serif; margin: 0px;">
                            <span><a href="mailto:somyot_pon@truecorp.co.th" style="color: rgb(17, 85, 204);"
                                target="_blank"><?php echo $order->customer_email; ?></a></span>
                        </td>
                    </tr>
                    <tr>
                        <th style="font-family: arial, sans-serif; margin: 0px;" align="right">
                            <span>Shipping address :</span>
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
                        <span>Order Number:</span>
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
                        <span>Date of Order:</span>
                    </th>
                    <td style="font-family: arial, sans-serif; margin: 0px;">
                        <span style="margin-left: 10px;"><?php echo $order->created_at;?></span>
                    </td>
                </tr>
                <tr>
                    <th style="font-family: arial, sans-serif; margin: 0px;" align="right">
                        <span>Payment Method:</span>
                    </th>
                    <td style="font-family: arial, sans-serif; margin: 0px;">
                        <span style="margin-left: 10px;"><?php echo $paymentMethodThai; ?>(<?php echo $paymentMethod; ?>)</span></span>
                    </td>
                </tr>
                <tr>
                    <th style="font-family: arial, sans-serif; margin: 0px;" align="right">
                        <span>Payment Status:</span>
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
                            Shop <strong style="color: #000;">
                            <?php
                            $vendor = VVendor::find($shipment->vendor_id);
                            $shop = Shop::find($vendor->shop_id);
                            echo $shop->name;
                            ?>
                            (<?php echo $vendor->name; ?>) 
                            </strong> (<?php echo count($shipment);?> Items)
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
                item
                        </td>
                        <td style="font-family: arial, sans-serif; margin: 0px; color: #000;" width="90">
                price/unit
                        </td>
                        <td style="font-family: arial, sans-serif; margin: 0px; color: #000;" width="50">
                amount
                        </td>
                        <td style="font-family: arial, sans-serif; margin: 0px; color: #000;" width="90">
                Total Price (฿)
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
                        <img src="<?php echo $variant->product->image; ?>"
                            width="100px" style="float: left; margin: 5px 20px 5px 5px" />
                        <p style="overflow: hidden;">
                            Order Number: <?php echo $item->material_code . '/' . $item->inventory_id;?><br />
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
                endforeach;
                ?>

                <tr bgcolor="#dddddd">
                    <th colspan="3" style="font-family: arial, sans-serif; margin: 0px; padding: 5px" align="right">
                        Shipping Fee:
                    </th>
                    <td align="center" style="font-family: arial, sans-serif; margin: 0px;">
                        <?php echo $shipment->method->name; ?>(<?php echo $shipment->method->description; ?>)
                    </td>
                </tr>
                <tr bgcolor="#dddddd">
                    <th colspan="3" style="font-family: arial, sans-serif; margin: 0px; padding: 5px"
                        align="right">
                        Discount
                    </th>
                    <td align="center" style="font-family: arial, sans-serif; margin: 0px;">
                        <?php echo number_format($discount,2); ?>
                    </td>
                </tr>
                <tr bgcolor="#dddddd">
                    <th colspan="3" style="font-family: arial, sans-serif; margin: 0px; padding: 5px"
                        align="right">
                       Total Amount
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
                            $pay_date = date("d/m/Y H:i:s", strtotime($order->expired_at.' -1 day'));
                        endif;
                    ?>
                    <td colspan="2" style="font-family: arial, sans-serif; margin: 0px;">
                        <?php if (strtolower($order->payment->code) === 'atm'){ ?>
                        <div class="col-sm-12">
                            <span class="payment-remark">Remark : For customer who pay by ATM channel</span>
                            <div class="panel payment-remark-desc text-red-1">
                                <div class="panel-body">
                                    <p>You have to pay in <?php echo $pay_date;?>. If not your products will not be sent.</p>
                                    <ul class="pm-rm-notice">
                                        <li>
                                            Payment by ATM Machine in Bangkok Metropolitan Area have fee about 20-25 Baht.
                                            <span style="color: red">*</span>
                                        </li>
                                        <li>
                                            Payment by ATM Machine in countryside have fee about 35-40 Baht.
                                            <span style="color: red">*</span></li>
                                        <li><span style="color: red">*</span>Fee rate will relate with bank where you pay.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <?php }else if (strtolower($order->payment->code) === 'banktrans'){ ?>
                        <div class="col-sm-12">
                            <span class="payment-remark">Remark : For customer who pay by Bank Transfer (Payment Counter)</span>
                            <div class="panel payment-remark-desc text-red-1">
                                <div class="panel-body">
                                    <p>You have to pay in <?php echo $pay_date;?>. If not your products will not be sent.</p>
                                    <ul class="pm-rm-notice">
                                        <li>Payment by Transfering in Bangkok Metropolitan Area have fee about 20-25 Baht<span style="color: red">*</span></li>
                                        <li>
                                            Payment by Transfering in countryside have fee about 35-40 Baht.
                                            <span style="color: red"> *</span>
                                        </li>
                                        <li><span style="color: red">*</span>Fee rate will relate with bank where you pay.</li>
                                    </ul>
                                    <p>
                                        Download and print form for payment
                                        <a href="<?php echo $front_url.'/checkout/print'; ?>?order_id=<?php echo array_get($order,'order_id'); ?>" target="_blank">>> Click here <<</a>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <?php }else if (strtolower($order->payment->code) === 'cs'){ ?>
                        <div class="col-sm-12">
                            <span class="payment-remark">Remark : For customer who pay by Counter Service </span>
                            <div class="panel payment-remark-desc text-red-1">
                                <div class="panel-body">
                                    <p>You have to pay in <?php echo $pay_date;?>. If not your products will not be sent.</p>
                                    <ul class="pm-rm-notice">
                                        <li>
                                            Payment by counter service payment bill is expired in 4 days start at creating order and should pay in day rang if not your order will be auto cancelled,
                                            <span style="color: red">*</span>
                                        </li>
                                        <li><span style="color: red">*</span>Payment by counter service have fee about 15 Baht</li>
                                    </ul>
                                    <p>Pay at Counter Service 7-Eleven all branch in country. (Not confirm your order)</p>
                                    <?php if ( array_get($order,'barcode') != null) : ?>
                                    <img src="http://www.weloveshopping.com/wetrust/assets/barcode_image.php?barcode_data=<?php echo array_get($order,'barcode');?>" />
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <?php } else if (strtolower($order->payment->code) === 'ibank'){ ?>
                        <div class="col-sm-12">
                            <span class="payment-remark">Remark : For customer who pay by iBanking</span>
                            <div class="panel payment-remark-desc text-red-1">
                                <div class="panel-body">
                                    <p>You have to pay in <?php echo $pay_date;?>. If not your products will not be sent.</p>
                                    <ul class="pm-rm-notice">
                                        <li>
                                            Can check payment method by iBanking service of Kasikorn Bank, The Siam Commercial Bank
                                            and Bauluang Bank from order comfirm page.
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
                        If you have any question or want to ask more <wbr>information. Please contract<span class="Apple-converted-space">&nbsp;</span><a
                            href="mailto:iTrueMart@gmail.com" style="color: rgb(17, 85, 204);" target="_blank">iTrueMart@gmail.com</a><span
                                class="Apple-converted-space">&nbsp;</span>or CALL CENTER No. : 02 900 9999
                    </td>
                </tr>
            </table>

</body>
</html>