<?php

		Theme::asset()->writeStyle('Vendor-policy-create-version-1.0','
		.div-right {
		    text-align:right;
			padding-right:10px;
		}
		
		.mws-form .mws-form-inline .mws-form-label {
		    float: left;
		    padding-top: 5px;
		    width: 30%;
			height:70px;
		}
		.logo {
			width:30px;
			margin-right:10px;
		}
		.tarea {
			width:500px;
			margin-top:10px;
		}
		.mws-form textarea {
			resize: none;
		}
		.form-control {
			width:70%;
		}
		.cke {
			overflow:hidden;
		}
		.br10 {
			margin-bottom:10px;
		}
		
		input[type="text"] {
			width:80%;
		}
		.policy-description {
			margin-top:10px;
		}
		.policy-title {
			margin-top:5px;
		}
		');

?>
<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span>Create Vendor's Policy</span>
    </div>
    <div class="mws-panel-body no-padding">

        <?php /*
		<?php if ($errors->count() > 0) { ?>
			<div class="alert alert-error">
				<?php foreach ($errors->all() as $error) { ?>
				    <p><?php echo $error ?></p>
				<?php } ?>
			</div>
		<?php } ?>
        */ ?>

        <?php echo HTML::message(); ?>

        <form method="post" id="vendor-policy-form" action="" class="mws-form" enctype="multipart/form-data">
            <div class="mws-form-inline">
                <div class="mws-form-row">
                    <label class="mws-form-label" for="title"><strong><?php echo VVendor::getLabel('name') ?></strong></label>
                    <div class="mws-form-item">
	                   <select name="vendor_id">
	                   	<?php foreach($vendors as $key => $vendor) { ?>
	                   		<option value="<?php echo $vendor->vendor_id ?>">
	                   			<?php echo $vendor->name,' | ',$vendor->master_id ?: $vendor->shop_id; ?>
	                   		</option>
	                   	<?php } ?>
	                   </select>
                    </div>
                </div>
                <?php foreach ($policies as $key => $policy) { ?>
                	<div class="mws-form-row">
	                    <label class="mws-form-label" for="description">
	                    	<img class="logo" src="<?php echo $policy->logo ?>" title="<?php echo $policy->title ?>"> <?php echo $policy->title ?>
	                    </label>
	                    <div class="mws-form-item policy-row">
								<input type="radio" name="policy_type[<?php echo $policy->id ?>]" value="<?php echo $policy->id ?>">&nbsp;&nbsp;<strong>Use</strong>&nbsp;&nbsp;&nbsp;&nbsp; 
								<input type="radio" name="policy_type[<?php echo $policy->id ?>]" value="0" checked="checked">&nbsp;&nbsp;<strong>Don't Use</strong>  <br class="br10">
								<div class="policy-title">
									<input type="text"  name="title[<?php echo $policy->id ?>]" id="title" value="<?php echo $policy->title ?>">
								</div>
								<div class="policy-description" style="display:none;">
									<?php echo Form::ckeditor("description[{$policy->id}]", Input::old("description[{$policy->id}]", $policy->description), array('id' => 'description'.$key, 'class' => 'form-control', 'height' => '300px')); ?>
								</div>
	                    </div>
	                </div>
                <?php } ?>


            </div>
            <div class="mws-button-row div-right">
                <input type="submit" class="btn btn-primary" value="Save">
            </div>
        </form>


    </div>
</div>
<?php
Theme::asset()->container('footer')->writeScript('script', '
$(document).on("change", "#vendor-policy-form input[type=radio]", function(){
	var o = $(this),
		desc = o.parents(".policy-row").find(".policy-description");

	if(o.val()>0)
		desc.slideDown("fast");
	else
		desc.slideUp("fast");
})
');
?>