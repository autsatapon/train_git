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
					<th class="span1" rowspan="2">Product</th>
					<th class="span2" rowspan="2">Variant</th>
					<th colspan="4">Shipping Data</th>
					<th class="span1" rowspan="2">Allow COD?</th>
					<th rowspan="2">Actions</th>
				</tr>
				<tr>
					<th class="span1">Dimension</th>
					<th class="span1">Max dimension</th>
					<th class="span1">Weight</th>
					<th class="span1">Fragility</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($products as $product) { ?>
					<tr style="border-top:5px solid #000;">
						 <td style="background-color:#D7D7D7;" colspan="6"><strong><?php echo $product->title ?></strong> (<?php echo $product->brand->name ?>)</td>
						 <td class="table-center"><?php echo $product->allow_cod ? 'allow' : '-' ?></td>
						 <td class="table-center">
						 	<a href="/products/set-shipping/edit/<?php echo $product->id ?>" ><input type="button" class="btn btn-warning" value="Edit"></a>
						 </td>
					</tr>
						<?php foreach ($product->variants as $i => $variant) { ?>
							<?php // foreach ($vData as $vkey => $vvData) { ?>
								<tr>
									<?php if ($i == 0): ?>
									<td class="span2 table-center" rowspan="<?php echo count($product->variants) ?>" style="width:100px;"><?php echo $product->image ? HTML::image($product->image) : '' ?></td>
									<?php endif ?>
									<td><?php echo $variant->title ?></td>
									<td><?php echo $variant->dimension_width ?><?php echo isset($variant->dimension_length) ? ' x '. $variant->dimension_length : '' ?><?php echo isset($variant->dimension_height) ? ' x '.$variant->dimension_height.  ' cm' : '' ?></td>
									<td><?php echo isset($variant->dimension_max) ? $variant->dimension_max . ' cm' : '' ?></td>
									<td><?php echo (isset($variant->weight) ? (($variant->weight >= 1000) ? $variant->weight / 1000 . ' kg' : $variant->weight .' g' ) : '') ?></td>
									<td class="table-center"><?php echo $variant->fragility=='yes' ? 'fragile' : '&nbsp;' ?></td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
								</tr>
							<?php // } ?>
						<?php } ?>
					
			  	 <?php } ?>
			  	 <tr style="border-bottom:3px solid #000;"></tr>
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
						<th class="span1" rowspan="2">Product</th>
						<th class="span2" rowspan="2">Variant</th>
						<th colspan="4">Shipping Data</th>
						<th class="span1" rowspan="2">Allow COD?</th>
						<th rowspan="2">Actions</th>
					</tr>
					<tr>
						<th class="span1">Dimension</th>
						<th class="span1">Max dimension</th>
						<th class="span1">Weight</th>
						<th class="span1">Fragility</th>
					</tr>
				</thead>
		            <tbody>
						<td class="table-center" colspan="8">ไม่พบข้อมูล</td>
		            </tbody>
		        </table>

        <?php } ?>


	</div>
</div>

