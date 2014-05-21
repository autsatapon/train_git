<div class="container">
    <div class="mws-panel grid_8">
        <div class="mws-panel-header">
            <span><?php echo 'Edit permissions' ?></span>
        </div>
        <div class="mws-panel-body no-padding">
            <?php echo Form::open(array('class' => 'mws-form', 'files' => true)); ?>

    <?php echo Theme::widget('CMessage', array('messages' => $errors, 'type' => 'error'))->render(); ?>

                <fieldset class="mws-form-inline">
                    <div class="mws-form-row bordered">
                        <label class="mws-form-label"><?php echo 'Email' ?></label>
                        <div class="mws-form-item">
                            <?php echo Form::input('text', 'email',
                                    Input::old('email', $user->email),
                                    array('class' => 'small', 'disabled' => 'disabled')
                                ); ?>
                        </div>
                    </div>
                    <div class="mws-form-row bordered">
                        <label class="mws-form-label">Role</label>
                        <div class="mws-form-item">
                            <?php
                                $group_value = array('no' => 'Not in any role');
                                foreach($groups as $group) {
                                    $group_value[$group->id] = $group->name;
                                }
                                $group = $user->groups()->first();
                                $current_group = $group ? $group->id : null;
                                echo Form::select('group', $group_value, $current_group, array('class' => 'small'));?>
                        </div>
                    </div>
                </fieldset>

                <?php if($rules): ?>

                <fieldset class="mws-form-inline">
                    <legend>User Permissions</legend>

                    <?php foreach ($rules as $controller => $perms): ?>
                        <?php foreach ($perms as $perm): ?>
                            <div class="mws-form-row bordered">
                                <label class="mws-form-label">Can <?php echo $perm; ?> <?php echo ucfirst($controller); ?>?</label>
                                <div class="mws-form-item">
                                    <?php
                                        $name = $controller.'_'.$perm;
                                        $varFromDB = (!empty($permissions[$controller.'.'.$perm])) ? $permissions[$controller.'.'.$perm] : '';
                                        echo Form::select($name,
                                               array('0' => 'Inherit', '1' => 'Allow', '-1' => 'Not allow'),
                                               Input::old($name, $varFromDB),
                                               array('class' => 'small')
                                           );
                                    ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </fieldset>

                <?php endif; ?>

                <div class="mws-button-row">
                    <input type="submit" value="Save" class="btn btn-danger">
                    <input type="button" value="Cancel" class="btn " onclick="history.back();">
                </div>
            <?php echo Form::close(); ?>
        </div>
    </div>
</div>