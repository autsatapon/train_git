<div class="mws-panel grid_8">
    <div class="mws-panel-header">
         <span><i class="icon-table"></i> Set Product Tag </span>
    </div>

	<?php echo Theme::widget('WidgetProductSearchForm', array())->render(); ?>

    <div class="mws-panel-body no-padding dataTables_wrapper">

    	<?php echo HTML::message() ?>

		<?php if ( !empty($products) && (is_object($products) && (!$products->isEmpty())) ) { ?>

        <table class="mws-datatable-fn mws-table">
            <thead>
                <tr>
					<th style="width:150px;">Brand</th>
					<th >Product</th>
					<th >Tag</th>
					<th style="width:150px;">Action</th>
                </tr>
            </thead>
            <tbody>

				<?php foreach ($products as $product) { ?>
					<tr style="border-top:3px solid #4D4D4D;border-bottom:1px solid #4D4D4D;margin-top:10px;">
    					 <td style="background-color:#D7D7D7;width:100px"><?php echo $product->brand->name ?></td>
    					 <td><strong><?php echo $product->title ?></strong></td>
    					 <td><?php echo $product->tag ?></td>
    					 <td style="text-align:center;">
    					 	<a href="<?php echo URL::to('products/set-tag/edit/'.$product->id); ?>" ><input type="button" class="btn btn-warning" value="Edit"></a>
    					 	<!-- <a href="<?php echo URL::to('products/delete/'.$product->id); ?>"><input type="button" class="btn btn-danger" value="Delete"></a> -->
    					 </td>
					</tr>
						<?php foreach ($product->variants as $row => $variant) { ?>

								 <tr>
								    <?php if ($row == 0): ?>
                                       <td class="table-center" rowspan="<?php echo $product->variants->count(); ?>"><img src="<?php echo $product->image ?>"></td>
                                    <?php endif; ?>
									 <td>- <?php echo $variant->title ?></td>
									 <td></td>
									 <td></td>
								</tr>

						<?php } ?>
					<tr style="border-top:1px solid #999999">

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
						<th style="width:150px;">Brand</th>
						<th >Product</th>
						<th >Tag</th>
						<th style="width:150px;">Action</th>
	                </tr>
	            </thead>
	            <tbody>
					<td class="table-center" colspan="4">ไม่พบข้อมูล</td>
	            </tbody>
		        </table>

        <?php } ?>


    </div>
</div>

