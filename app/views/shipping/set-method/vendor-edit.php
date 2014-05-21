<div class="mws-panel grid_8">
	<div class="mws-panel-header">
		<span><i class="icon-table"></i> Set Shipping Method by Vendor</span>
	</div>

	<div class="mws-panel-body no-padding">
        <form method="post" action="" class="mws-form">
            <div class="mws-form-inline">
                <div class="mws-form-row">
                    <label class="mws-form-label">Vendor Detail</label>
                    <div class="mws-form-item">
                        <p style="margin:0; padding:5px 0;"><?php echo $vendor->vendor_detail ?></p>
                    </div>
                </div>
                <div class="mws-form-row">
                    <label class="mws-form-label">Shipping Method</label>
                    <div class="mws-form-item">
                        <ul class="mws-form-list shipping-method-row">
                            <?php foreach ($shippingMethods as $key=>$shippingMethod) { ?>
                            <?php
                                $shippingSlug = strtolower($shippingMethod->slug);
                                if ($shippingSlug === Order::COD || $shippingSlug === Order::FREE_SHIPPING)
                                {
                                    continue;
                                }
                               $checked = (in_array($shippingMethod->id, $arrVendorShippingMethodId)) ? 1 : 0 ;
                            ?>
                            <li>
                                <label>
                                    <?php echo Form::checkbox('shipping_method_id[]', $shippingMethod->id, $checked, array('class' => 'shipping-method-item', 'data-alway-with' => $shippingMethod->alway_with_shipping_methods)) ?> <?php echo $shippingMethod->name ?>
                                </label>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
                <div class="mws-button-row">
                    <input type="submit" class="btn btn-primary" value="Save">
                </div>
            </div>
        </form>
	</div>
</div>
<?php

Theme::asset()->add('sh-method-js','/js/shipping-method.js');
