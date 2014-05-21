<div class="mws-panel grid_8">
	<div class="mws-panel-header">
		<span><i class="icon-table"></i>Shipping Box Management</span>
	</div>

	<div class="mws-panel-toolbar">
		<div class="btn-toolbar">
			<div class="btn-group">
				<a class="btn" href="<?php echo URL::to('shipping/boxes/create'); ?>"><i class="icol-add"></i> Create Shipping Box</a>
			</div>
		</div>
	</div>

	<div class="mws-panel-body no-padding">
		<table class="mws-datatable-fn mws-table">
			<thead>
				<tr>
					<th style="width:30px;">No</th>
					<th><?php echo ShippingBox::getLabel('name'); ?></th>
					<th style="width:180px;"><?php echo ShippingBox::getLabel('weight'); ?></th>
					<th style="width:135px;" class="no_sort"><?php echo ShippingBox::getLabel('price'); ?></th>
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
							<a href="<?php echo URL::to("shipping/boxes/edit/{$box->id}"); ?>" title="edit"><i class="icol-pencil"></i> Edit</a>&nbsp;&nbsp;&nbsp;
							<a href="<?php echo URL::to("shipping/boxes/delete/{$box->id}"); ?>" class="delete-box" title="delete"><i class="icol-cross"></i> Delete</a>  

						</td>
					</tr>
				<?php } ?>      
			</tbody>
		</table>
		<input type="hidden" id="boxes-id" name="boxes-id" value="<?php echo $box->id;?>" >
		<a href="<?php echo URL::to("shipping/boxes/trashed/"); ?>" style="float: right; padding: 10px;" ><i class="icol32-bin-closed"></i> View Trash</a>
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
					{ "bVisible": true, "bSearchable": false, "bSortable": false , sClass: "alignCenter" }
				]
			});
		}
	});
}) (jQuery, window, document);

  
</script>