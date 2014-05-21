<?php

return array(

    'connections' => array(

        // 'mysql' => array(
        //     'read' => array(
        //         'host' => 'localhost',
        //     ),
        //     'write' => array(
        //         'host' => 'localhost'
        //     ),
        //     'driver'    => 'mysql',
        //     'database'  => 'pcms',
        //     'username'  => 'root',
        //     'password'  => '',
        //     'charset'   => 'utf8',
        //     'collation' => 'utf8_general_ci',
        //     'prefix'    => '',
        // ),

        'redis' => array(
            'cluster' => false,
            'default' => array('host' => '192.168.120.160', 'port' => 6380, 'database' => 0),
        ),

    ),

);
