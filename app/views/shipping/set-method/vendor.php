<div class="mws-panel grid_8">
	<div class="mws-panel-header">
		<span><i class="icon-table"></i> Set Shipping Method by Vendor</span>
	</div>

	<div class="mws-panel-body no-padding">

		<?php if(Session::has('success')) { ?>
			<div class="alert alert-success">
				<p><?php echo Session::get('success') ?></p>
			</div>
		<?php } ?>

		<table class="mws-datatable-fn mws-table">
			<thead>
				<tr>
					<?php /* <th>Vendor ID</th> */ ?>
					<th>Vendor Detail</th>
					<th style="width:240px;" class="no_sort">Available Shipping Method</th>
					<th style="width:135px;" class="no_sort">Action</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($vendors as $vendor) { ?>
					<?php 
						if ($vendor->methods->isEmpty())
						{
							$vendorShippingMethods = '-';
						}
						else
						{
							$rs = array();
							foreach ($vendor->methods as $val)
							{
                                $rs[] = $val->name;
							}

							$vendorShippingMethods = implode(', ', $rs);
						}
					?>
					<tr>
						<?php /* <td><?php echo $vendor->vendor_id ?></td> */ ?>
						<td><?php echo $vendor->vendor_detail ?></td>
						<td><?php echo $vendorShippingMethods ?></td>
						<td><a href="<?php echo URL::to("shipping/set-method/vendor/{$vendor->vendor_id}") ?>">Set Shipping Method</a></td>
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
                 	<?php /* { "bVisible": true, "bSearchable": false, "bSortable": true , sClass: "alignCenter" }, */ ?>
                    { "bVisible": true, "bSearchable": true, "bSortable": true , sClass: "" },
                    { "bVisible": true, "bSearchable": true, "bSortable": true , sClass: "alignCenter" },
                    { "bVisible": true, "bSearchable": false, "bSortable": false , sClass: "alignCenter" }
                ]
            });
        }
    });
}) (jQuery, window, document);
</script>