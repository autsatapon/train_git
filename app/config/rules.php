<?php

return array(
	'user' => array('read', 'write'),
    'role' => array('read', 'write'),
	'product' => array('read', 'write', 'delete'),
	'dashboard' => array('view-new-material', 'view-wait-approve-product', 'view-rejected-product'),
	'track-Order' => array('act-as-fulfillment-to', 'act-as-sourcing-to', 'act-as-logistic-to', 'act-as-callcenter-to'),
);
