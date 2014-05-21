<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-table"></i> Approve Product</span>
    </div>

    <?php echo Theme::widget('WidgetProductSearchForm', array())->render(); ?>

    <div class="mws-panel-body no-padding dataTables_wrapper">

		<?php if ( !empty($products) && (is_object($products) && (!$products->isEmpty())) ) { ?>

        <table class="mws-datatable-fn mws-table">
            <thead>
                <tr>
					<th style="width:100px;">Brand</th>
					<th>Product</th>
					<th style="width:50px;">Status</th>
					<th style="width:100px;">Actions</th>
                </tr>
            </thead>
            <tbody>

				<?php foreach ($products as $product) { ?>
					<?php foreach ($product->revisions as $key=>$revision) { ?>
						<?php
							if ( $revision->status == 'publish')
								continue;

							$modifiedData = json_decode($revision->value, TRUE);
							$modifiedFields = array_keys($modifiedData);

							foreach ($modifiedFields as $key=>$val)
							{
								$modifiedFields[$key] = Product::getLabel($val);
							}

							$textClass = '';
							if ($revision->status == 'approved')
							{
								$textClass = 'text-success';
							}
							elseif ($revision->status == 'rejected')
							{
								$textClass = 'text-error';
							}
						?>
						<tr style="border-top:3px solid #000">
							 <td class="table-center">
							 	<p><strong><?php echo $product->brand->name ?></strong></p>
                                <img src="<?php echo $product->image ?>">
							 </td>
							 <td>
							 	<p><strong><?php echo $product->title ?></strong></p>
							 	<p class="text-success">Modified Fields : <?php echo implode(', ', $modifiedFields) ?></p>
							 	<?php if ( $revision->note != '' ) { ?>
							 	<p class="text-warning">Note : <?php echo $revision->note ?></p>
							 	<?php } ?>
							 	<p class="muted">Editor : <?php echo User::find($revision->editor_id)->display_name ?></p>
							 </td>
							 <td class="table-center">
							 	<span class="<?php echo $textClass ?>">
							 		<?php echo $revision->status ?>
							 	</span>
							 </td>
							 <td class="table-center">
							 	<a href="<?php echo URL::to("products/approve/detail/{$product->id}/{$revision->id}") ?>" class="btn btn-info">Detail</a>
							 	<?php /* if ($revision->status == 'approved') { ?>
							 		<a href="#publish" class="btn btn-success">Publish</a>
							 	<?php } */ ?>
							 	<?php /*
							 	<a href="#" class="btn btn-success">Approve</a>
							 	<a href="#" class="btn btn-danger">Reject</a>
							 	*/ ?>
							 	<?php /* <a href="#" class="btn btn-warning">Edit</a> */ ?>
							 </td>
						</tr>
					<?php } ?>

						<?php /* foreach ($product->variants as $variant) { ?>
							<?php // foreach ($vData as $vkey => $vvData) { ?>
								 <tr>
									 <td>&nbsp;</td>
									 <td>- <?php echo $variant->title ?></td>
									 <td></td>
									 <td></td>
									 <td></td>
									 <td>

									 </td>
								</tr>
							<?php // } ?>
						<?php } */ ?>

					<tr style="border-top:3px solid #000;border-bottom:1px solid #4D4D4D;margin-top:10px;">

					</tr>
              	 <?php } ?>
            </tbody>
        </table>

            <?php
                // $query = array(
                //     'product' => Input::get('product'),
                //     'product_line' => Input::get('product_line'),
                //     'tag' => Input::get('tag'),
                //     'has_product_content' => Input::get('has_product_content'),
                //     'has_product_mediacontent' => Input::get('has_product_mediacontent'),
                //     'product_allow_installment' => Input::get('product_allow_installment'),
                //     'variant_allow_installment' => Input::get('variant_allow_installment'),
                //     'product_allow_cod' => Input::get('product_allow_cod'),
                //     'has_price' => Input::get('has_price'),
                //     );
                // echo $products->appends($query)->links();
            ?>

        <?php }else{ ?>

		        <table class="mws-datatable-fn mws-table">
		            <thead>
		                <tr>
							<th style="width:100px;">Brand</th>
							<th>Product</th>
							<th style="width:50px;">Status</th>
							<th style="width:100px;">Actions</th>
		                </tr>
		            </thead>
		            <tbody>
						<td class="table-center" colspan="4">ไม่พบข้อมูล</td>
		            </tbody>
		        </table>

        <?php } ?>



    </div>
</div>

