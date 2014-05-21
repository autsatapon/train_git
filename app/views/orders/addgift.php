<div class="mws-panel grid_8">
	<div class="mws-panel-header">
		<span><i class="icon-gift"></i> Add Gift</span>
    </div>

	<div class="mws-panel-body no-padding add-gift">
		<div class="mws-form">
			<div class="mws-form-inline">
				<div class="mws-form-row">
					<label class="mws-form-label" for="add-gift-item">Inventory ID</label>
					<div class="mws-form-item">
						<?php echo Form::text('inventory_id', Input::old('inventory_id'), array('id'=>'gift-inventory-id')) ?>
						<input type="button" id="add-gift-item" value="Add" class="btn">
					</div>
				</div>
			</div>
		</div>
		<form class="mws-form" action="/orders/add-gift/<?php echo $order->getKey(); ?>" method="POST">
			<table class="mws-table customer">
				<thead>
					<tr>
						<th width="40%"><?php echo Product::getLabel('name') ?></th>
						<th width="20%"><?php echo ProductVariant::getLabel('inventory_id') ?></th>
						<th width="20%">Quantity</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody id="gift-list">
				<?php foreach($order->giftItems as $gift): ?>
					<tr data-id="<?php echo $gift->getKey() ?>">
						<td>
							<?php echo $gift->variant!=false ? $gift->variant->product->name : '' ?>
						</td>
						<td class="table-center">
							<?php echo $gift->inventory_id ?>
						</td>
						<td>
							<input type="text" name="giftQuantity[<?php echo $gift->inventory_id ?>]" value="<?php echo number_format($gift->quantity) ?>" size="2" class="pcms-numeric">
						</td>
						<td>
							<a href="/orders/remove-gift/<?php echo $gift->getKey() ?>" class="btn btn-warning"><i class="icon icon-trash"></i></a>
						</td>
					</tr>
				<?php endforeach ?>
				</tbody>
			</table>
			<div class="submit">
				<input type="submit" value="Save" class="btn btn-primary">
			</div>
		</form>
	</div>
</div>

<?php

Theme::asset()->container('footer')->writeScript('gift-item', '
$(document).on("click","#add-gift-item",function(e){
	e.preventDefault();
	var inventId = $("#gift-inventory-id"),
		tempId = Math.random();
	inventId.val() && $("#gift-list").append("<tr data-id=\'"+inventId.val()+"\'><td>&nbsp;</td><td class=\'table-center\'>"+inventId.val()+"</td><td><input type=\'text\' name=\'newGiftQuantity["+inventId.val()+"]\' value=\'1\' size=\'2\' class=\'pcms-numeric\'></td><td></td></tr>");
	inventId.val("");
})
');

?>
<div class="mws-panel grid_8">

    <div class="mws-panel-header">
        <span><i class="icon-shopping-cart"></i> Order: <?php echo $order->order_ref; ?></span>
    </div>

	<?php echo Theme::widget('WidgetOrderDetailCustomer', compact('user', 'order'))->render(); ?>

	<?php $i = 1; ?>

	<?php foreach ($order->shipments as $shipment): ?>

		<?php echo Theme::widget('WidgetOrderDetailShipment', compact('user', 'order', 'shipment', 'i'))->render(); ?>

		<?php $i++; ?>

	<?php endforeach; ?>

</div>
