<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><?php echo __('Meta for :name', array('name' => $metadata->app->name)); ?></span>
    </div>


    <?php echo Form::open(array('files' => true, 'url' => null, 'class' => 'mws-form')) ?>

        <div class="mws-panel-body no-padding">

            <?php echo HTML::message(); ?>

            <div class="mws-form-inline">

                <div class="mws-form-row">
                    <label class="mws-form-label" for="name"><?php echo __('Key'); ?></label>
                    <div class="mws-form-item">
                        <input type="text" name="key" value="<?php echo $metadata->key; ?>" class="small" readonly="readonly">
                    </div>
                </div>

                <div id="form-lists">

                    <div class="mws-form-row">
                        <label class="mws-form-label">&nbsp;</label>
                        <div class="mws-form-item">
                            <?php $type  = ($metadata->type == 'link') ? 'text' : $metadata->type; ?>
                            <?php $value = ($metadata->type == 'file') ? array() : $metadata->value; ?>
                            <?php echo call_user_func_array(array('Form', $type), array('value', $value, array('class' => 'small'))); ?>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <div class="mws-button-row">
            <input type="submit" class="btn btn-primary" value="Save">
        </div>
    <?php echo Form::close(); ?>

</div>