Hello! Develop branch
<div id="mws-login-wrapper">

    <?php echo Theme::widget('CMessage', array('messages' => $errors, 'type' => 'error'))->render(); ?>
    <?php echo Theme::widget('CMessage', array('messages' => $error, 'type' => 'error'))->render(); ?>
    <?php echo Theme::widget('CMessage', array('messages' => $success, 'type' => 'success'))->render(); ?>

    <div id="mws-login">
        <h1>Login</h1>
        <div class="mws-login-lock"><i class="icon-lock"></i></div>
        <div id="mws-login-form">
            <?php echo Form::open(array('class' => 'mws-form', 'files' => true)); ?>
                <div class="mws-form-row">
                    <div class="mws-form-item">
                        <input type="text" name="email" class="mws-login-username required" placeholder="email" value="<?php echo Input::old('email'); ?>">
                    </div>
                </div>
                <div class="mws-form-row">
                    <div class="mws-form-item">
                        <input type="password" name="password" class="mws-login-password required" placeholder="password">
                    </div>
                </div>
                <div id="mws-login-remember" class="mws-form-row mws-inset">
                    <ul class="mws-form-list inline">
                        <li>
                            <input id="remember" name="remember" type="checkbox">
                            <label for="remember">Remember me</label>
                        </li>
                    </ul>
                </div>
                <div class="mws-form-row">
                    <input type="submit" value="Login" class="btn btn-success mws-login-button">
                    <a href="<?php echo URL::action('AuthController@getResetpw');?>">Reset password</a>
                </div>
            <?php echo Form::close(); ?>
        </div>
        <span style="color:#fff">PCMS version <?php echo Config::get('app.version') ?></span>
    </div>
</div>