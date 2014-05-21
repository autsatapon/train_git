<div class="mws-panel grid_8" style="margin-top: 40px;">
    <div class="mws-panel-header">
        <span>1. หยิบสินค้าลงตระกร้า</span>
    </div>
    <div class="mws-panel-body no-padding">
        <form class="mws-form" method="POST">
            <div class="mws-form-inline">
                <div class="mws-form-row">
                    <label class="mws-form-label">สินค้า</label>
                    <div class="mws-form-item">
                        <select name="inventory_ids[]" multiple="multiple" style="height: 200px;">
                            <?php foreach ($variants as $variant): ?>
                                <option value="<?php echo $variant->inventory_id; ?>">
                                    <?php echo $variant->title; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <br />
                        *กด Ctrl(Cmd) ค้างไว้เพื่อเลือกสินค้าหลายชนิด
                    </div>
                </div>
                <div class="mws-form-row">
                    <label class="mws-form-label">ชนิดละ</label>
                    <div class="mws-form-item">
                        <input name="qty" type="text" class="small" value="1" style="width: 200px;">
                        <span style="margin-left: 10px;">ชิ้น</span>
                    </div>
                </div>
            </div>
            <div class="mws-button-row">
                <input name="action" type="hidden" value="add-to-cart">
                <input type="submit" value="เพิ่มลงตระกร้า" class="btn btn-danger">
            </div>
        </form>
    </div>
</div>

