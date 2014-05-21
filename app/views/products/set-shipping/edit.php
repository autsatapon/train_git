<?php /*
<script type="text/javascript" src="/js/angular.min.js"></script>
<script type="text/javascript">
function SetPriceController($scope) {
	$scope.variants = <?php echo $product->variants->toJson() ?>;
	
	$scope.old_price = function() {
		if($scope.free_item)
			return 0;
		if($scope.normal_price>0)
			return $scope.normal_price;
		else
			return $scope.price;
	}
	
	$scope.special_price = function() {
		if($scope.free_item)
			return 0;
		if($scope.normal_price>0)
			return $scope.price;
		else
			return 0;
	}
	
	$scope.is_free_item = function() {
		return $scope.free_item;
	}
}
</script>
*/ ?>
<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-table"></i> Edit Product Shipping</span>
    </div>
    <div class="mws-panel-body no-padding">
		<?php echo HTML::message(); ?>

        <table class="mws-datatable-fn mws-table" ng-app ng-controller="SetShippingController">
           <thead>
                 <tr>
					<th rowspan="2"><?php echo Brand::getLabel('name') ?></th>
					<th rowspan="2"><?php echo Product::getLabel('title') ?></th>
					<th colspan="3">Shipping Data</th>
					<th rowspan="2">Allow COD?</th>
                </tr>
                <tr> 
                	<th class="span1"><?php echo Product::getLabel('dimension') ?> (w x l x h) cm</th>
                	<th class="span1"><?php echo Product::getLabel('weight') ?></th>
                	<th class="span1"><?php echo Product::getLabel('fragility') ?></th>
                </tr> 
            </thead> 
            <tbody>
			<form method="post" action="" class="mws-form" >  
				<tr style="border-top:3px solid #4D4D4D;border-bottom:1px solid #4D4D4D;margin-top:10px;">
					 <td colspan="1" style="background-color:#D7D7D7;width:100px"><?php echo $product->brand->name ?></td>
					 <td colspan="4"><strong><?php echo $product->title ?></strong></td>
					 <td class="table-center"><?php
					 	$variant = $product->variants->first();
					 	if ($variant && Stock::isStock($variant->stock_type))
					 	{
						 	echo Form::label('allow_cod', 'Allow '),
						 		 Form::checkbox('allow_cod', 1, Input::old('allow_cod', $product->allow_cod));
						}
						else
						{
							echo 'Not allow';
						}
					 ?></td>
				</tr>
				<?php foreach($product->variants as $variant): ?>
				<tr>
					<td>&nbsp;<input type="hidden" name="vid[<?php echo $variant->id?>]" value="<?php echo $variant->id?>"></td>
					<td>- <?php echo $variant->title ?></td>
					<td class="table-center">
						<input type="text" class="small pcms-numeric" size="1" name="dimension_width[<?php echo $variant->id?>]" value="<?php echo Input::old('dimension_width.'.$variant->id , $variant->dimension_width) ?>" > X
						<input type="text" class="small pcms-numeric" size="1" name="dimension_length[<?php echo $variant->id?>]" value="<?php echo Input::old('dimension_length.'.$variant->id , $variant->dimension_length) ?>" > X
						<input type="text" class="small pcms-numeric" size="1" name="dimension_height[<?php echo $variant->id?>]" value="<?php echo Input::old('dimension_height.'.$variant->id), $variant->dimension_height ?>" > cm
					</td>
					<td class="table-center">
						 <input type="text" style="text-align:right" class="small pcms-numeric" size="1" name="weight[<?php echo $variant->id?>]" value="<?php echo (Input::old('weight.'.$variant->id, $variant->weight) >= 1000) ? (input::old('weight.'.$variant->id, $variant->weight)/1000) : (input::old('weight.'.$variant->id, $variant->weight))  ?>" >
						 <?php 
							$old_val = 	Input::old('weight.'.$variant->id, $variant->weight) ;
						 	if (isset($old_val)) : ?>
						 <?php echo Form::select('dimension_unit['.$variant->id.']',array('1000' =>'Kg','1' => 'g'), (Input::old('weight.'.$variant->id, $variant->weight) >= 1000) ? 1000 : 1) ?>	
						 <?php else : ?>
						 <?php echo Form::select('dimension_unit['.$variant->id.']',array('1000' =>'Kg','1' => 'g'),1000) ?>
						 <?php endif ; ?>
					<td class="table-center">    
						<input type="checkbox" name="fragility[<?php echo $variant->id?>]" id="fragility_<?php echo $variant->id?>" <?php echo (Input::old('fragility.'.$variant->id, $variant->fragility)=='yes' ? 'checked="checked"' : '') ?>>
						<label for="fragility_<?php echo $variant->id?>">Fragile</label>
					</td>
					<td>&nbsp;</td>
				</tr>
				<?php endforeach ?>
				<tr style="border-top:1px solid #999999">
					 <td colspan="6" style="text-align:right;padding-right:50px;"><input type="submit" class="btn btn-primary" value="Save"></td>
				</tr>
			</form>
            </tbody>
        </table>
    </div>
</div>
