

<div class="mws-panel grid_8">

    <div class="mws-panel-header">
        <span style="width:40%;text-align:left;float:left;">
				<i class="icon-table"></i> Order Tracker
		</span>

		<span style="width:60%;text-align:right;">
				<p>Search By Date : <input type="text" id="datepicker" name="dateSelected" style="min-height:26px;padding:0;margin:0;" value="<?php echo $dateData ?>"> And Status :
				<select id="statusSelected" name="statusSelected" style="min-height:26px;padding:0;margin:0;">
						<option value="all" <?php if($status == 'all'){echo "selected"; }?>>All</option>
						<option value="closed" <?php if($status == 'closed'){echo "selected"; }?>>Closed</option>
						<option value="new" <?php if($status == 'new'){echo "selected"; }?>>New</option>
						<option value="paid" <?php if($status == 'paid'){echo "selected"; }?>>paid</option>
						<option value="delivered" <?php if($status == 'delivered'){echo "selected"; }?>>Delivered</option>
						<option value="refund" <?php if($status == 'refund'){echo "selected"; }?>>refund</option>
						<option value="ready" <?php if($status == 'ready'){echo "selected"; }?>>ready</option>
						<option value="sent" <?php if($status == 'sent'){echo "selected"; }?>>Sent</option>
						<option value="shipping" <?php if($status == 'shipping'){echo "selected"; }?>>shipping</option>
						<option value="unshippable" <?php if($status == 'unshippable'){echo "selected"; }?>>unshippable</option>
						<option value="checked" <?php if($status == 'checked'){echo "selected"; }?>>checked</option>
						<option value="waiting" <?php if($status == 'waiting'){echo "selected"; }?>>waiting</option>
				</select>
				<input id="searchdate" type="button" value="Search" class="btn btn-info ">
				</p>
		</span>
    </div>

	<div class="mws-panel-toolbar" style="z-index:1;">
		<div class="btn-toolbar">
			<div class="btn-group">
				<a href="<?php echo Url::to('orders/task'); ?>" class="btn<?php echo ($tab=='task')?' active':''; ?>">
					My Tasks
				</a>
				<a href="<?php echo Url::to('orders/all'); ?>" class="btn<?php echo ($tab=='all')?' active':''; ?>">
					All Tasks
				</a>
				<a href="#" class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
				<ul class="dropdown-menu pull-right">
					<li><a href="<?php echo Url::to('orders/status/new'); ?>" class="btn<?php echo ($tab=='new')?' active':''; ?>">
						New
					</a></li>
					<li><a href="<?php echo Url::to('orders/status/paid'); ?>" class="btn<?php echo ($tab=='paid')?' active':''; ?>">
						Paid
					</a></li>
					<li><a href="<?php echo Url::to('orders/status/delivered'); ?>" class="btn<?php echo ($tab=='delivered')?' active':''; ?>">
						Delivered
					</a></li>
					<li><a href="<?php echo Url::to('orders/status/refund'); ?>" class="btn<?php echo ($tab=='refund')?' active':''; ?>">
						Refund
					</a></li>
					<li><a href="<?php echo Url::to('orders/status/ready'); ?>" class="btn<?php echo ($tab=='ready')?' active':''; ?>">
						Ready
					</a></li>
					<li><a href="<?php echo Url::to('orders/status/sent'); ?>" class="btn<?php echo ($tab=='sent')?' active':''; ?>">
						Sent
					</a></li>
					<li><a href="<?php echo Url::to('orders/status/shipping'); ?>" class="btn<?php echo ($tab=='shipping')?' active':''; ?>">
						Shipping
					</a></li>
					<li><a href="<?php echo Url::to('orders/status/unshippable'); ?>" class="btn<?php echo ($tab=='unshippable')?' active':''; ?>">
						Unshippable
					</a></li>
					<li><a href="<?php echo Url::to('orders/status/checked'); ?>" class="btn<?php echo ($tab=='checked')?' active':''; ?>">
						Checked
					</a></li>
					<li><a href="<?php echo Url::to('orders/status/waiting'); ?>" class="btn<?php echo ($tab=='waiting')?' active':''; ?>">
						Waiting
					</a></li>
					<li><a href="<?php echo Url::to('orders/closed'); ?>" class="btn<?php echo ($tab=='closed')?' active':''; ?>">
						Closed
					</a></li>
				</ul>
			</div>
		</div>
	</div>

	<?php echo Theme::widget('WidgetOrderSearchForm', array())->render(); ?>

    <div class="mws-panel-body no-padding">

        <?php echo HTML::message(); ?>

        <table class="mws-datatable-fn mws-table">
            <thead>
                <tr>
					<th width="15%">SLA Time</th>
					<th width="100px">Order ID</th>
					<th width="20%">Customer</th>
					<th width="40%">Items</th>
					<th width="10%">Status</th>
					<?php if ($tab != 'closed'):?>
                    <th width="120px">Action</th>
					<?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (count($orders)) foreach ($orders as $order) : ?>
                    <tr>
						<td class="table-center">
							<?php echo $order->sla_time_at!=false ? $order->sla_time_at->format('j M H:i'): '&nbsp;'; ?>
							<?php
							if($order->sla_time_at!=false)
							{	
								$time_remaining = strtotime($order->sla_time_at) - time();
							?>
								<div class="badge <?php echo ($time_remaining<0 ? 'badge-inverse' : ($time_remaining<30*60 ? 'badge-important' : ($time_remaining<60*60 ? 'badge-warning' : 'badge-info') ) ) ?>"><?php echo $order->sla_time_at->diffForHumans() ?></div>
							<?php 
							}
							?>
							<?php if(count($order->orderNotes)>0): ?>
								<div>
								<?php foreach($order->orderNotes as $orderNote): ?>
									<?php
									if( 
										($user->hasAccess('track-Order.act-as-fulfillment-to') && $orderNote->note_to==='fulfillment') ||
										($user->hasAccess('track-Order.act-as-logistic-to') && $orderNote->note_to==='logistic') ||
										($user->hasAccess('track-Order.act-as-sourcing-to') && $orderNote->note_to==='sourcing') ||
										($user->hasAccess('track-Order.act-as-callcenter-to') && $orderNote->note_to==='callcenter')
									)
									{
										if($orderNote->mentioned_at==false)
											echo '<a href="#order-note-',$orderNote->getKey(),'" class="read-order-note" style="color:#F00;" data-toggle="modal" data-unread="true" data-id="', $orderNote->getKey() ,'"><i class="icon icon-list-2"></i></a> ';
										else
											echo '<a href="#order-note-',$orderNote->getKey(),'" class="read-order-note" style="color:#00F;" data-toggle="modal" data-unread="false" data-id="', $orderNote->getKey() ,'"><i class="icon icon-list-2"></i></a> ';
									}
									else
									{
										echo '<a href="#order-note-',$orderNote->getKey(),'" class="read-order-note" data-toggle="modal" data-id="', $orderNote->getKey() ,'"><i class="icon icon-list-2"></i></a> ';
									}
									?>

									<div style="text-align:left" id="order-note-<?php echo $orderNote->getKey() ?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
										<div class="modal-header">
											<h3>Note <?php echo $orderNote->note_to ? 'to '.ucfirst($orderNote->note_to) : '' ?></h3>
										</div>
										<div class="modal-body">
											<p><?php echo nl2br($orderNote->detail) ?></p>
										</div>
										<div class="modal-footer">
											<a href="/orders/delete-note/<?php echo $orderNote->getKey() ?>"><i class="icon icon-trash"></i></a>
											<button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Close</button>
										</div>
									</div>
								<?php endforeach ?>
								</div>
							<?php endif ?>
						</td>
						<td class="table-center">
							<?php echo $order->order_ref; ?>
						</td>
						<td>
							<div><span class="icon icon-user"></span> <strong><?php echo $order->customer_name; ?></strong></div>
							<div><span class="icon icon-home"></span> <?php echo $order->customer_address; ?></div>
							<div><span class="icon icon-phone"></span> <?php echo $order->customer_tel; ?></div>
							<div><span class="icon icon-envelope"></span> <?php echo $order->customer_email; ?></div>
							<?php //if (($role == 5 || $role == 6) && ! is_null($order->customer_info_modified_at)): ?>
							<div>
								<span class="label label-important">Modified: <?php echo $order->customer_info_modified_at; ?></span>
								<a href="<?php echo Url::to('orders/actions/'.$order->getKey().'/dismiss_contact_modification'); ?>">Dismiss</a>
							</div>
							<?php //endif; ?>
						</td>
						<td>
							<ol class="items">
								<?php foreach ($order->shipmentItems as $item): ?>
								<li>
									<?php echo $item->name; ?>
								</li>
								<?php endforeach; ?>
							</ol>
							<div><strong>Sub Total:</strong> <?php echo number_format($order->sub_total); ?></div>
							<a href="<?php echo Url::to('orders/detail/'.$order->getKey()); ?>" target="_blank">View <?php echo $count = count($order->shipments); ?> <?php echo Str::plural('shipment', $count); ?></a>
						</td>
						<td class="table-center">
							<div><strong><?php echo ucfirst($order->order_status_th); ?></strong></div>
							<div><?php echo $order->updated_at->format('j M H:i'); ?></div>
							<div><span class="label label-info human-time"><?php echo $order->updated_at->diffForHumans(); ?></span></div>
						</td>
						<?php /*
						<td class="table-center">
							<div><strong><?php echo (is_null($order->transaction_time))?'Not paid':'Paid'; ?></strong></div>
							<?php if ( ! is_null($order->transaction_time)): ?>
							<div><?php echo $order->transaction_time->format('j M H:i'); ?></div>
							<div><span class="label label-info human-time"><?php echo $order->transaction_time->diffForHumans(); ?></span></div>
							<?php endif; ?>
						</td>
						*/ ?>
						<?php if ($tab != 'closed'):?>
						<td>
							<?php echo Theme::widget('WidgetOrderStatusButton', compact('user', 'order'))->render(); ?>
						</td>
						<?php endif; ?>
					</tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php

