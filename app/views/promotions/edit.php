<div class="mws-panel grid_8 promotion_create">

    <div class="mws-panel-header">
        <span>Edit Promotion</span>
    </div>

    <div class="mws-panel-body no-padding">

        <?php echo HTML::message(); ?>

        <form method="post" action="" class="mws-form" enctype="multipart/form-data">

            <fieldset class="mws-form-inline">

				<legend>Promotion</legend>

                <div class="mws-form-row">
                    <label class="mws-form-label" for="type">Type</label>
                    <div class="mws-form-item">
						<?php echo Form::select('promotion_category', array($promotion->promotion_category => $promotion->promotion_category) , Input::old('promotion_category', $promotion->promotion_category),array('disabled' => "disabled")); ?>
                        <input type="hidden" name="promotion_category" value="<?php echo Input::old('promotion_category', $promotion->promotion_category) ?>">
                    </div>
                </div>

                <div class="mws-form-row">
                    <label class="mws-form-label" for="name">Name</label>
                    <div class="mws-form-item">
                        <input type="text" class="small" name="name" id="name" value="<?php echo Input::old('name', $promotion->name); ?>">
                    </div>
                </div>

                <div class="mws-form-row">
                    <label class="mws-form-label" for="start_date">Period</label>
                    <div class="mws-form-item">
                        <?php $start_date = explode(" ", $promotion->start_date)?>
                        <input type="text" class="ssmall" name="start_date" value="<?php echo Input::old('start_date', $promotion->start_date); ?>" readonly>
                        To
                        <?php $end_date = explode(" ", $promotion->end_date)?>
                        <input type="text" class="ssmall datepicker" name="end_date" value="<?php echo Input::old('end_date', $promotion->end_date); ?>">
                    </div>
                </div>

				<div class="mws-form-row">
                    <label class="mws-form-label" for="code">Code</label>
                    <div class="mws-form-item">
                        <input type="text" class="small" name="code" id="code" value="<?php echo Input::old('code', $promotion->code); ?>" readonly>
                    </div>
                </div>

                <div class="mws-form-row">
                    <label class="mws-form-label" for="description">Detail</label>
                    <div class="mws-form-item">
                        <?php echo Form::ckeditor('description', Input::old('description',$promotion->description), array('id' => 'description', 'class' => 'form-control', 'height' => '150px')); ?>
                        <?php echo Form::transCkeditor($promotion, 'description', array('class' => 'form-control', 'height' => '150px')); ?>
                    </div>
                </div>

                <div class="mws-form-row">
                    <label class="mws-form-label" for="note">Note</label>
                    <div class="mws-form-item">
                        <textarea class="tarea small" name="note"><?php echo Input::old('note', @$promotion->note->detail); ?></textarea>
                    </div>
                </div>

                <div class="mws-form-row">
                    <label class="mws-form-label" for="note">Status</label>
                    <div class="mws-form-item">
                        <select name="status">
							<option value="activate"<?php echo ($promotion->status == 'activate')?' selected':''; ?>>Activate</option>
							<option value="deactivate"<?php echo ($promotion->status == 'deactivate')?' selected':''; ?>>Deactivate</option>
						</select>
                    </div>
                </div>

            </fieldset>





            <div class="mws-button-row">
                <input type="submit" class="btn btn-primary" value="Save">
				<a href="<?php echo URL::previous(); ?>" class="btn">Cancel</a>
            </div>

        </form>


    </div>
</div>