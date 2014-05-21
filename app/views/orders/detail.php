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

<?php if(count($order->orderLogs)>0): ?>
<div class="mws-panel grid_8">
	<div class="mws-panel-header">
		<span><i class="icon-users"></i> Activities Logs</span>
    </div>

    <table class="mws-datatable-fn mws-table">
		<thead>
			<tr>
				<th width="20%" rowspan="2">Actor</th>
				<th width="30%" colspan="2">Activity</th>
				<th width="20%" rowspan="2">Time</th>
				<th width="10%" rowspan="2">Performance</th>
				<th width="20%" rowspan="2">SLA Time</th>
			</tr>
			<tr>
				<th width="15%">Previous Status</th>
				<th width="15%">New Status</th>
			</tr>
		</thead>
		<tbody>
	    	<?php foreach($order->orderLogs as $order_log): $user = $order_log->actor; ?>
	    	<tr>
	    		<td><?php echo $user!=false ? $order_log->actor->display_name.' ('.$order_log->actor->groups[0]->name.')' : '&nbsp;' ?></td>
	    		<td class="table-center"><?php echo ucfirst($order_log->previous_status) ?></td>
	    		<td class="table-center"><?php echo ucfirst($order_log->order_status_th ?: $order_log->customer_status) ?></td>
	    		<td class="table-center">
	    			<?php echo $order_log->created_at->format('j/M/Y H:i') ?>
	    		</td>
	    		<td class="table-center">
	    			<div class="label <?php echo ($order_log->created_at < $order_log->previous_sla_time_at ? 'label-success' : 'label-important') ?>"><?php echo $order_log->created_at->diffForHumans($order_log->previous_sla_time_at) ?></div>
	    		</td>
	    		<td class="table-center"><?php echo $order_log->previous_sla_time_at!=false ? $order_log->previous_sla_time_at->format('j/M/Y H:i') : '&nbsp;' ?></td>
	    	</tr>
	    	<?php endforeach ?>
	    </tbody>
    </table>
</div>
<?php endif ?>

<?php if(count($order->orderAddressLog)>0): ?>
<div class="mws-panel grid_8">
	<div class="mws-panel-header">
		<span><i class="icon-users"></i> Address Logs</span>
    </div>

    <table class="mws-datatable-fn mws-table">
		<thead>
			<tr>
				<th width="20%" >Actor</th>
				<th width="10%" >Name</th>
				<th width="20%" >Address</th>
				<th width="10%" >Province</th>
				<th width="10%" >Postcode</th>
				<th width="10%" >Tel</th>
				<th width="20%" >Time</th>
		
			</tr>
		</thead>
		<tbody>
	    	<?php foreach($order->orderAddressLog as $order_address_log): $user = $order_address_log->user; ?>
	    	<tr>
	    		<td class="table-center"><?php echo $user['display_name'];echo "   (".$user['groups'][0]['name'].")";  ?></td>
	    		<td class="table-center"><?php echo $order_address_log->name ?></td>
	    		<td ><?php echo $order_address_log->address ?></td>
	    		<td ><?php echo $order_address_log->province ?></td>
	    		<td class="table-center"><?php echo $order_address_log->postcode ?></td>
	    		<td ><?php echo $order_address_log->tel ?></td>
				<td class="table-center">
	    			<?php echo $order_address_log->created_at->format('j/M/Y H:i') ?>
	    		</td>
	    	</tr>
	    	<?php endforeach ?>
	    </tbody>
    </table>
</div>
<?php endif ?>