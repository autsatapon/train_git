<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span>Create Policy</span>
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

        <form method="post" action="" class="mws-form" enctype="multipart/form-data">
            <div class="mws-form-inline">
                <div class="mws-form-row">
                    <label class="mws-form-label" for="title">Title</label>
                    <div class="mws-form-item">
	                    <input type="text" class="small" name="title" id="title" value="<?php echo Input::old('title') ?>">
                        <?php echo Form::transText(null, 'title', array('class' => 'small')) ?>
                    </div>
                </div>
                <div class="mws-form-row">
                    <label class="mws-form-label" for="title">Type</label>
                    <div class="mws-form-item">
                        <?php echo Form::select('type', $policyType, null) ?>
                    </div>
                </div>
                <div class="mws-form-row">
                    <label class="mws-form-label" for="description">Description</label>
                    <div class="mws-form-item">

                        <?php echo Form::ckeditor('description', Input::old('description'), array('id' => 'description', 'class' => 'form-control', 'height' => '150px')); ?>
                        <?php echo Form::transCkeditor(null, 'description', array('class' => 'form-control', 'height' => '150px')) ?>

                        <p>
                            <ul>
                                <li>{shop} แทนชื่อร้านค้า</li>
                                <li>{vendor} แทนชื่อ vendor</li>
                                <li>{brand} แทนชื่อ brand</li>
                            </ul>
                        </p>

                    </div>
                </div>
				<div class="mws-form-row">
                    <label class="mws-form-label" for="policylogo">Logo</label>
                    <div class="mws-form-item small" class="col-lg-4">
					   <input type="file" name="policylogo" >
                    </div>
                </div>

            </div>
            <div class="mws-button-row">
                <input type="submit" class="btn btn-primary" value="Save">
            </div>
        </form>


    </div>
</div>