<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span>2. ตระกร้าสินค้า</span>
    </div>
    <div class="mws-panel-body no-padding">
        <div class="mws-form">
            <div class="mws-form-inline">
                <div class="mws-form-row">
                    <label class="mws-form-label">สินค้าในตระกร้า</label>
                    <div class="mws-form-item">
                        <ul>
                            <?php if (!empty($cart)) foreach ($cart->cartDetails as $item): ?>
                                    <li>
                                        <span>
                                            <?php echo $item->title; ?>
                                        </span>
                                        <span style="margin-left: 10px; display: inline-block;">
                                            <?php echo Form::open(); ?>
                                            จำนวน
                                            <?php echo Form::text('qty', $item->quantity, array('style' => 'width: 60px')); ?>
                                            <?php echo Form::hidden('inventory_id', $item->inventory_id); ?>
                                            <input name="action" type="hidden" value="update-item">
                                            <input type="submit" value="แก้ไข" class="btn btn-danger">
                                            <?php echo Form::close(); ?>
                                        </span>
                                        <span style="margin-left: 10px;">
                                            <a href="?action=remove-item&inventory_id=<?php echo $item->inventory_id; ?>" class="btn">ลบ</a>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($order)): ?>
    <div class="mws-panel grid_8">
        <div class="mws-panel-header">
            <span>3. รายการสั่งซื้อ</span>
        </div>
        <div class="mws-panel-body no-padding">
            <div class="mws-form" method="POST">
                <fieldset class="mws-form-inline">

                    <legend>Customer Info</legend>

                    <div class="mws-form-row">
                        <label class="mws-form-label">
                            จังหวัด
                        </label>
                        <div class="mws-form-item">
                            <form method="POST">
                                <input name="action" type="hidden" value="select-province">
                                <?php echo Form::select('customer_province_id', $provinces, $order['customer_province_id'], array('onchange' => '$(this).parents(\'form\').submit();')); ?>
                            </form>
                        </div>
                    </div>
                    <?php if ($order['customer_province_id']): ?>
                        <div class="mws-form-row">
                            <label class="mws-form-label">
                                อำเภอ
                            </label>
                            <div class="mws-form-item">
                                <form method="POST">
                                    <input name="action" type="hidden" value="select-city">
                                    <?php echo Form::select('customer_city_id', $cities, $order['customer_city_id'], array('onchange' => '$(this).parents(\'form\').submit();')); ?>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>
                </fieldset>

                <fieldset class="mws-form-inline">

                    <legend>Shipments</legend>
                    <form method="POST">
                        <?php $i = 1; ?>

                        <?php foreach ($order['shipments'] as $key => $shipment): ?>
                            <div class="mws-form-row">
                                <label class="mws-form-label">
                                    Shipment <?php echo $i; ?>
                                </label>
                                <div class="mws-form-item">
                                    <ul>
                                        <?php foreach ($shipment['items'] as $item): ?>
                                            <li>
                                                <?php echo $item['name']; ?>
                                                -
                                                <?php echo $item['price']; ?>
                                                x
                                                <?php echo $item['quantity']; ?>
                                                =
                                                <?php echo $item['quantity'] * $item['price']; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <?php if (count($shipment['available_shipping_methods'])): ?>
                                        <?php $tmp = array('' => 'กรุณาเลือกวิธีการขนส่ง'); ?>
                                        <?php foreach ($shipment['available_shipping_methods'] as $pkey => $value): ?>
                                            <?php $tmp[$pkey] = $value['name'] . ' (' . $value['fee'] . ' บาท)'; ?>
                                        <?php endforeach; ?>
                                        <?php echo Form::select('shipments[' . $key . ']', $tmp, $shipment['shipping_method'], array('_onchange' => '$(this).parents(\'form\').submit();')); ?>
                                        ราคาสินค้า <?php echo $shipment['total_price']; ?> บาท<br />
                                        ราคาค่าขนส่ง <?php echo $shipment['shipping_fee']; ?> บาท
                                    <?php else: ?>
                                        ไม่สามารถจัดส่งสินค้าได้
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php $i++; ?>

                        <?php endforeach; ?>
                        <div class="mws-form-row">
                            <label class="mws-form-label"></label>
                            <div class="mws-form-item">
                                <input name="action" type="hidden" value="select-shipment">
                                <input type="submit" class="btn" value="อัพเดทวิธีการขนส่ง" />
                            </div>
                        </div>
                    </form>

                </fieldset>

                <fieldset class="mws-form-inline">

                    <legend>Payment</legend>

                    <div class="mws-form-row">
                        <label class="mws-form-label">
                            วิธีการชำระเงิน
                        </label>
                        <div class="mws-form-item">
                            <form method="POST">
                                <input name="action" type="hidden" value="select-payment">
                                <?php $tmp = array('' => 'กรุณาเลือกวิธีการชำระเงิน'); ?>
                                <?php foreach ((array) @$order['available_payment_methods'] as $pkey => $value): ?>
                                    <?php $tmp[$pkey] = $value; ?>
                                <?php endforeach; ?>
                                <?php echo Form::select('payment_method', $tmp, null, array('onchange' => '$(this).parents(\'form\').submit();')); ?>
                                <br />
                                <?php echo @PaymentMethod::find($order['payment_method'])->name; ?>
                            </form>
                        </div>
                    </div>

                </fieldset>

                <fieldset class="mws-form-inline">

                    <legend>Total</legend>

                    <div class="mws-form-row">
                        <label class="mws-form-label">
                            ราคาสินค้า
                        </label>
                        <div class="mws-form-item">
                            <?php echo $order['total_price']; ?> บาท
                        </div>
                    </div>

                    <div class="mws-form-row">
                        <label class="mws-form-label">
                            ราคาค่าขนส่ง
                        </label>
                        <div class="mws-form-item">
                            <?php echo $order['total_shipping_fee']; ?> บาท
                        </div>
                    </div>

                    <div class="mws-form-row">
                        <label class="mws-form-label">
                            ส่วนลด
                        </label>
                        <div class="mws-form-item">
                            <?php echo $order['discount']; ?> บาท
                        </div>
                    </div>

                    <div class="mws-form-row">
                        <label class="mws-form-label">
                            ราคารวม
                        </label>
                        <div class="mws-form-item">
                            <?php echo $order['sub_total']; ?> บาท
                        </div>
                    </div>

                    <div class="mws-button-row">
                        <form method="POST">
                            <input name="action" type="hidden" value="create-order">
                            <button type="submit" class="btn btn-danger">เสร็จสิ้น (Create order)</button>
                        </form>
                    </div>

                </fieldset>
            </div>
        </div>
    </div>
<?php endif; ?>

<style>
    #mws-footer { position: static !important; }
</style>