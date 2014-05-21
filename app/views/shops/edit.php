<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span>Edit Shop</span>
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
                    <label class="mws-form-label" for="title">Shop ID</label>
                    <div class="mws-form-item">
                        <input type="text" class="small" disabled="disabled" name="shop_id" id="shop_id" value="<?php echo Input::old('shop_id', $formData['shop_id']) ?>">
                    </div>
                </div>
            </div>

            <div class="mws-form-inline">
                <div class="mws-form-row">
                    <label class="mws-form-label" for="title">Shop Name</label>
                    <div class="mws-form-item">
                        <input type="text" class="small" name="name" id="name" value="<?php echo Input::old('name', $formData['name']) ?>">
                        <?php echo Form::transText($shop, 'name', array('class' => 'small')) ?>
                    </div>
                </div>
            </div>

            <div class="mws-button-row">
                <input type="submit" class="btn btn-primary" value="Save">
            </div>
        </form>


    </div>
</div>
