<?php

/*
|--------------------------------------------------------------------------
| Html Macro
|--------------------------------------------------------------------------
|
| Extending custom methods for HTML.
|
*/

require app_path('boots/macros/HTML.php');

/*
|--------------------------------------------------------------------------
| Form Macro
|--------------------------------------------------------------------------
|
| Extending custom methods for Form.
|
*/

require app_path('boots/macros/Form.php');

/*
|--------------------------------------------------------------------------
| Validator Extended
|--------------------------------------------------------------------------
|
| Extending validations.
|
*/

require_once app_path('boots/extended/Validator.php');

/*
|--------------------------------------------------------------------------
| Useful Helpers
|--------------------------------------------------------------------------
|
| Application helpers.
|
*/

require_once app_path('boots/helpers/global.php');
require_once app_path('boots/helpers/migrate.php');
require_once app_path('boots/helpers/locale.php');

/*
|--------------------------------------------------------------------------
| Repository binded.
|--------------------------------------------------------------------------
|
| Application repositories.
|
*/

require app_path('boots/binded.php');

/*
|--------------------------------------------------------------------------
| Repository binded.
|--------------------------------------------------------------------------
|
| Application repositories.
|
*/

require app_path('boots/events.php');

/*
|--------------------------------------------------------------------------
| IoC
|--------------------------------------------------------------------------
|
| Application IoC.
|
*/

require app_path('boots/IoC.php');