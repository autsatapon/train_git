<?php

return array(

	'default' => 'mysql',

	'connections' => array(

		// 'mysql' => array(
		// 	'driver'    => 'mysql',
		// 	'host'      => 'localhost',
		// 	'database'  => 'pcms',
		// 	'username'  => 'pcms',
		// 	'password'  => 'UY6dn2tqGxuJae8h',
		// 	'charset'   => 'utf8',
		// 	'collation' => 'utf8_general_ci',
		// 	'prefix'    => '',
		// ),

		'mysql' => array(
			'read' => array(
		        'host' => 'localhost',
		    ),
		    'write' => array(
		        'host' => 'localhost'
		    ),
			'driver'    => 'mysql',
			'database'  => 'pcms',
			'username'  => 'pcms',
			'password'  => 'UY6dn2tqGxuJae8h',
			'charset'   => 'utf8',
			'collation' => 'utf8_general_ci',
			'prefix'    => '',
		),

		'itruemart' => array(
			'driver'    => 'mysql',
			'host'      => 'localhost',
			'database'  => 'pcms_test',
			'username'  => 'pcms',
			'password'  => 'UY6dn2tqGxuJae8h',
			'charset'   => 'utf8',
			'collation' => 'utf8_general_ci',
			'prefix'    => '',
		),

		'pcms_migrate' => array(
			'driver'    => 'mysql',
			'host'      => 'localhost',
			'database'  => 'pcms_migrate',
			'username'  => 'pcms',
			'password'  => 'UY6dn2tqGxuJae8h',
			'charset'   => 'utf8',
			'collation' => 'utf8_general_ci',
			'prefix'    => '',
		),
	),

);