Theme::asset()->container('footer')->writeScript('read-order-note', '
$(function(){
	$(document).on("click", ".read-order-note[data-unread=true]", function(e){
		e.preventDefault();
		$.post("/orders/read-note/"+$(this).data("id"));
	})
})
');

?>
 <script>
	$(function() {
		$('#datepicker').datepicker({ dateFormat: 'yy-mm-dd' }).val();
		$('.datepicker').datepicker({ dateFormat: 'yy-mm-dd' }).val();
	});
	$('#searchdate').click(function(){
		if ( $("#datepicker").val() != '' && $('select[name="statusSelected"] option:selected').val() != '')
		{
			window.location.href = "/orders/datedata/" + $("#datepicker").val() + "/" + $('select[name="statusSelected"] option:selected').val();
		}
	});
</script>
<script>
(function( $, window, document, undefined ) {
    $(document).ready(function() {
        // Data Tables
        if ($.fn.dataTable)
		{
            var table = $(".mws-datatable-fn").dataTable({
                bSort: true,
				aLengthMenu: [
					[30, 100, -1],
					[30, 100, "All"]
				],
				iDisplayLength: 30,
                sPaginationType: "full_numbers",
                 "aoColumns": [
                    { "bVisible": true, "bSearchable": false, "bSortable": true , sClass: "" }, // sla.
                    { "bVisible": true, "bSearchable": true, "bSortable": true , sClass: "" }, // order ref.
					{ "bVisible": true, "bSearchable": true, "bSortable": false , sClass: "" }, // customer
					{ "bVisible": true, "bSearchable": true, "bSortable": false , sClass: "" }, // items
					{ "bVisible": true, "bSearchable": false, "bSortable": true , sClass: "" } // status
					<?php if ($tab != 'closed'):?>
					,{ "bVisible": true, "bSearchable": false, "bSortable": false , sClass: "" } // action
					<?php endif; ?>
//					{ "bVisible": false, "bSearchable": true, "bSortable": false , sClass: "" } // hidden column
                ]
            });

			// order by sla_time_at
			table.fnSort([[0,'asc']]);

//			$(".filter.task").click(function() {
//				table.fnFilter('1', 7);
//			});

        }
    });
}) (jQuery, window, document);
</script>