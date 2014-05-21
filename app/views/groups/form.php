<div class="container">
    <div class="mws-panel grid_8">
        <div class="mws-panel-header">
            <span><?php echo ! $group ? 'Create' : 'Edit'; ?> group</span>
        </div>
        <div class="mws-panel-body no-padding">
            <?php echo Form::model($group, array('class' => 'mws-form', 'files' => true)); ?>


    <?php echo Theme::widget('CMessage', array('messages' => $errors, 'type' => 'error'))->render(); ?>

                <fieldset class="mws-form-inline">
                    <div class="mws-form-row bordered">
                        <label class="mws-form-label">Name</label>
                        <div class="mws-form-item">
                            <?php echo Form::input('text', 'name', null, array('class' => 'small')); ?>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="mws-form-inline">
                    <legend>Role Permission</legend>

                    <?php foreach ($rules as $controller => $perms): ?>
                        <?php foreach ($perms as $perm): ?>
                            <div class="mws-form-row bordered">
                                <label class="mws-form-label">Can <?php echo str_replace('-',' ',$perm); ?> <?php echo ucfirst($controller); ?>?</label>
                                <div class="mws-form-item">
                                    <?php
                                        $name = $controller.'_'.$perm;
                                        $varFromDB = (!empty($permissions[$controller.'.'.$perm])) ? $permissions[$controller.'.'.$perm] : '';
                                        echo Form::select($name,
                                               array('0' => 'Not allow', '1' => 'Allow'),
                                               Input::old($name, $varFromDB),
                                               array('class' => 'small')
                                           );
                                    ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </fieldset>

                <div class="mws-button-row">
                    <input type="submit" value="<?php echo ! $group ? 'Create' : 'Save'; ?>" class="btn btn-primary">
                    <input type="button" value="Cancel" class="btn " onclick="history.back();">
                </div>
            <?php echo Form::close(); ?>
        </div>
    </div>
</div>