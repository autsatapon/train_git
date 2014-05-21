<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-table"></i> Set Variant Style </span>
    </div>

	<?php echo Theme::widget('WidgetProductSearchForm', array())->render(); ?>

    <div class="mws-panel-body no-padding">


        <table class="mws-datatable-fn mws-table">
            <thead>
                <tr>
					<th class="span1" rowspan="2"><?php echo Brand::getLabel('name') ?></th>
					<th class="span2" rowspan="2"><?php echo Product::getLabel('title') ?></th>
					<th class="span1" rowspan="2"><?php echo Product::getLabel('pkey'),'/',ProductVariant::getLabel('inventory_id') ?></th>
					<th rowspan="2">Actions</th>
                </tr>
            </thead>
            <tbody>

				<?php foreach ($products as $product) { ?>
					<tr>
						 <td style="background-color:#D7D7D7;width:100px"><?php echo $product->brand->name ?></td>
						 <td><strong><?php echo $product->title ?></strong></td>
						 <td class="table-center"><?php echo $product->pkey ?></td>
						 <td class="table-center">
						 	<a href="/products/set-variant-style/edit/<?php echo $product->id ?>" ><input type="button" class="btn btn-warning" value="Edit"></a>
						 </td>
					</tr>
						<?php foreach ($product->variants as $variant) { ?>
							<?php // foreach ($vData as $vkey => $vvData) { ?>
								 <tr>
									 <td>&nbsp;</td>
									 <td>- <?php echo $variant->title ?></td>
									 <td class="table-center"><?php echo $variant->inventory_id ?></td>
									 <td>&nbsp;</td>
								</tr>
							<?php // } ?>
						<?php } ?>
					<tr style="border-top:1px solid #999999">

					</tr>
              	 <?php } ?>
            </tbody>
        </table>
    </div>
</div>

