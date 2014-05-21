<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span>Edit Brand Policy</span>
    </div>
    <div class="mws-panel-body no-padding">

		<?php if ($errors->count() > 0) { ?>
			<div class="alert alert-error">
				<?php foreach ($errors->all() as $error) { ?>
				    <p><?php echo $error ?></p>
				<?php } ?>
			</div>
		<?php } ?>
		
		    

        <form method="post" action="" class="mws-form" enctype="multipart/form-data">
            <div class="mws-form-inline">
            	<div class="mws-form-row">
                    <label class="mws-form-label" for="title">  </label>
                    <div class="mws-form-item">
                        <div class="row">
					  	  <div style="width:250px;float:left;text-align:center;"><div style="height:25px;"><strong><?php echo $brand['name'] ?></strong></div><img src="<?php echo $brand['logo'] ?>" width="80"></div>
					  	  <div style="width:250px;float:left;text-align:center;"><div style="height:25px;"><strong><?php echo $policy['title'] ?></strong></div><img src="<?php echo $policy['logo'] ?>" width="80"></div>
					    </div>
                    </div>
                </div>

				<div class="mws-form-row">
                    <label class="mws-form-label" for="description"> <strong>Brand Policy Title:</strong></label>
                    <div class="mws-form-item">
                        <div class="row">
                        		
                        		<input type="text" class="small" name="policy_title" id="policy_title" value="<?php echo Input::old('policy_title', $vendor_policy['policy_title']) ?>">
					    </div>
                    </div>
                </div>

                <div class="mws-form-row">
                    <label class="mws-form-label" for="detail"><strong>Brand Policy Description</strong></label>
                    <div class="mws-form-item">
                    		
						  		<?php echo Form::ckeditor('policy_description', Input::old('policy_description', $vendor_policy['policy_description']), array('id' => 'policy_description', 'class' => 'form-control', 'height' => '150px')); ?>
                  		 	 
                    </div>
                </div>

				<div class="mws-form-row">
                    <label class="mws-form-label" for="description"> <strong>Brand Policy Status:</strong></label>
                    <div class="mws-form-item">
                        <div class="row">
                        		
                        		<?php if ($vendor_policy['status'] == 'used') { ?>
									<input type="radio" name="status" value="used" checked="checked">&nbsp;&nbsp;<strong>Use</strong>&nbsp;&nbsp;&nbsp;&nbsp; 
									<input type="radio" name="status" value="not_used">&nbsp;&nbsp;<strong>Don't Use</strong>  <br class="br10">
								<?php  } else { ?>
									<input type="radio" name="status" value="used">&nbsp;&nbsp;<strong>Use</strong>&nbsp;&nbsp;&nbsp;&nbsp; 
									<input type="radio" name="status" value="not_used" checked="checked">&nbsp;&nbsp;<strong>Don't Use</strong>  <br class="br10">
								<?php } ?>
					    </div>
                    </div>
                </div>

            </div>
            <div class="mws-button-row" style="text-align:right">
                <input type="submit" class="btn btn-primary " value="Save">
            </div>
        </form>


    </div>
</div>
