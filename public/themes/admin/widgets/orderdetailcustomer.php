<div class="mws-panel-body no-padding">

	<table class="mws-datatable-fn mws-table customer">
		<thead>
			<tr>
				<th width="10%">Order Ref.</th>
				<th>Customer</th>
				<th width="15%">Sub Total</th>
				<th width="15%">Status</th>
				<th width="15%">Payment</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="table-center">
					<?php echo $order->order_ref; ?>
				</td>
				<td>
				<?php if ($callcenter) echo Form::open(); ?>
					<?php if ($callcenter): ?>
					<div>
						<span class="icon icon-user"></span>
						<?php echo Form::text('order['.$order->getKey().'][customer_name]', $order->customer_name); ?>
					</div>
					<div>
						<span class="icon icon-home"></span>
						<?php echo Form::textarea('order['.$order->getKey().'][customer_address]', $order->customer_address); ?><br />
						<?php echo Form::text('order['.$order->getKey().'][customer_province]', $order->customer_province, array('style' => 'margin-left: 15px;')); ?><br />
						<?php echo Form::text('order['.$order->getKey().'][customer_postcode]', $order->customer_postcode, array('style' => 'margin-left: 15px;')); ?>
					</div>
					<div>
						<span class="icon icon-phone"></span>
						<?php echo Form::text('order['.$order->getKey().'][customer_tel]', $order->customer_tel); ?>
					</div>
					<div>
						<span class="icon icon-envelope"></span> <?php echo $order->customer_email; ?>
					</div>
					<div class="pull-right">
						<?php echo Form::submit('Save', array('class' => 'btn btn-primary')); ?>
					</div>
					<?php else: ?>
					<div>
						<span class="icon icon-user"></span> <strong><?php echo $order->customer_name; ?></strong>
					</div>
					<div>
						<span class="icon icon-home"></span>
						<?php echo $order->customer_address; ?>
						<?php echo $order->customer_province; ?>
						<?php echo $order->customer_postcode; ?>
					</div>
					<div>
						<span class="icon icon-phone"></span> <?php echo $order->customer_tel; ?>
					</div>
					<div>
						<span class="icon icon-envelope"></span> <?php echo $order->customer_email; ?>
					</div>
					<?php if (($user->hasAccess('track-Order.act-as-sourcing-to') || $user->hasAccess('track-Order.act-as-logistic-to')) && ! is_null($order->customer_info_modified_at)): ?>
					<div>
						<span class="label label-important">Modified: <?php echo $order->customer_info_modified_at; ?></span>
						<a href="<?php echo Url::to('orders/actions/'.$order->getKey().'/dismiss_contact_modification'); ?>">Dismiss</a>
					</div>
					<?php endif; ?>
					<?php endif; ?>
				<?php if ($callcenter) echo Form::close(); ?>
				</td>
				<td class="table-center">
					<?php echo number_format($order->sub_total); ?>
				</td>
				<td class="table-center">
					<div><strong><?php echo ucfirst($order->order_status_th); ?></strong></div>
					<div><?php echo $order->updated_at->format('j/M H:i'); ?></div>
					<div><span class="label label-info human-time"><?php echo $order->updated_at->diffForHumans(); ?></span></div>
				</td>
				<td class="table-center">
					<div><strong><?php //echo (is_null($order->transaction_time))?'Not paid':'Paid'; ?><?php echo ucfirst($order->payment_status); ?></strong></div>
					<?php if ( ! is_null($order->transaction_time)): ?>
					<div><?php echo $order->transaction_time->format('j/M H:i'); ?></div>
					<div><span class="label label-info human-time"><?php echo $order->transaction_time->diffForHumans(); ?></span></div>
					<div><?php echo $order->payment_channel; ?></div>
					<?php endif; ?>
				</td>
			</tr>
				<tr>
				<td>&nbsp;</td>
				<td>

					<?php if($fulfillment){ ?>
							<div style="float:left;width:80%;text-align:left;">
									<strong>Invoice :</strong> <?php echo $order->invoice ?>
									<div id="invoice" class="hide" style="margin-top:10px;">
										<form method="post" action="/orders/invoice/<?php echo $order->getKey(); ?>">
											<div>
												<label>Invoice: </label>
												<input type="text" name="invoiceCode" value="<?php echo $order->invoice ?>" style="margin-bottom:0;width:200px;"> 
												<input type="submit" value="Save" class="btn btn-primary">
											</div>
										</form>
									</div>
							</div>
							<div style="float:left;width:10%;text-align:right;">
									<a href="javascript:void(0)" onClick="$('#invoice').toggle()"><input type="submit" value="Edit Invoice" class="btn btn-primary"></a>
							</div>
					<?php }else{ 
							echo "<strong>Invoice :</strong> ".$order->invoice;
					 }?>
				</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
					<?php
					if(count($order->orderNotes)>0)
					{
						echo '<div>';
						$user = Sentry::getUser();
						foreach($order->orderNotes as $orderNote)
						{
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
						}
						echo '</div>';
					}
					?>
					<a href="#" onClick="$('#order-note').toggle()">Add Note</a>
					<div id="order-note" class="hide">
						<form method="post" action="/orders/note/<?php echo $order->getKey(); ?>">
							<div>
								<label>Note to: </label>
								<select name="note-to">
									<option value="">All</option>
									<option value="fulfillment">Fulfillment</option>
									<option value="logistic">Logistic</option>
									<option value="sourcing">Sourcing</option>
									<option value="callcenter">Call Center</option>
								</select>
							</div>
							<div>
								<label>Message: </label>
								<textarea name="note-message"></textarea>
							</div>
							<div>
								<label></label>
								<input type="submit" value="Save" class="btn btn-primary">
							</div>
						</form>
					</div>
				</td>
				<td>&nbsp;</td>
				<td>
					<?php echo Theme::widget('WidgetOrderStatusButton', compact('user', 'order'))->render(); ?>
				</td>
				<td></td>
			</tr>
		
		</tbody>
	</table>

</div>

<?php if(count($order->orderNotes)>0): ?>
	<?php foreach($order->orderNotes as $orderNote): ?>
<div id="order-note-<?php echo $orderNote->getKey() ?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
<?php endif ?>

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