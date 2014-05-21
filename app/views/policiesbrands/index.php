<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-table"></i> Brand's Policy of <?php echo $vendor->vendor; ?></span>
    </div>


    <div class="mws-panel-body no-padding">


        <?php echo HTML::message(); ?>

        <table class="mws-datatable-fn mws-table">
            <thead>
                <tr>
					 <th style="width:135px;" >Brand</th>
					 <?php if ( ! $policies_data->isEmpty()) foreach ($policies_data as $key => $policy) { ?>
					 	<th><?php echo $policy->title ?></th>
					 <?php } ?>
                </tr>
                <tr>
                	<th style="width:135px;" >&nbsp;</th>
					 <?php if ( ! $policies_data->isEmpty()) { ?>
					 	<?php foreach ($policies_data as $key => $policy) { ?>
					 		
					 		<?php if (in_array($policy->id, $arr1)) { ?>
					 			<th>Used</th>	
					 		<?php } else { ?>
					 			<th><i class="icon-remove"></i></th>
					 		<?php } ?>
					 		
					 	<?php } ?>
					 <?php } ?>
                </tr>
            </thead>
            <tbody>
	
                <?php foreach ($brands as $key => $brand) { ?>
                    <tr>
						 <td class="table-center">
						 	<strong><?php echo $brand->name ?></strong><br>
						 	<img src="<?php echo $brand->LogoThumb?>" alt="<?php echo $brand->title ?>" width="60">
						 </td>
						 
						 <?php if ( ! $policies_data->isEmpty()) { ?>
						 	<?php foreach ($policies_data as $key => $policy) { ?>
						 		<td class="table-center">
						 			
						 			<?php if (isset($arr2[$brand->id][$policy->id])) { ?>
							 			<?php	if($arr2[$brand->id][$policy->id]['status'] == 'used'){ ?>
							 					<strong>My Policy</strong>
							 			<?php	}else{ ?>
							 					<strong>Don't Used</strong>
							 			<?php	} ?>
						 				<?php	$vendor_policy_brand_id = $arr2[$brand->id][$policy->id]['id']; ?>
						 				<br><br>
							 		 <a class="btn btn-mini btn-warning" href="<?php echo URL::to("policies/brands/edit/$vendor_policy_brand_id/{$brand->id}/{$policy->id}/{$vendor->vendor_id}") ?>"><i class="icon-edit"></i> Edit</a>
						 			<?php } else { ?>
						 				<?php	$vendor_policy_brand_id = 0; ?>
							 			<?php if (in_array($policy->id, $arr1)) { ?>
								 			<strong>Inherit</strong>
								 		<?php } else { ?>
								 			<i class="icon-remove"></i>
								 		<?php } ?>
								 		<br><br>
							 		 <a class="btn btn-mini btn-warning" href="<?php echo URL::to("policies/brands/create/{$brand->id}/{$policy->id}/{$vendor->vendor_id}") ?>"><i class="icon-edit"></i> Edit</a>
							 		<?php } ?>
							 		 
						 		</td>
						 	<?php } ?>
						 <?php } ?>
<?php /*
                        <?php foreach ($vendor_policies as $key => $policy) { ?>
    
                        	<td class="table-center">
                        		<?php if ( in_array($policy->id, $vendor_brand_policies) ) { ?>
                        			<span><img src="<?php echo $policy->LogoThumb ?>" width="60"></span>
                        			<span style="margin-left:15px;height:50px;overflow:hidden;">
                        				<a class="btn btn-mini btn-warning" href="<?php echo URL::to("policies/brands/edit/{$brand->id}/{$policy->id}/{$brand->vendor_id}") ?>"><i class="icon-edit"></i></a>
										<a class="btn btn-mini btn-danger" href="<?php echo URL::to("policies/brands/delete/{$brand->id}/{$policy->id}/{$brand->vendor_id}") ?>"><i class="icon-trash"></i></a>

                        			</span>
                        		<?php } else { ?>
                        			<a class="btn btn-mini btn-success" href="<?php echo URL::to("policies/brands/edit/{$brand->id}/{$policy->id}/{$brand->vendor_id}") ?>"><i class="icon-plus-sign"></i></a>
                        		<?php } ?>


                        		<?php /*
                        		<?php echo ( in_array($policy->id, $brandPoliciesArr) ) ?
                        		 '<span><img src="'.$policy->LogoThumb.'" width="60"></span><span style="margin-left:15px"><i class="icon-edit"></i>   <i class="icon-trash"></i></span>' : '<i class="icon-plus-sign"></i>' ; ?>
								 */ ?><?php /*
                        	</td>
							
                        <?php } ?>
*/ ?>
								  
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

