<div class="mws-panel grid_8">

    <div class="mws-panel-header">
        <span><i class="icon-table"></i> Order Discount Tracker</span>
    </div>

    <div class="mws-panel-body">

    <div class="order-detail">
    	<p>
    		<strong>Order ID :</strong> <?php echo $order->id ?>
    	</p>
        <p>
        	<strong>Order Discount :</strong> <?php echo $order->discount ?> THB
        </p>

        <p>
        	<strong>Customer Pay :</strong> <?php echo $allCustomerPay ?> THB
        </p>

    </div>
    <?php $arrShipmentId = array_unique($orderTransactions->lists('order_shipment_id')) ?>
    <?php foreach ($arrShipmentId as $order_shipment_id) { ?>
    	<?php
	    	$eachShipment = $orderTransactions->filter( function($transaction) use($order_shipment_id) {
	    		return ($transaction->order_shipment_id == $order_shipment_id);
	    	});
    	?>
    	<table class="discount-tracker mws-table" style="margin-bottom:20px; border:1px solid #666;">
            <thead>
            	<tr>
            		<th width="200">Item</th>
            		<th>Total Price</th>
            		<th>Total Margin</th>
            		<th>Item Discount</th>
            		<th>Order Discount</th>
            		<th>Customer Pay</th>
            		<th>Fee</th>
            		<th>Vendor Owe</th>
            		<th>Business Lost</th>
            	</tr>
            </thead>
            <tbody>
            	<?php foreach ($eachShipment as $key=>$val) { ?>
            		<?php if ($val->order_shipment_item_id != 0) { ?>
            		<tr>
            			<td><?php echo $val->item_title ?></td>
		        		<td><?php echo $val->total_price ?></td>
		        		<td><?php echo $val->total_margin ?></td>
		        		<td><?php echo $val->item_discount ?></td>
		        		<td><?php echo $val->affected_discount ?></td>
		        		<td><?php echo $val->customer_pay ?></td>
		        		<td><?php echo $val->fee ?></td>
		        		<td><?php echo $val->vendor_owe ?></td>
		        		<td><?php echo $val->business_lost ?></td>
            		</tr>
            		<?php } else { ?>
            		<tr class="shipment-row">
            			<td><strong>Shipment : <?php echo $val->item_title ?></strong></td>
            			<td><?php echo $val->price ?></td>
            			<td>-</td>
            			<td>-</td>
            			<td>-</td>
            			<td><?php echo $val->customer_pay ?></td>
            			<td><?php echo $val->fee ?></td>
            			<td><?php echo $val->vendor_owe ?></td>
            			<td>-</td>
            		</tr>
            		<?php } ?>
            	<?php } ?>
            </tbody>
        </table>

	<?php } ?>




        <?php // d($orderTransactions) ?>
    </div>

</div>


<style type="text/css">
.order-detail { font-size:16px; }
.order-detail strong { display:block; float:left; width:150px; }
.discount-tracker td { text-align: center; }
/*.discount-tracker .shipment-row td { background:#FFD; }*/
</style>