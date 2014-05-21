<?php

		Theme::asset()->writeStyle('Vendor-policy-version-1.0','
		.table-left {
		    text-align:left;
			padding-left:10px;
		}
		.table-right {
		    text-align:right;
			padding-right:10px;
			margin-bottom:10px;
		}
		.bg-blue {
			background-color:#BAF6FF;
		}
		.logo {
			width:50px;
		}
		.tborder{
			border-top:3px solid #B3B3B3;
		}
		.headbar_left{
			float:left;
			width:80%;
			overflow:hidden;
		}
		.headbar_right{
			float:left;
			width:20%;
			text-align:right;
		}
		');

?>
<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-table"></i> Vendor's Policy </span>
    </div>

     <div class="mws-panel-toolbar">
        <div class="btn-toolbar">
            <div class="btn-group">
                <a class="btn" href="<?php echo URL::to('policies/vendors/create') ?>"><i class="icol-add"></i> Add Policy for vendor</a>
            </div>
        </div>
    </div>
    
        <div class="mws-panel-body no-padding">


        <?php echo HTML::message(); ?>
        
		 <?php foreach ($vendors as $key => $vendor) { ?>
        <table class="mws-datatable-fn mws-table table-left tborder">
            <thead>
                <tr>
					 <th >
						<div class="headbar_left">
							<?php echo $vendor->vendor_detail; ?>
						</div>
						<div class="headbar_right">
							<a class="btn btn-warning" href="<?php echo URL::to("policies/vendors/edit/{$vendor->vendor_id}") ?>"><i class="icon-edit"></i> Edit Policies of <?php echo $vendor->vendor ?></a>
						</div>
					</th>
                </tr>
            </thead>
        </table>
         <table class="mws-datatable-fn mws-table">
            <thead>
                <tr>
                	 <?php if ( ! $policies->isEmpty()) foreach ($policies as $key => $policy) { ?>
					 	<th>
									<?php echo $policy->title ?>
						</th>
					 <?php } ?>
                </tr>
            </thead>
            <tbody class="table-center">
            		<?php
                		$vendorsArr = DB::table('vendors_policies')->select('policy_id')->distinct()->where('vendor_id', $vendor->vendor_id)->where('brand_id', null)->lists('policy_id');
                	?>
            	<tr>
            		<?php foreach ($policies as $key => $policy) { ?>
	            		<td class="table-center">
	            				<?php if ( in_array($policy->id, $vendorsArr) ) { ?>
                        			<span><img src="<?php echo $policy->LogoThumb ?>" class="logo"></span>
                        		<?php } else { ?>
                        			<i class="icon-remove"></i>
                        		<?php } ?>
	            		</td>
            		<?php } ?>
            	</tr>
            </tbody>
         </table>
         <table class="mws-datatable-fn mws-table table-right">
            <thead>
                <tr>
					 <th >
					 	<div >
			                <a  href="<?php echo URL::to("policies/brands/vendor/{$vendor->vendor_id}") ?>"><i class="icon-edit"></i> view Brand's Policies of <?php echo $vendor->vendor_detail ?></a>
			            </div>
					 </th>
                </tr>
            </thead>
        </table>
        <?php } ?>
        
       
        
    </div>
    
</div>

