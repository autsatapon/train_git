<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><?php echo __('Meta for :name', array('name' => $app->name)); ?></span>
    </div>


    <?php echo Form::open(array('files' => true, 'url' => null, 'class' => 'mws-form')) ?>

        <div class="mws-panel-body no-padding">

            <?php echo HTML::message(); ?>

            <div class="mws-form-inline">

                <div class="mws-form-row">
                    <label class="mws-form-label" for="name"><?php echo __('Please select a meta key'); ?></label>
                    <div class="mws-form-item">
                        <?php echo Form::select('key', $types, Input::old('key'), array('id' => 'type', 'class' => 'select2 small')); ?>
                    </div>
                </div>

                <div id="form-lists">
                    <?php foreach ($metas as $meta) : ?>
                    <div class="mws-form-row hide" id="type-<?php echo $meta->key; ?>">
                        <label class="mws-form-label">&nbsp;</label>
                        <div class="mws-form-item">
                            <?php if ($meta->type == 'link') : ?>
                            <?php echo Form::text($meta->key, 'http://', array('class' => 'small')); ?>
                            <?php else : ?>
                            <?php echo call_user_func_array(array('Form', $meta->type), array($meta->key, Input::old($meta->key), array('class' => 'small'))); ?>
                            <?php endif; ?>
                            <!-- Help block -->
                            <?php if ($meta->description): ?>
                            <p class="help-block"><?php echo $meta->description; ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

            </div>

        </div>

        <div class="mws-button-row">
            <input type="submit" class="btn btn-primary" value="Save">
        </div>
    <?php echo Form::close(); ?>

</div>

<script>
    $(document).ready(function() {

        $('#type').on('change', function(){

            var theId = '#type-' + $(this).val();

            $('#form-lists').find(theId).show().siblings().hide();

        }).trigger('change');

    });
</script>