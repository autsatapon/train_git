<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span>Edit Brand</span>
    </div>
    <div class="mws-panel-body no-padding">

        <?php if (Session::has('success')): ?>
            <div class="alert alert-success">
                <p><?php echo Session::get('success') ?></p>
            </div>
        <?php endif ?>
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
                    <label class="mws-form-label" for="name"><?php echo Brand::getLabel('name') ?></label>
                    <div class="mws-form-item">
                        <input type="text" class="small" name="name" id="name" value="<?php echo Input::old('name', $formData['name']) ?>">
                        <?php echo Form::transText($brand, 'name', array('class' => 'small')) ?>
                    </div>
                </div>
                <div class="mws-form-row">
                    <label class="mws-form-label" for="description"><?php echo Brand::getLabel('description') ?></label>
                    <div class="mws-form-item">
						<textarea class="form-control" rows="3" cols="53" name="description" id="description" ><?php echo Input::old('description', $formData['description']) ?></textarea>
                        <?php echo Form::transTextarea($brand, 'description', array('row' => '3', 'cols' => '53', 'class' => 'form-control')) ?>
                    </div>
                </div>
                <div class="mws-form-row">
                    <label class="mws-form-label" for="note"><?php echo Brand::getLabel('note') ?></label>
                    <div class="mws-form-item">
						<textarea class="form-control" rows="3" cols="53" name="note" id="note" ><?php echo Input::old('note', $formData['note']) ?></textarea>
                    </div>
                </div>

                <div class="mws-form-row">
                    <label class="mws-form-label" for="brandlogo"><?php echo Brand::getLabel('logo') ?></label>
                    <div class="mws-form-item small">
					   <input type="file" name="brandlogo" class="small">
                    </div>
                </div>

                <?php if($formData['logo'] != ''){ ?>
                    <div class="mws-form-row">
                        <label class="mws-form-label" for="brandlogoimg"></label>
                        <div class="mws-form-item small" style="width:150px;">
                           <img src="<?php echo $formData['logo'] ?>">
                        </div>
                    </div>
                <?php } ?>

            </div>
            <div class="mws-button-row">
                <input type="submit" class="btn btn-primary" value="Save">
            </div>
        </form>


    </div>
</div>

<?php if(isset($brand)) foreach ($apps as $app) : ?>
<?php echo Theme::widget('meta', array('app' => $app, 'content' => $brand))->render(); ?>
<?php endforeach; ?>


