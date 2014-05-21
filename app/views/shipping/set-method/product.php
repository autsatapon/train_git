<div class="mws-panel grid_8">
	<div class="mws-panel-header">
		<span><i class="icon-table"></i> Set Shipping Method by Product</span>
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
					<th>Product Name</th>
                    <th style="width:135px;">Brand</th>
					<th style="width:240px;" class="no_sort">Available Shipping Method</th>
					<th style="width:135px;" class="no_sort">Action</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($products as $product) { ?>
					<?php 
						if ($product->methods->isEmpty())
						{
							$productShippingMethods = '-';
						}
						else
						{
							$rs = array();
							foreach ($product->methods as $val)
							{
                                $rs[] = $val->name;
							}

							$productShippingMethods = implode(', ', $rs);
						}
					?>
					<tr>
                        <td><?php echo $product->title ?></td>
						<td><?php echo $product->brand->name ?></td>
						<td><?php echo $productShippingMethods ?></td>
						<td><a href="<?php echo URL::to("shipping/set-method/product/{$product->id}") ?>">Set Shipping Method</a></td>
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
                    { "bVisible": true, "bSearchable": true, "bSortable": true , sClass: "" },
                    { "bVisible": true, "bSearchable": true, "bSortable": true , sClass: "alignCenter" },
                    { "bVisible": true, "bSearchable": true, "bSortable": true , sClass: "alignCenter" },
                    { "bVisible": true, "bSearchable": false, "bSortable": false , sClass: "alignCenter" }
                ]
            });
        }
    });
}) (jQuery, window, document);
</script>