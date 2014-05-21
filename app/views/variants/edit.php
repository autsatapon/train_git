<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-table"></i> Variants </span>
    </div>
	<div class="clear"></div>

    <div class="mws-panel-body no-padding">

    	<?php echo HTML::message() ?>

						<form method="post" action="" class="mws-form" >
						<table class="mws-datatable-fn mws-table">
							<thead>
								<tr style="border-top:3px solid #000000;height:40px;border-bottom:1px solid #000000;">
									<td>
									<strong>Product :</strong> <?php echo $products->product->title?>
									</td>
									<td colspan="<?php echo count($products->variantStyleOption)?>">
									<strong>Brand :</strong> <?php echo $products->brand->name ?>
									</td>

								</tr>
								<tr>
									<th style="width:500px;">Material Title</th>
										<?php foreach  ($products->variantStyleOption as $variantStyleOption  ) : ?>
											<th class="table-center">
											<?php echo $variantStyleOption->StyleType->name ?> 
											</th>
										<?php endforeach ; ?>
								</tr>
							</thead>
							<tbody>
									<tr>
										 <td>- <?php echo $products->title ?>  
										 
										 </td>
										 <?php foreach  ($products->variantStyleOption as $variantStyleOption  ) : ?>
										 <td class="table-center">
					<?php
					$options = array() ;
					foreach ($thisthis[$variantStyleOption->style_type_id] as $pop) {
						$options[$pop->text] = $pop->text ; 
					}
                    echo Form::suggestBox("option[{$variantStyleOption->id}]", $options, $variantStyleOption->text, array('style' => 'width: 120px;'), true);
                    ?>
										 </td>
										<?php  endforeach;  ?>
									</tr>
 				<tr style="border-top:1px solid #999999">
 				 <td></td>
					 <td style="text-align:right;padding-right:50px;"  colspan="<?php echo count($products->variantStyleOption)?>"><input type="submit" class="btn btn-primary" value="Save"></td>
				</tr>
           			 </tbody>
		        	</table>
		          	</form>	  


    </div>
</div>

