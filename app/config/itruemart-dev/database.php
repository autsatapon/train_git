<?php

echo 'load';

return array(

	'connections' => array(

		'mysql' => array(
			'read' => array(
                'host' => '192.168.224.2',
            ),
            'write' => array(
                'host' => '192.168.224.2'
            ),
			'driver'    => 'mysql',
			'database'  => 'pcms_db',
			'username'  => 'pcms_app',
			'password'  => 'k9hkg[l',
			'charset'   => 'utf8',
			'collation' => 'utf8_general_ci',
			'prefix'    => '',
		),
	),

);
