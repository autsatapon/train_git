<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
    //
});


App::after(function($request, $response)
{
    //
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
    if (Auth::guest()) return Redirect::guest('login');
});


Route::filter('auth.basic', function()
{
    return Auth::basic();
});

Route::filter('auth.admin', function()
{
    if (!Sentry::check()) return Redirect::guest('auth/login');
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
    if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
    if (Session::token() != Input::get('_token'))
    {
        throw new Illuminate\Session\TokenMismatchException;
    }
});

/**
 * Check access control list.
 */
Route::filter('access.check', function($route, $request)
{
    /*
    // Current controller and action.
    list($controller, $action) = explode('@', $route->getAction());

    // Accesses for current route.
    $accesses = $controller::$accesses;

    // Having permission to check.
    if ($access = array_get($accesses, $action))
    {
        $accessAllowed = FALSE;

        $user = Sentry::getUser();

        if ( !empty($access['permissions']) )
        {
            $permissionArr = explode('|', $access['permissions']);
            $accessAllowed = ( $user->hasAnyAccess($permissionArr) ) ? TRUE : FALSE ;
        }
        elseif ( !empty($access['groups']) )
        {
            $groupNameArr = explode('|', $access['groups']);
            foreach ($groupNameArr as $groupName)
            {
                try
                {
                    $group = Sentry::getGroupProvider()->findByName($groupName);

                    if ( $user->inGroup($group) )
                    {
                        $accessAllowed = TRUE;
                        break;
                    }
                }
                catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e)
                {

                }
            }
        }

        if ( !$accessAllowed )
        {
            App::abort(401, 'Error Access');
        }
    }
    */

    $authUser = Sentry::getUser();

    //get name's controller and method
    $action = $route->getAction();

    $action = array_get($action, 'controller');

    list ($controllerName, $methodName) = explode('@', $action);

    $access = $controllerName::$access;

    // check rule in $access
    if ( ! empty($access[$methodName]))
    {
        // check user has access to method
        if ($authUser->hasAccess($access[$methodName]) === false)
        {
            App::abort(401, 'noaccess');
        }
    }
});