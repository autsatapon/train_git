<?php

/*
|--------------------------------------------------------------------------
| Register The Laravel Class Loader
|--------------------------------------------------------------------------
|
| In addition to using Composer, you may use the Laravel class loader to
| load your controllers and models. This is useful for keeping all of
| your classes in the "global" namespace without Composer updating.
|
*/

ClassLoader::addDirectories(array(

    app_path().'/core',
    app_path().'/utils',
    app_path().'/models',
	app_path().'/commands',
    app_path().'/libraries',
	app_path().'/controllers',
    app_path().'/apisearch',
    app_path().'/repositories',
	app_path().'/database/seeds'

));

/*
|--------------------------------------------------------------------------
| Application Error Logger
|--------------------------------------------------------------------------
|
| Here we will configure the error logger setup for the application which
| is built on top of the wonderful Monolog library. By default we will
| build a rotating log file setup which creates a new file each day.
|
*/

$logFile = 'log-'.php_sapi_name().'.txt';

Log::useDailyFiles(storage_path().'/logs/'.$logFile);

/*
|--------------------------------------------------------------------------
| Application Error Handler
|--------------------------------------------------------------------------
|
| Here you may handle any errors that occur in your application, including
| logging them or displaying custom views for specific errors. You may
| even register several error handlers to handle different types of
| exceptions. If nothing is returned, the default error view is
| shown, which includes a detailed stack trace during debug.
|
*/

use Illuminate\Database\Eloquent\ModelNotFoundException;

App::error(function(ModelNotFoundException $e)
{
    LOGGER('ModelNotFound', DB::getQueryLog());

    if (Request::segment(1) == 'api')
    {
        return API::createResponse('Data not found.', 404);
    }
    else
    {
        return Response::make('Data Not Found.', 404);
    }
});

App::error(function(Exception $exception, $code)
{
    switch($code) {
        case 401 : return Response::make('You don\'t have permission to access this area.', 401); break;
        case 408 : return Response::make('408 Error', 408); break;
//        default  : return 'Unknown error'; break;
    }

	Log::error($exception);
});

App::missing(function()
{
    return Response::make('404 NOT FOUND', 404);
});

/*
|--------------------------------------------------------------------------
| Maintenance Mode Handler
|--------------------------------------------------------------------------
|
| The "down" Artisan command gives you the ability to put an application
| into maintenance mode. Here, you will define what is displayed back
| to the user if maintenace mode is in effect for this application.
|
*/

App::down(function()
{
	return Response::make("Be right back!", 503);
});

/*
|--------------------------------------------------------------------------
| Require The Filters File
|--------------------------------------------------------------------------
|
| Next we will load the filters file for the application. This gives us
| a nice separate location to store our route and application filter
| definitions instead of putting them all in the main routes file.
|
*/

require app_path().'/filters.php';

/**
 * Include application logic.
 */
require app_path().'/boots/start.php';