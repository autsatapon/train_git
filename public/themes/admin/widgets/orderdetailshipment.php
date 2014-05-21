<?php if ($editable) echo Form::open(); ?>
<div class="mws-panel-header shipment">
	<span><i class="icon-truck"></i> Shipment: <?php echo $i; ?></span>
</div>

<div class="mws-panel-body no-padding">

	<div class="clearfix shipping-header">

		<div class="grid_8 text-right">
			<div class="label"><strong>Status: <?php echo $shipment->shipment_status ?></strong></div>
		</div>

		<div class="grid_4">
			<strong>Shipping method:</strong> <?php echo $shipment->method->name; ?> <a href="<?php echo $shipment->method->tracking_url; ?>" target="_blank"><i class="icon icon-link"></i></a>
		</div>

		<div class="grid_4 text-right">
			<strong>Ship by:</strong> <?php echo $ship_by; ?>
		</div>

		<div class="grid_4">
			<strong>Tracking number:</strong>
			<?php if ($editable): ?>
			<?php echo Form::text('shipment['.$shipment->getKey().'][tracking_number]', $shipment->tracking_number); ?>
			<?php else: ?>
			<?php echo ($shipment->tracking_number)?:'-'; ?>
			<?php endif; ?>
		</div>

		<div class="grid_4 text-right">
			 <!-- <span   class="label label<?php echo ($stock_type == 'stock')?'success':'warning'; ?>">Stock Type <?php echo ucfirst($stock_type); ?></span>  -->
			<span > <strong>Stock Type :</strong> <?php echo ucfirst($stock_type); ?></span> 
		</div>

		<div class="grid_4">
			<strong>Shipping fee:</strong> <?php echo number_format($shipment->shipping_fee); ?> Baht
		</div>

		<div class="grid_4 text-right">
			<strong>Shipment total price:</strong> <?php echo number_format($shipment->total_price); ?> Baht
		</div>

	</div>

	<table class="mws-datatable-fn mws-table">
		<thead>
			<tr>
				<th width="20%">Inventory ID</th>
				<!-- <th width="10%">Material code</th> -->
				<th width="25%">Item</th>
				<th width="10%">Quantity</th>
				<th width="10%">Price</th>
				<th width="10%">Total</th>
				<th width="15%">Status</th>
				<th width="10%">Tracking Number</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($shipment->shipmentItems as $item): ?>
			<tr>
				<td class="table-center">
					<?php echo $item->inventory_id; ?>
				</td>
				<!-- <td>
					-
				</td> -->
				<td>
					<?php echo $item->name; ?>
				</td>
				<td class="table-center">
					<?php echo number_format($item->quantity); ?>
				</td>
				<td class="table-center"><?php
					if($item->is_gift_item==false)
					{
						echo number_format($item->price);
					}
					else
					{
						echo '-';
					}
				?></td>
				<td class="table-center"><?php
					if($item->is_gift_item==false)
					{
						echo number_format($item->total_price);
					}
					else
					{
						echo '<i class="icon icon-gift"></i>';
					}
				?></td>
				<td>
					<?php if ($editable): ?>
					<?php echo Form::select('items['.$item->getKey().'][item_status]', array(
						'prepairing' => 'Preparing',
						'ready' => 'Ready',
						'waiting' => 'Waiting',
						'out of stock' => 'Out of stock'
						),
					$item->item_status); ?>
					<?php else: ?>
					<?php echo ($item->item_status)?:'Preparing'; ?>
					<?php endif; ?>
				</td>
				<td>
					<?php if ($editable): ?>
							<?php echo Form::text('items['.$item->getKey().'][tracking_number]', $item->tracking_number); ?>
					<?php else: ?>
							<?php echo ($item->tracking_number)?:'-'; ?>
					<?php endif; ?>
				</td>
			</tr>
			<?php endforeach ;?>
			<tr>
				<td colspan="4"></td>
				<td class="table-center"><?php echo number_format($shipment->total_price); ?></td>
				<td></td>
			</tr>
		</tbody>
	</table>

</div>

<?php if ($editable): ?>
<div class="clearfix actions">
	<div class="pull-right action">
		<strong>Shipment status</strong>
		<?php echo Form::select('shipment['.$shipment->getKey().'][shipment_status]', $shipment_status,
		$shipment->shipment_status); ?>
		<button class="btn btn-primary">Save</button>
	</div>
</div>
<?php else: ?>
<div class="clearfix actions">
	<div class="pull-right action">
		<strong>Shipment status</strong>
		<?php echo ucfirst($shipment->shipment_status)?:'Prepairing'; ?>
	</div>
</div>
<?php endif; ?>
<?php if ($editable) echo Form::close(); ?>