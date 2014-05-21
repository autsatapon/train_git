<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-table"></i> Variants </span>
    </div>

	<?php echo Theme::widget('WidgetProductSearchForm', array())->render(); ?>
	<div class="clear"></div>

    <div class="mws-panel-body no-padding">

    	<?php echo HTML::message() ?>

      		  


						<?php foreach ($products as $product) { ?>
						<table class="mws-datatable-fn mws-table">
							<thead>
								<tr style="border-top:3px solid #000000;height:40px;border-bottom:1px solid #000000;">
									<td>
									<strong>Product :</strong> <?php echo $product->title?>
									</td>
									<td>
									<strong>Brand   :</strong> <?php echo $product->brand->name ?>
									</td>
									<td colspan="3">
									<td>
								</tr>
								<tr>
									<th style="width:500px;">Material Title</th>
									<th></th>
									<th></th>
									<th><a class="btn add_style" title="Add style type"><i class="icon-plus"></i></a></th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
							<?php foreach ($product->variants as $variant) { ?>
								<?php foreach ($product->styleTypes as $styleType) { ?>
									<tr>
										 <td>- <?php echo $variant->title ?></td>
										 <?php
										 	// $aaa = $variant->variantStyleOption()->where('style_type_id', 1)->get();
										 ?>
										 <td><?php echo $styleType->text ?>;;l</td>
										 <td></td>
										 <td></td>
										 <td class="table-center">
											<a href="<?php echo URL::to('variants/edit/'.$variant->id ); ?>"><input type="button" class="btn-mini btn btn-warning" value="Edit"></a>
											<a href="<?php echo URL::to('variants/delete/'.$variant->id ); ?>"><input type="button" class="btn-mini btn btn-danger" value="Delete"></a>
										 </td>
									</tr>
								<?php } ?>
							<?php } ?>

						
					<!-- <tr style="border-top:1px solid #999999">
					</tr> -->

           			 </tbody>
		        	</table>
		        	<?php } ?>
    </div>
</div>

