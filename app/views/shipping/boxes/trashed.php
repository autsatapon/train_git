<div class="mws-panel grid_8">
	<div class="mws-panel-header">
		<span><i class="icon-table"></i>Trashed Box Management</span>
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
					<th><?php echo ShippingBox::getLabel('name') ?></th>
					<th style="width:180px;"><?php echo ShippingBox::getLabel('weight') ?></th>
					<th style="width:135px;" class="no_sort"><?php echo ShippingBox::getLabel('price') ?></th>
					<th style="width:135px;" class="no_sort">Action</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($boxes as $key => $box) { ?>
					<tr>
						<td><?php echo $key+1; ?></td>
						<td><?php echo $box->name; ?></td>
						<td><?php echo $box->weight; ?></td>
						<td><?php echo number_format($box->price,2);?></td>
						<td>
							<a href="<?php echo URL::to("shipping/boxes/undo/{$box->id}"); ?>" title="Restore"><i class="icol-arrow-undo"></i> Restore</a>&nbsp;&nbsp;&nbsp;

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
					{ "bVisible": true, "bSearchable": false, "bSortable": false , sClass: "alignCenter" }
				]
			});
		}
	});
}) (jQuery, window, document);
  
</script>