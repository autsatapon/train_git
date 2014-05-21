<div id="mws-login-wrapper">
    <?php echo Theme::widget('CMessage', array('messages' => $errors, 'type' => 'error'))->render(); ?>
    <?php echo Theme::widget('CMessage', array('messages' => $error, 'type' => 'error'))->render(); ?>

    <div id="mws-login">
        <h1>Reset password</h1>
        <div class="mws-login-lock"><i class="icon-lock"></i></div>
        <div id="mws-login-form">
            <form class="mws-form" method="post">
                <div class="mws-form-row">
                    <div class="mws-form-item">
                        <input type="text" name="email" class="mws-login-username required" placeholder="email" disabled="disabled" value="<?php echo Input::old('email', $user->email); ?>">
                    </div>
                </div>
                <div class="mws-form-row">
                    <div class="mws-form-item">
                        <input type="password" name="password" class="mws-login-password required" placeholder="New password" value="<?php echo Input::old('password'); ?>">
                    </div>
                </div>
                <div class="mws-form-row">
                    <div class="mws-form-item">
                        <input type="password" name="password_confirmation" class="mws-login-password required" placeholder="Confirm password" value="<?php echo Input::old('password_confirmation'); ?>">
                    </div>
                </div>
                <div class="mws-form-row">
                    <input type="submit" value="Reset" class="btn btn-success mws-login-button">
                </div>
            </form>
        </div>
    </div>
</div>