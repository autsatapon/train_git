<?php

Theme::asset()->container('footer')->writeScript('installment-script', '
$(document).on("change",".toggle-allow-installment",function(e){
	var o = $(this),
		t = o.parents(".variant-row,.product-row").find(".installment-period-div");
	if((o.is(":checked") || o.prop("tagName")==="SELECT") && o.val()==="yes")
		t.removeClass("hide");
	else
		t.addClass("hide");
})
', array('jquery'));

Form::macro('macroAllowInstallment', function($name, $checked)
{
	return Form::checkbox($name, 'yes', $checked, array('class' => 'toggle-allow-installment', 'id'=>$name))
		  .Form::label($name, ' allow');
});
Form::macro('macroVariantAllowInstallment', function($name, $value)
{
	return Form::select($name, array(''=>'As product', 'yes'=>'Allow', 'no'=>'Not allow'), $value===null ? '' : ($value===true ? 'yes' : ($value===false ? 'no' : $value)), array('class' => 'toggle-allow-installment'));
});

Form::macro('macroInstallment', function($name, $values, $periods)
{
	$checkboxes = '';
	foreach ($periods as $period)
	{
		$id = snake_case($name.$period);
		$checkboxes .= Form::checkbox($name.'['.$period.']', $period, in_array($period, $values), array('id' => $id)).Form::label($id, " $period months").'<br>';
	}
	return $checkboxes;
});

?>
<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-table"></i> Edit Product Price</span>
    </div>

    <div class="mws-panel-body no-padding">

    	<?php if ( !$revisions->isEmpty() ) { ?>
            <?php
            	$revision = $revisions->first();
            	$modifiedData = $revision->modifiedData;
            ?>
            <?php if (isset($modifiedData['price'])) { ?>
	            <div class="alert alert-warning">
	                <p>Warning: You have a draft of this product !<br> <a href="<?php echo URL::to("products/approve/detail/{$product->id}/{$revision->id}") ?>">View Detail</a></p>
	            </div>
            <?php } ?>
        <?php } ?>

		<?php echo HTML::message(); ?>

        <table class="mws-datatable-fn mws-table">
            <thead>
                <tr>
					 <th style="width:10%"><?php echo Brand::getLabel('name') ?></th>
					 <th style="width:20%"><?php echo Product::getLabel('title') ?></th>
					 <th style="width:10%">ราคาเต็ม (SCM)</th>
					 <th style="width:10%">ราคาหน้าเว็บ (SCM)</th>
					 <th style="width:10%"><?php echo ProductVariant::getLabel('free_item') ?></th>
					 <th style="width:10%"><?php echo ProductVariant::getLabel('net_price') ?></th>
					 <th style="width:10%"><?php echo ProductVariant::getLabel('special_price') ?></th>
					 <th style="width:10%"><?php echo ProductVariant::getLabel('allow_installment') ?></th>
					 <th style="width:20%"><?php echo ProductVariant::getLabel('installment_period') ?></th>
                </tr>
            </thead>
            <tbody>
			<form method="post" action="" class="mws-form" >
				<tr style="border-top:3px solid #4D4D4D;border-bottom:1px solid #4D4D4D;margin-top:10px;" class="product-row">
					<td colspan="1" style="background-color:#D7D7D7;width:100px"><?php echo $product->brand->name ?></td>
					<td colspan="6"><strong><?php echo $product->title ?></strong></td>
					<td><?php
						echo Form::macroAllowInstallment('allow-installment-product', Input::old('allow-installment-product', $product->allow_installment) ? true : false);
					?></td>
					<td>
						<div class="installment-period-div <?php echo Input::old('allow-installment-product', $product->allow_installment) ? null : 'hide' ?>">
						<?php
							echo Form::macroInstallment('installment-product', $product->installment_periods, $installmentPeriods);
						?>
						</div>
					</td>
				</tr>
				<?php foreach($product->variants as $variant): ?>
				<?php
					$normalPrice = $variant->getOriginal('normal_price');
					$price = $variant->getOriginal('price');

					$netPrice = (int) ($normalPrice > 0 ? $normalPrice : $price);
					$specialPrice = (int) ($normalPrice > 0 ? $price : '');
				?>
				<tr class="variant-row">
				
					<td>&nbsp;</td>
					<td>- <?php echo $variant->title ?></td>
					<td class="table-center" style="background-color:#DDDDDD;border-bottom:1px solid #4D4D4D;">
						<strong><?php echo $variant->retail_normal_price; ?></strong>
					</td>
					<td class="table-center" style="background-color:#DDDDDD;border-bottom:1px solid #4D4D4D;">
						<strong><?php echo $variant->retail_price; ?></strong>
					</td>
					<td class="table-center">
						<input type="checkbox" name="free_item[<?php echo $variant->id?>]" <?php echo (Input::old('free_item.'.$variant->id, $variant->free_item)=='yes' ? 'checked="checked"' : '') ?>>
					</td>

					<!-- net price -->
					<td class="table-center">
						<input type="text" style="text-align:right;width:100px;" class="small pcms-numeric" name="old_price[<?php echo $variant->id?>]" 
							value="<?php echo Input::old('old_price.'.$variant->id, $netPrice) ?>">
					</td>
					
					<!-- special price -->
					<td class="table-center">
						 <input type="text" style="text-align:right;width:100px;" class="small pcms-numeric" name="special_price[<?php echo $variant->id?>]" 
						 	value="<?php echo Input::old('special_price.'.$variant->id, $specialPrice ?: '') ?>">
					</td>


					<td><?php
						echo Form::macroVariantAllowInstallment('allow-installment-variant['.$variant->id.']', Input::old('allow-installment-variant.'.$variant->id, $variant->allow_installment));
					?></td>
					<td>
						<div class="installment-period-div <?php echo Input::old('allow-installment-variant.'.$variant->id, $variant->allow_installment) ? null : 'hide' ?>">
						<?php
							echo Form::macroInstallment('installment-variant['.$variant->id.']', $variant->installment_periods, $installmentPeriods);
						?>
						</div>
					</td>
				</tr>
				<?php endforeach ?>
				<tr style="border-top:1px solid #999999">
					 <td colspan="10" style="text-align:right;padding-right:50px;"><input type="submit" class="btn btn-primary" value="Save"></td>
				</tr>
			</form>
            </tbody>
        </table>
    </div>
</div>
