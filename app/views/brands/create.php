<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span>Create New Brand</span>
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
                    <label class="mws-form-label" for="name">Name</label>
                    <div class="mws-form-item">
                        <input type="text" class="small" name="name" id="name" value="<?php echo Input::old('name') ?>">
                        <?php echo Form::transText(null, 'name', array('class' => 'small')) ?>
                    </div>
                </div>
                <div class="mws-form-row">
                    <label class="mws-form-label" for="description">Description</label>
                    <div class="mws-form-item">
						<textarea class="form-control" rows="3" cols="53" name="description" id="description" ><?php echo Input::old('description') ?></textarea>
                        <?php echo Form::transTextarea(null, 'description', array('row' => '3', 'cols' => '53', 'class' => 'form-control')) ?>
                    </div>
                </div>
                <div class="mws-form-row">
                    <label class="mws-form-label" for="note">Note</label>
                    <div class="mws-form-item">
					<textarea class="form-control" rows="3"  cols="53" name="note" id="note" ><?php echo Input::old('note') ?></textarea>
                    </div>
                </div>
				<div class="mws-form-row">
                    <label class="mws-form-label" for="brandlogo">Logo</label>
                    <div class="mws-form-item small" class="col-lg-4">
					   <input type="file" name="brandlogo" >
                    </div>
                </div>

            </div>
            <div class="mws-button-row">
                <input type="submit" class="btn btn-primary" value="Create">
            </div>
        </form>


    </div>
</div>
