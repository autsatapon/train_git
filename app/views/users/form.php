<div class="container">
    <div class="mws-panel grid_8">
        <div class="mws-panel-header">
            <span><?php echo empty($user) ? 'Create user' : 'Edit user'; ?></span>
        </div>
        <div class="mws-panel-body no-padding">
            <?php echo Form::model($user, array('class' => 'mws-form', 'files' => true)); ?>

            <?php echo Theme::widget('CMessage', array('messages' => $errors, 'type' => 'error'))->render(); ?>

                <fieldset class="mws-form-inline">
                    <div class="mws-form-row bordered">
                        <label class="mws-form-label">Email</label>
                        <div class="mws-form-item">
                            <?php echo Form::text('email', null, array('class' => 'small')); ?>
                        </div>
                    </div>
                    <div class="mws-form-row bordered">
                        <label class="mws-form-label">Name</label>
                        <div class="mws-form-item">
                            <?php echo Form::text('display_name', null, array('class' => 'medium', 'placeholder' => 'Display name')); ?>
                        </div>
                    </div>
                    <div class="mws-form-row bordered">
                        <label class="mws-form-label">Group</label>
                        <div class="mws-form-item">
                            <select name="group">
                                <?php foreach($groups as $group): ?>
                                    <option value="<?php echo $group->id;?>"><?php echo $group->name; ?></option>                                
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="mws-form-inline">
                    <legend>Permission</legend>
                    <?php
                        if( ! empty($user) )
                            $user_apps = $user->apps->lists('id');
                    ?>
                    <?php foreach($apps as $app): ?>
                        <div class="mws-form-row bordered">
                            <?php
                                $checkbox_name = 'apps_'.$app->id;
                                echo Form::checkbox($checkbox_name, '1', Input::old($checkbox_name, ! empty($user) && in_array($app->id, $user_apps) ? '1' : null), array('id'=>'allowed-app-'.$app->id));
                            ?>
                            <label class="mws-form-label" for="allowed-app-<?php echo $app->id ?>"><?php echo $app->name; ?></label>
                        </div>
                    <?php endforeach; ?>
                </fieldset>
                <fieldset class="mws-form-inline">
                    <legend><?php echo empty($user) ? 'New' : 'Change'; ?> password</legend>

                    <?php
                        $placeholder = empty($user) ? 'Type password more than 5 length.' : 'Leave blank if you don\'t want to change it.';
                    ?>

                    <div class="mws-form-row bordered">
                        <label class="mws-form-label">New password</label>
                        <div class="mws-form-item">
                            <?php echo Form::password('password', '',
                                    array('class' => 'small', 'placeholder' => $placeholder)
                                ); ?>
                        </div>
                    </div>
                    <div class="mws-form-row bordered">
                        <label class="mws-form-label">Confirm password</label>
                        <div class="mws-form-item">
                            <?php echo Form::password('password2', '',
                                    array('class' => 'small', 'placeholder' => $placeholder)
                                ); ?>
                        </div>
                    </div>
                </fieldset>
                <div class="mws-button-row">
                    <input type="submit" value="<?php echo empty($user) ? 'Create' : 'Save'; ?>" class="btn btn-primary">
                    <input type="button" value="Back" class="btn " onclick="history.back();">
                </div>
            <?php echo Form::close(); ?>
        </div>
    </div>
</div>