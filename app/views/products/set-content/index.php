<div class="grid_8">
	<div class="pull-right">
		<a href="<?php echo URL::to('/products/set-content/bulk-upload') ?>"><i class="icon icon-upload"></i> Upload multiple images</a>
	</div>
</div>

<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-table"></i> Set Product Content </span>
    </div>

	<?php echo Theme::widget('WidgetProductSearchForm', array())->render(); ?>

    <div class="mws-panel-body no-padding dataTables_wrapper">

		
		<?php if ( !empty($products) && (is_object($products) && (!$products->isEmpty())) ) { ?>

        <table class="mws-datatable-fn mws-table">
            <thead>
                <tr>
					<th class="span2" rowspan="2"><?php echo Product::getLabel('title') ?></th>
					<th class="span1" rowspan="2">Styles</th>
					<th class="span1" rowspan="2"><?php echo Product::getLabel('pkey'),'/',ProductVariant::getLabel('inventory_id') ?></th>
					<th colspan="3">Contents</th>
					<th rowspan="2">Actions</th>
                </tr>
                <tr>
                	<th class="span1"><?php echo Product::getLabel('key_feature') ?></th>
                	<th class="span1"><?php echo Product::getLabel('description') ?></th>
                	<th class="span1">Media</th>
                </tr>
            </thead>
            <tbody>

				<?php foreach ($products as $product) { ?>
					<tr style="border-top:5px solid #000;">
						 <td rowspan="<?php echo count($product->variants)+1 ?>" class="table-center" style="vertical-align:top"><h4><?php echo $product->title ?></h4><img src="<?php echo $product->image ?>"></td>
						 <td></td>
						 <td class="table-center"><?php echo $product->pkey ?></td>
						 <td class="table-center"><?php echo $product->key_feature!=false ? '<i class="icon-ok"></i>' : '-' ?></td>
						 <td class="table-center"><?php echo $product->description!=false ? '<i class="icon-ok"></i>' : '-' ?></td>
						 <td class="table-center"><?php echo count($product->mediaContents)>0 ? count($product->mediaContents) : '-' ?></td>
						 <td class="table-center">
						 	<a href="/products/set-content/edit/<?php echo $product->id ?>" ><input type="button" class="btn btn-warning" value="Edit"></a>
						 </td>
					</tr>

						<?php foreach ($product->variants as $variant) { ?>
							<?php // foreach ($vData as $vkey => $vvData) { ?>
								 <tr>
									 <td>- <?php echo $variant->title ?></td>
									 <td class="table-center"><?php echo $variant->inventory_id ?></td>
									 <td colspan="3">&nbsp;</td>
									 <td>&nbsp;</td>
								</tr>

							<?php // } ?>
						<?php } ?>

              	 <?php } ?>
            </tbody>
        </table>

            <?php
                $query = array(
                    'product' => Input::get('product'),
                    'product_line' => Input::get('product_line'),
                    'brand' => Input::get('brand'),
                    'tag' => Input::get('tag'),
                    'has_product_content' => Input::get('has_product_content'),
                    'has_product_mediacontent' => Input::get('has_product_mediacontent'),
                    'product_allow_installment' => Input::get('product_allow_installment'),
                    'variant_allow_installment' => Input::get('variant_allow_installment'),
                    'product_allow_cod' => Input::get('product_allow_cod'),
                    'has_price' => Input::get('has_price'),
                    );
                echo $products->appends($query)->links();
            ?>
            
        <?php }else{ ?>

		        <table class="mws-datatable-fn mws-table">
	            <thead>
	                <tr>
						<th class="span2" rowspan="2"><?php echo Product::getLabel('title') ?></th>
						<th class="span1" rowspan="2">Styles</th>
						<th class="span1" rowspan="2"><?php echo Product::getLabel('pkey'),'/',ProductVariant::getLabel('inventory_id') ?></th>
						<th colspan="3">Contents</th>
						<th rowspan="2">Actions</th>
	                </tr>
	                <tr>
	                	<th class="span1"><?php echo Product::getLabel('key_feature') ?></th>
	                	<th class="span1"><?php echo Product::getLabel('description') ?></th>
	                	<th class="span1">Media</th>
	                </tr>
	            </thead>
		            <tbody>
						<td class="table-center" colspan="7">ไม่พบข้อมูล</td>
		            </tbody>
		        </table>

        <?php } ?>


    </div>
</div>

