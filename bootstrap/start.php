<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

$app = new Illuminate\Foundation\Application;

/*
|--------------------------------------------------------------------------
| Detect The Application Environment
|--------------------------------------------------------------------------
|
| Laravel takes a dead simple approach to your application environments
| so you can just specify a machine name or HTTP host that matches a
| given environment, then we will automatically detect it for you.
|
*/

$env = $app->detectEnvironment(function()
{
    $environments = array(
        'dev'               => array('pcms.com'),
        'local-test'        => array('MacPro.local','EThaiZone-PC', 'Tum', 'Siravits-MacBook-Air.local', 'iGetWeb', 'ouang-PC','boonkuae-boos-MacBook-Pro.local', 'Kamols-MacBook-Air.local'),
        'local'             => array('pcms.loc', 'www.pcms.loc', 'pcms.truelife.com'),
        'office'            => array('pcms.igetapp.com', 'pcms-true.igetapp.com', 'pcms.dev.igetapp.com', 'pcms-true-dev.igetapp.com'),
        'itruemart-dev'     => array('pcms.dev.itruemart.com', 'api-pcms.dev.itruemart.com'),
        'itruemart-alpha'   => array('pcms.alpha.itruemart.com', '10.98.34.25', '192.168.225.2', 'api-pcms.alpha.itruemart.com'),
        'itruemart-beta'    => array('pcms.2014.itruemart.com', 'pcms.2014-2.itruemart.com', '10.98.34.24', '192.168.121.188'),
        'production'        => array('pcms.itruemart.com'),
    );

    $domain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null;

    $hostname = gethostname();

    foreach ($environments as $environment => $hosts)
    {
        // To determine the current environment, we'll simply iterate through the possible
        // environments and look for the host that matches the host for this request we
        // are currently processing here, then return back these environment's names.
        foreach ((array) $hosts as $host)
        {
            if (str_is($host, $domain) || ($host == $hostname)) return $environment;
        }
    }

    return 'production';
});

/*
|--------------------------------------------------------------------------
| Bind Paths
|--------------------------------------------------------------------------
|
| Here we are binding the paths configured in paths.php to the app. You
| should not be changing these here. If you need to change these you
| may do so within the paths.php file and they will be bound here.
|
*/

$app->bindInstallPaths(require __DIR__.'/paths.php');

/*
|--------------------------------------------------------------------------
| Load The Application
|--------------------------------------------------------------------------
|
| Here we will load the Illuminate application. We'll keep this is in a
| separate location so we can isolate the creation of an application
| from the actual running of the application with a given request.
|
*/

$framework = $app['path.base'].'/vendor/laravel/framework/src';

require $framework.'/Illuminate/Foundation/start.php';

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;
