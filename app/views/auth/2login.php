<div id="mws-login-wrapper">
    <div id="mws-login">
        <h1>Login</h1>
        <div class="mws-login-lock"><i class="icon-lock"></i></div>
        <div id="mws-login-form">

            <?php if (!empty($auth_errors)) { ?>
                <div class="alert alert-error" style="width:210px; margin:5px auto;">
                    <?php echo $auth_errors ?>
                </div>
            <?php } ?>

            <form class="mws-form" action="<?php echo URL::to('login') ?>" method="post">
                <div class="mws-form-row">
                    <div class="mws-form-item">
                        <?php /* <input type="text" name="username" class="mws-login-username required" placeholder="username"> */ ?>
                        <input type="text" name="email" class="mws-login-email required" placeholder="email">
                    </div>
                </div>
                <div class="mws-form-row">
                    <div class="mws-form-item">
                        <input type="password" name="password" class="mws-login-password required" placeholder="password">
                    </div>
                </div>
                <?php /*
                <div id="mws-login-remember" class="mws-form-row mws-inset">
                    <ul class="mws-form-list inline">
                        <li>
                            <input id="remember" type="checkbox">
                            <label for="remember">Remember me</label>
                        </li>
                    </ul>
                </div>
                */ ?>
                <div class="mws-form-row">
                    <input type="submit" value="Login" class="btn btn-large btn-success mws-login-button">
                </div>
            </form>
        </div>
    </div>
</div>