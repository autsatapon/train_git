<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Inherit from another theme
    |--------------------------------------------------------------------------
    |
    | Set up inherit from another if the file is not exists,
    | this is work with "layouts", "partials", "views" and "widgets"
    |
    | [Notice] assets cannot inherit.
    |
    */

    'inherit' => null, //default

    /*
    |--------------------------------------------------------------------------
    | Listener from events
    |--------------------------------------------------------------------------
    |
    | You can hook a theme when event fired on activities
    | this is cool feature to set up a title, meta, default styles and scripts.
    |
    | [Notice] these event can be override by package config.
    |
    */

    'events' => array(

        // Listen on event before render theme.
        'beforeRenderTheme' => function($theme)
        {
            $theme->asset()->usePath()->add('bootstrap-css', 'bootstrap/css/bootstrap.min.css');
            $theme->asset()->usePath()->add('fonts-ptsans-style', 'css/fonts/ptsans/stylesheet.css');
            $theme->asset()->usePath()->add('fonts-icomoon-style', 'css/fonts/icomoon/style.css');

            $theme->asset()->usePath()->add('style-mws-style', 'css/mws-style.css');
            $theme->asset()->usePath()->add('style-mws-theme', 'css/mws-theme.css', array('jui-css', 'jui-custom'));

            $theme->asset()->usePath()->add('style-pcms', 'css/pcms.css');

            $theme->asset()->usePath()->add('jquery', 'js/libs/jquery-1.8.3.min.js');
            $theme->asset()->container('footer')->usePath()->add('jquery-placeholder', 'js/libs/jquery.placeholder.min.js');

            $theme->asset()->add('pcms-validator', URL::to('/js/pcms-validator.js'));

            // You can remove this line anytime.
            // $theme->setTitle('Copyright ©  2013 - Laravel.in.th.');

            // You may use this event to set up your assets.
            //$theme->asset()->usePath()->add('core', 'core.js');
            //$theme->asset()->add('jquery', 'vendor/jquery/jquery.min.js');
            //$theme->asset()->add('jquery-ui', 'vendor/jqueryui/jquery-ui.min.js', array('jquery'));


            // Breadcrumb template.
            // $theme->breadcrumb()->setTemplate('
            //     <ul class="breadcrumb">
            //     @foreach ($crumbs as $i => $crumb)
            //         @if ($i != (count($crumbs) - 1))
            //         <li><a href="{{ $crumb["url"] }}">{{ $crumb["label"] }}</a><span class="divider">/</span></li>
            //         @else
            //         <li class="active">{{ $crumb["label"] }}</li>
            //         @endif
            //     @endforeach
            //     </ul>
            // ');


            // $theme->partialComposer('header', function($view)
            // {
            //     $view->with('auth', Auth::user());
            // });
        },

        'beforeRenderLayout' => array(

            'default' => function($theme)
            {

            },

            'admin-auth' => function($theme)
            {
                $theme->asset()->usePath()->add('style-login', 'css/login.css');
                $theme->asset()->container('footer')->usePath()->add('js-login', 'js/core/login.js');
                $theme->asset()->container('footer')->usePath()->add('custom-plugins-fileinput', 'custom-plugins/fileinput.js');
                $theme->asset()->container('footer')->usePath()->add('jui', 'jui/js/jquery-ui-effects.min.js');
                $theme->asset()->container('footer')->usePath()->add('jquery-validate', 'plugins/validate/jquery.validate-min.js');
            },

            'admin-dashboard' => function($theme)
            {
                $theme->asset()->usePath()->add('icol16', 'css/icons/icol16.css');
                $theme->asset()->usePath()->add('icol32', 'css/icons/icol32.css');

                $theme->asset()->container('footer')->usePath()->add('jquery-mousewheel', 'js/libs/jquery.mousewheel.min.js');
                $theme->asset()->container('footer')->usePath()->add('custom-plugins-fileinput', 'custom-plugins/fileinput.js');

                $theme->asset()->container('footer')->usePath()->add('jui', 'jui/js/jquery-ui-1.9.2.min.js');

                $theme->asset()->container('footer')->usePath()->add('jui-custom', 'jui/jquery-ui.custom.min.js');
                $theme->asset()->container('footer')->usePath()->add('jui-touch-punch', 'jui/js/jquery.ui.touch-punch.js');

                $theme->asset()->usePath()->add('jui-css', 'jui/css/jquery.ui.all.css');
                $theme->asset()->usePath()->add('jui-css-custom', 'jui/jquery-ui.custom.css');

                $theme->asset()->container('footer')->usePath()->add('bootstrap-js', 'bootstrap/js/bootstrap.min.js');
                $theme->asset()->container('footer')->usePath()->add('mws-js', 'js/core/mws.js');
                $theme->asset()->container('footer')->usePath()->add('extend', 'js/core/extend.js');
            },
                
            'popup-iframe' => function($theme)
            {
                $theme->asset()->container('footer')->usePath()->add('jui', 'jui/js/jquery-ui-1.9.2.min.js');
                
                $theme->asset()->usePath()->add('jui-css', 'jui/css/jquery.ui.all.css');
                $theme->asset()->usePath()->add('jui-css-custom', 'jui/jquery-ui.custom.css');

                $theme->asset()->container('footer')->usePath()->add('bootstrap-js', 'bootstrap/js/bootstrap.min.js');
            },

        )

    )

);