<?php

return array(

	'connections' => array(

		'mysql' => array(
            'read' => array(
                'database'  => 'pcms_db',
                'host' => '192.168.134.15',
                'username'  => 'pcms_rw',
                'password'  => 'X4PbKt5bz132',
                // 'username'  => 'pcms_ro',    <= for phpMyAdmin
                // 'password'  => 'gBb878NSR846',
            ),
            'write' => array(
                'database'  => 'pcms_db',
                'host' => '192.168.134.14',
                'username'  => 'pcms_rw',
                'password'  => 'X4PbKt5bz132',
            ),
		),

	),

    'redis' => array(
        'cluster' => false,
        'default' => array('host' => '192.168.120.160', 'port' => 6381, 'database' => 0),
    ),

);
