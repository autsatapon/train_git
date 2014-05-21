<?php

use Teepluss\Theme\Theme;
use Teepluss\Theme\Widget;

class WidgetOrderDetailCustomer extends Widget {

    /**
     * Widget template.
     *
     * @var string
     */
    public $template = 'orderdetailcustomer';

    /**
     * Arrtibutes pass from a widget.
     *
     * @var array
     */
    public $attributes = array(
		'user' => '',
		'order' => ''
	);

    /**
     * Code to start this widget.
     *
     * @return void
     */
    public function init(Theme $theme)
    {

    }

    /**
     * Logic given to a widget and pass to widget's view.
     *
     * @return array
     */
    public function run()
    {
		$attrs = $this->getAttributes();

		// only call center able to edit customer info
		$attrs['callcenter'] = $attrs['user']->hasAccess('track-Order.act-as-callcenter-to'); //($role == 7);
	    $attrs['fulfillment'] = $attrs['user']->hasAccess('track-Order.act-as-fulfillment-to'); //($role == 7);
	
        return $attrs;
    }

}