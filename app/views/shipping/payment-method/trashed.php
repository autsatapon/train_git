<div class="mws-panel grid_8">
	<div class="mws-panel-header">
		<span><i class="icon-table"></i>Trashed Payment Method Management</span>
	</div>

	<div class="mws-panel-toolbar">
		<div class="btn-toolbar">
			<div class="btn-group">
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
						<td><?php echo $payment_method->channel; ?></td>
						<td><?php echo number_format( $payment_method->transaction_fee, 2 ); ?></td>
						<td><?php echo $payment_method->transaction_apply; ?></td>
						<td>
							<a href="<?php echo URL::to("shipping/payment-methods/undo/{$payment_method->id}"); ?>" title="Restore"><i class="icol-arrow-undo"></i> Restore</a>&nbsp;&nbsp;&nbsp;
						</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>

<script>
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
					{ "bVisible": true, "bSearchable": false, "bSortable": false , sClass: "alignCenter" }
				]
			});
		}
	});
}) (jQuery, window, document);
  
</script>