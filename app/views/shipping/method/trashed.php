<div class="mws-panel grid_8">
	<div class="mws-panel-header">
		<span><i class="icon-table"></i>Shipping Method Management</span>
	</div>

	<div class="mws-panel-toolbar">
		<div class="btn-toolbar">

		</div>
	</div>

	<div class="mws-panel-body no-padding">

		<table class="mws-datatable-fn mws-table">
			<thead>
				<tr>
					<th style="width:30px;">No</th>
					<th><?php echo ShippingMethod::getLabel('name') ?></th>
					<th style="width:135px;" class="no_sort"><?php echo ShippingMethod::getLabel('max_weight') ?></th>
					<th style="width:300px;" class="no_sort">Max Dimension</th>
					<th style="width:300px;" class="no_sort"><?php echo ShippingMethod::getLabel('delivery_area') ?></th>
					<th style="width:135px;" class="no_sort">Action</th>
				</tr>
			</thead>
			<tbody>        
			<?php if (!$methods->isEmpty()) { ?>    
				<?php foreach ($methods as $key => $method) { ?>
					<tr>
						<td><?php echo $key+1; ?></td>
						<td><?php echo $method->name; ?></td>
						<td><?php echo $method->max_weight > 0 ? ($method->max_weight/1000).' kg' : 'no limit' ; ?></td>
						<td><?php echo ( $method->dimension_max > 0 AND $method->dimension_mid > 0 AND $method->dimension_min > 0 ) ? $method->dimension_max.' cm x '.$method->dimension_mid.' cm x '.$method->dimension_min.' cm' : 'no limit'; ?></td>
						<td><?php
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
						<td>
							<a href="<?php echo URL::to("shipping/method/undo/{$method->id}"); ?>" title="Restore"><i class="icol-arrow-undo"></i> Restore</a>&nbsp;&nbsp;&nbsp;
						</td>
					</tr>
				<?php } ?>   
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
					{ "bVisible": true, "bSearchable": true, "bSortable": false , sClass: "alignCenter" },
					{ "bVisible": true, "bSearchable": true, "bSortable": true , sClass: "alignCenter" },
				]
			});
		}
	});
}) (jQuery, window, document);

  
</script>
