<div class="mws-panel grid_8">
	<div class="mws-panel-header">
		<span><i class="icon-table"></i>Payment Method Management</span>
	</div>

	<div class="mws-panel-toolbar">
		<div class="btn-toolbar">
			<div class="btn-group">
				<a class="btn" href="<?php echo URL::to('shipping/payment-methods/create'); ?>"><i class="icol-add"></i> Create Payment Method</a>
			</div>
		</div>
	</div>

	<div class="mws-panel-body no-padding">
		<table class="mws-datatable-fn mws-table">
			<thead>
				<tr>
					<th style="width:30px;">No</th>
					<th><?php echo PaymentMethod::getLabel('name'); ?></th>
					<th style="width:100px;"><?php echo PaymentMethod::getLabel('code'); ?></th>
					<th style="width:80px;"><?php echo PaymentMethod::getLabel('channel'); ?></th>
					<th style="width:50px;"><?php echo PaymentMethod::getLabel('transaction_fee'); ?></th>
					<th style="width:50px;"><?php echo PaymentMethod::getLabel('transaction_apply'); ?></th>
					<th style="width:180px;" class="no_sort">Action</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($payment_methods as $key => $payment_method) { ?>
					<tr>
						<td><?php echo $key+1; ?></td>
						<td><?php echo $payment_method->name; ?></td>
						<td><?php echo $payment_method->code; ?></td>
						<td><?php echo strtoupper($payment_method->channel); ?></td>
						<td><?php echo number_format( $payment_method->transaction_fee, 2 ); ?></td>
						<td><?php echo $payment_method->transaction_apply; ?></td>
						<td>
							<a href="<?php echo URL::to("shipping/payment-methods/edit/{$payment_method->id}"); ?>" title="edit"><i class="icol-pencil"></i> Edit</a>&nbsp;&nbsp;&nbsp;
							<a href="<?php echo URL::to("shipping/payment-methods/delete/{$payment_method->id}"); ?>" class="delete-box" title="delete"><i class="icol-cross"></i> Delete</a>
						</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		<a href="<?php echo URL::to("shipping/payment-methods/trashed/"); ?>" style="float: right; padding: 10px;" ><i class="icol32-bin-closed"></i> View Trash</a>
	</div>
</div>

<script>
$(function(){
	$(document).on('click','.delete-box',function(e)
	{
		return confirm('Do you want to delete this box?');
	});
});

(function( $, window, document, undefined ) {
	$(document).ready(function() {
		// Data Tables
		if( $.fn.dataTable ) {
			$(".mws-datatable-fn").dataTable({
				bSort: true,
				sPaginationType: "full_numbers",
				 "aoColumns": [
					{ "bVisible": true, "bSearchable": false, "bSortable": true , sClass: "alignCenter" },
					{ "bVisible": true, "bSearchable": true, "bSortable": true , sClass: "alignLeft" },
					{ "bVisible": true, "bSearchable": true, "bSortable": true , sClass: "alignCenter" },
					{ "bVisible": true, "bSearchable": true, "bSortable": true , sClass: "alignCenter" },
					{ "bVisible": true, "bSearchable": false, "bSortable": false , sClass: "alignCenter" },
					{ "bVisible": true, "bSearchable": false, "bSortable": false , sClass: "alignCenter" },
					{ "bVisible": true, "bSearchable": false, "bSortable": false , sClass: "alignCenter" },
				]
			});
		}
	});
}) (jQuery, window, document);

  
</script>