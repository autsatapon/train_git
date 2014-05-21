<div class="mws-panel grid_8">
	<div class="mws-panel-header">
		<span><i class="icon-table"></i> Set Shipping Method by Stock Type</span>
	</div>

	<div class="mws-panel-body">

		<?php if(Session::has('success')) { ?>
			<div class="alert alert-success">
				<p><?php echo Session::get('success') ?></p>
			</div>
		<?php } ?>

		<form method="post" action="<?php echo URL::to('shipping/set-method/stock') ?>">
			<div class="mws-panel grid_4">
				<div class="mws-panel-header">
					<span>Stock</span>
				</div>

				<div class="mws-panel-body shipping-method-row">
					<?php foreach($shippingMethods as $key=>$shippingMethod) { ?>
					<?php
                        $shippingSlug = strtolower($shippingMethod->slug);
                        if ($shippingSlug === Order::COD)
                        {
                            continue;
                        }
                    ?>
						<p>
							<label>
								<input type="checkbox" name="shipping_method_id[stock][]" class="shipping-method-item" data-alway-with="<?php echo $shippingMethod->alway_with_shipping_methods ?>" value="<?php echo $shippingMethod->id ?>"<?php if (!empty($typeStock) && in_array($shippingMethod->id, $typeStock)) { echo ' checked="checked"'; } ?>> <?php echo $shippingMethod->name ?>
							</label>
						</p>
					<?php } ?>
				</div>
			</div>

			<div class="mws-panel grid_4">
				<div class="mws-panel-header">
					<span>Non-Stock</span>
				</div>

				<div class="mws-panel-body shipping-method-row">
					<?php foreach($shippingMethods as $key=>$shippingMethod) { ?>
					<?php
                        $shippingSlug = strtolower($shippingMethod->slug);
                        if ($shippingSlug === Order::COD)
                        {
                            continue;
                        }
                    ?>
						<p>
							<label>
								<input type="checkbox" name="shipping_method_id[nonstock][]" class="shipping-method-item" data-alway-with="<?php echo $shippingMethod->alway_with_shipping_methods ?>" value="<?php echo $shippingMethod->id ?>"<?php if (!empty($typeNonStock) && in_array($shippingMethod->id, $typeNonStock)) { echo ' checked="checked"'; } ?>> <?php echo $shippingMethod->name ?>
							</label>
						</p>
					<?php } ?>
				</div>
			</div>

			<div class="clear"></div>

			<p>
				<input type="submit" class="btn btn-primary" value="Save">
			</p>
		</form>
	</div>
</div>
<?php

Theme::asset()->add('sh-method-js','/js/shipping-method.js');
