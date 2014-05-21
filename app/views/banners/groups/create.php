<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span>Create Banner Groups</span>
    </div>
    <div class="mws-panel-body no-padding">

        <?php if (Session::has('success')) : ?>
            <div class="alert alert-success">
                <span style="display:block;"><?php echo Session::get('success') ?></span>
            </div>
        <?php endif; ?>
		<?php if ($errors->count() > 0) : ?>
			<div class="alert alert-error">
				<?php foreach ($errors->all() as $error) : ?>
				    <span style="display:block;"><?php echo $error; ?></span>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

        <form method="post" action="<?php echo url('banners/groups/create'); ?>" class="mws-form">
            <div class="mws-form-inline">
                <div class="mws-form-row">
                    <label class="mws-form-label" for="banner_position">Position : </label>
                    <div class="mws-form-item">
                        <select name="banner_position_id" id="banner_position_id">
                            <option value="">== Select Position ==</option>
                            <?php foreach (BannerPosition::where('status_flg', 'Y')->get() as $key => $value) : ?>
                            <option value="<?php echo $value['id']; ?>" <?php echo (Input::old('banner_position_id') == $value['id']) ? '' : ""; ?>><?php echo $value['name']; ?></option>
                            <?php endforeach; ?>
                        </select>                        
                    </div>
                </div>
                <div class="mws-form-row">
                    <label class="mws-form-label" for="name">Group Name : </label>
                    <div class="mws-form-item">
                        <input type="text" class="small" name="name" id="name" value="<?php echo Input::old('name'); ?>">
                    </div>
                </div>
                <div class="mws-form-row">
                    <label class="mws-form-label" for="description">Description</label>
                    <div class="mws-form-item">
                        <?php echo Form::textArea('description', Input::old('description'), array('rows' => 5, 'cols' => 45)); ?>
                        <?php /*<textarea name="description" id="description" rows="5" cols="45"><?php echo Input::old('description'); ?></textarea>*/ ?>
                    </div>
                </div>
                <div class="mws-form-row">
                    <label class="mws-form-label" for="is_random">Display</label>
                    <div class="mws-form-item">
                        <input type="checkbox" name="is_random" id="is_random" value="Y" <?php echo (Input::old('is_random') == "Y") ? 'checked="checked"' : ""; ?>>
                        Random
                    </div>
                </div>
                <div class="mws-form-row">
                    <label class="mws-form-label" for="show_per_time">Show per time</label>
                    <div class="mws-form-item">
                        <?php /*
                        <?php echo Form::Input('show_per_time', Input::old('show_per_time'), array('id' => 'show_per_time')); ?>
                        */ ?>
                        <select name="show_per_time" id="show_per_time">
                            <?php for ($i = 1; $i < 20; $i++) : ?>
                            <option value="<?php echo $i; ?>" <?php echo (Input::old('show_per_time') == $i) ? 'selected="selected"' : ""; ?>><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                        ถ้าไม่ได้เซ็ทแบบสุ่ม จะแสดงตามลำดับที่แอดเข้าไป
                    </div>                        
                </div>                                
                <div class="mws-form-row">
                    <label class="mws-form-label" for="status_flg">Status</label>
                    <div class="mws-form-item">
                        <?php echo Form::select('status_flg', array('Y' => 'Enable', 'N' => 'Disable'), Input::old('status_flg'), array('id' => 'status_flg')); ?>
                        
                    </div>
                </div>
            </div>
            <div class="mws-button-row">
                <input type="submit" class="btn btn-primary" name="save-button" id="save-button" value="Create">
            </div>
        </form>


    </div>
</div>
