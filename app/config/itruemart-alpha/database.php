<?php

return array(

	'connections' => array(

		'mysql' => array(
			'driver'    => 'mysql',
			'read' => array(
                'host' => '192.168.225.2',
            ),
            'write' => array(
                'host' => '192.168.225.2'
            ),
			'database'  => 'pcms_db',
			'username'  => 'pcms_app',
			'password'  => 'FDewe923l',
			'charset'   => 'utf8',
			'collation' => 'utf8_general_ci',
			'prefix'    => '',
		),
	),

	'redis' => array(
	    'cluster' => false,
	    'default' => array('host' => '192.168.225.2', 'port' => 6379, 'database' => 0),
	),

);
