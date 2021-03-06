<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Base path.
    |--------------------------------------------------------------------------
    |
    | Full path to view your image
    | http://...image.jpg
    |
    */

    'baseUrl' => 'http://cdn.2014.itruemart.com/pcms/uploads',
    // 'baseUrl' => 'http://cdn.alpha.itruemart.com/pcms',
    // 

    /*
    |--------------------------------------------------------------------------
    | Base storage dir.
    |--------------------------------------------------------------------------
    |
    | Base directory to store uploaded files.
    |
    */

    'baseDir' => '/data/product/itruemart/pcms/public/uploads2014',
    // 'baseDir' => '/data/product/itruemart/pcms2014/public/uploads',
    // 'baseDir' => '/data/product/itruemart/pcms/public',

    /*
    |--------------------------------------------------------------------------
    | Append sub directory to 'base_dir'
    |--------------------------------------------------------------------------
    |
    | You can append a sub directories to base path
    | this allow you to use 'Closure'.
    |
    */

    'subpath' => function()
    {
        // [WRITE] '/data/product/itruemart/pcms/public/uploads';
        // [READ] 'http://cdn.[ENV].itruemart.com/pcms/uploads';
        return date('y-m-j');
        // return 'uploads'.DIRECTORY_SEPARATOR.date('y-m-j');
    },

    /*
    |--------------------------------------------------------------------------
    | All scales to resize.
    |--------------------------------------------------------------------------
    |
    | For image uploaded you can resize to
    | selected or whole of scales.
    |
    */

    'scales' => array(
        // 'wm' => array(260, 180),
        // 'wl' => array(300, 200),
        // 'wx' => array(360, 270),
        // 'ww' => array(260, 120),
        // 'ws' => array(160, 120),
        'xl' => array(1000, 1000),
        'l'  => array(350, 350),
        'm'  => array(200, 200),
        's'  => array(64, 64),
        // 'ss' => array(45, 45),
        'square' => array(150, 150)
    ),

    /*
    |--------------------------------------------------------------------------
    | Callback on each file uploaded.
    |--------------------------------------------------------------------------
    |
    | This should be closure to listen when each file uploaded.
    |
    */

    'onUpload' => function()
    {
        // $up_number = TableConfig::whereKey('up-number')->first();
        // $up_counter = TableConfig::whereKey('up-counter')->first();

        // return
    },

    /*
    |--------------------------------------------------------------------------
    | Callback on all files uploaded.
    |--------------------------------------------------------------------------
    |
    | This should be closure to listen when all files uploaded.
    |
    */

    'onComplete' => null,

    /*
    |--------------------------------------------------------------------------
    | Callback on all files deleted.
    |--------------------------------------------------------------------------
    |
    | This should be closure to listen when file deleted.
    |
    */

    'onRemove' => null,

);