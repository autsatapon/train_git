<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-table"></i> Set Product Price </span>
    </div>

	<?php echo Theme::widget('WidgetProductSearchForm', array())->render(); ?>

    <div class="mws-panel-body no-padding dataTables_wrapper">

		<?php if ( !empty($products) && (is_object($products) && (!$products->isEmpty())) ) { ?>

        <table class="mws-datatable-fn mws-table">
            <thead>
                <tr>
					 <th>Brand</th>
					 <th style="width:50%;"><?php echo Product::getLabel('title') ?></th>
					 <th style="background-color:#DDDDDD"><?php echo Product::getLabel('inventory_id') ?></th>
					 <th><?php echo ProductVariant::getLabel('net_price') ?></th>
					 <th><?php echo ProductVariant::getLabel('special_price') ?></th>
					 <th><?php echo ProductVariant::getLabel('allow_installment') ?></th>
					 <th></th>
                </tr>
            </thead>
            <tbody>

				<?php foreach ($products as $product) { ?>
					<tr style="border-top:3px solid #4D4D4D;border-bottom:1px solid #4D4D4D;margin-top:10px;">
						 <td colspan="1" style="background-color:#D7D7D7;width:100px"><?php echo $product->brand->name ?></td>
						 <td colspan="4"><strong><?php echo $product->title ?></strong></td>
						 <td class="table-center"><?php echo $product->allow_installment ? 'Allow' : '-' ?></td>
						 <td colspan="1" style="text-align:center;">
						 	<a href="/products/set-price/edit/<?php echo $product->id ?>" ><input type="button" class="btn btn-warning" value="Edit"></a>
						 </td>
					</tr>
						<?php foreach ($product->variants as $row => $variant) { ?>
							<?php // foreach ($vData as $vkey => $vvData) { ?>
								 <tr>
                                    <?php if ($row == 0): ?>
									   <td class="table-center" rowspan="<?php echo $product->variants->count(); ?>"><img src="<?php echo $product->image ?>"></td>
                                    <?php endif; ?>
									<td>- <?php echo $variant->title ?></td>
									<td class="table-center" style="background-color:#DDDDDD;border-bottom:1px solid #4D4D4D;"> <?php echo $variant->inventory_id; ?> </td>
									
									<?php if($variant->price==0){ ?>
										<td class="table-center"> <strong><u><?php echo number_format(($variant->normal_price), 2); ?></u></strong></td>
										<td class="table-center"><?php echo number_format(($variant->price), 2); ?> </td>
									<?php }else{ ?>
										<?php if($variant->normal_price == 0){ ?>
												<td class="table-center"> <strong><u><?php echo number_format(($variant->price), 2); ?></u></strong> </td>
												<td class="table-center"> <?php echo $variant->normal_price > 0 ? number_format(($variant->normal_price), 2) : ''; ?> </td>
										<?php }else{ ?>
												<td class="table-center"> <?php echo number_format(($variant->normal_price), 2); ?> </td>
												<td class="table-center"> <strong><u><?php echo $variant->price > 0 ? number_format(($variant->price), 2) : ''; ?></u></strong> </td>
										<?php } ?>
									<?php } ?>
									
									<td class="table-center"><?php echo $variant->allow_installment ? 'Allow' : '-' ?></td>
									<td>&nbsp;</td>
								</tr>
							<?php // } ?>
						<?php } ?>
					<tr style="border-top:1px solid #999999">
						<td colspan="7"></td>
					</tr>
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
						 <th>Brand</th>
						 <th style="width:50%;"><?php echo Product::getLabel('title') ?></th>
						 <th style="background-color:#DDDDDD"><?php echo Product::getLabel('retail_price') ?></th>
						 <th><?php echo ProductVariant::getLabel('net_price') ?></th>
						 <th><?php echo ProductVariant::getLabel('special_price') ?></th>
						 <th><?php echo ProductVariant::getLabel('allow_installment') ?></th>

	                </tr>
	            </thead>
		            <tbody>
						<td class="table-center" colspan="6">ไม่พบข้อมูล</td>
		            </tbody>
		        </table>

        <?php } ?>


    </div>
</div>

