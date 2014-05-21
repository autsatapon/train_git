<?php
    $user = Sentry::getUser();
?>

<!-- Header -->
<div id="mws-header" class="clearfix">

    <!-- Logo Container -->
    <div id="mws-logo-container">
        <!-- Logo Wrapper, images put within this wrapper will always be vertically centered -->
        <div id="mws-logo-wrap">
            PCMS Admin
            <div style="color:#fff;font-size:14px;">version <?php echo Config::get('app.version') ?></div>
        </div>
    </div>

    <!-- User Tools (notifications, logout, profile, change password) -->
    <div id="mws-user-tools" class="clearfix">
        <!-- User Information and functions section -->
        <div id="mws-user-info" class="mws-inset">
            <!-- User Photo -->
            <!--div id="mws-user-photo">
                <img src="example/profile.jpg" alt="User Photo">
            </div-->
            <!-- Username and Functions -->
            <div id="mws-user-functions">
                <div id="mws-username">Hello, <?php echo $user->display_name; ?></div>
                <ul>
                    <li><a href="#">Profile</a></li>
                    <li><a href="<?php echo URL::action('AuthController@getLogout'); ?>">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>

</div>