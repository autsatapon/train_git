<div class="mws-panel grid_8">
	<div class="mws-panel-header">
		<span><i class="icon-table"></i>Shipping Method Management</span>
	</div>

	<div class="mws-panel-toolbar">
		<div class="btn-toolbar">
			<div class="btn-group">
				<a class="btn" href="<?php echo URL::to('shipping/method/create') ?>"><i class="icol-add"></i> Create Shipping Method</a>
			</div>
		</div>
	</div>

	<div class="mws-panel-body no-padding">
		<table class="mws-datatable-fn mws-table">
			<thead>
				<tr>
					<th style="width:30px;">No</th>
					<th style="width:100px;"><?php echo ShippingMethod::getLabel('name') ?></th>
					<th style="width:200px;" class="no_sort">Description</th>
					<th style="width:200px;" class="no_sort"><?php echo ShippingMethod::getLabel('delivery_area') ?></th>
					<th style="width:200px;" class="no_sort"><?php echo ShippingMethod::getLabel('allow_nonstock') ?></th>
					<th style="width:100px;" class="no_sort"><?php echo ShippingMethod::getLabel('tracking_url') ?></th>
					<th style="width:135px;" class="no_sort">Action</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($methods as $key => $method) { ?>
					<tr>
						<td class="table-center"><?php echo $key+1; ?></td>
						<td class="table-center"><?php echo $method->name; ?></td>
						<td class="table-center"><?php echo $method->description; ?></td>
						<td class="table-center"><?php
							$deliveryAreas = $method->deliveryAreas;
							if($deliveryAreas != false)
							{
								$areasLinks = array();
								foreach($deliveryAreas as $area)
								{
									array_push($areasLinks, HTML::link( URL::to('shipping/method/set-fee/'.$area->pivot->id), $area->name ));
								}
								echo implode(' / ', $areasLinks);
							}
						?></td>
						<td class="table-center"><?php echo $method->allow_nonstock ? 'Yes' : 'No' ?></td>
						<td class="table-center"><?php
								if(isset($method->tracking_url))
								{
									echo '<a href="'.$method->tracking_url.'" target="_blank"><i class="icon icon-link"></i></a>';
								}
							?>
						</td>
						<td class="table-center">
                     <a href="<?php echo URL::to("shipping/method/edit/{$method->id}"); ?>" title="edit"><i class="icol-pencil"></i> Edit</a>&nbsp;&nbsp;&nbsp;
                     <?php if ($method->slug != Order::COD && $method->slug != Order::FREE_SHIPPING): ?>
							<a href="<?php echo URL::to("shipping/method/delete/{$method->id}"); ?>" class="delete-method" title="delete"><i class="icol-cross"></i> Delete</a>
                     <?php endif; ?>
						</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		<input type="hidden" id="method-id" name="method-id" value="<?php echo $method->id;?>" >
		<a href="<?php echo URL::to("shipping/method/trashed/"); ?>" style="float: right; padding: 10px;" ><i class="icol32-bin-closed"></i> View Trash</a>
	</div>
</div>

<script>
$(function(){
	$(document).on('click','.delete-method',function(e)
	{
		return confirm('Do you want to delete this method?');
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
					{ "bVisible": true, "bSearchable": true, "bSortable": true , sClass: "alignCenter" },
					{ "bVisible": true, "bSearchable": true, "bSortable": false , sClass: "alignCenter" },
					{ "bVisible": true, "bSearchable": true, "bSortable": false , sClass: "alignCenter" },
					{ "bVisible": true, "bSearchable": true, "bSortable": false , sClass: "alignCenter" },
					{ "bVisible": true, "bSearchable": true, "bSortable": true , sClass: "alignCenter" },
				]
			});
		}
	});
}) (jQuery, window, document);


</script>
