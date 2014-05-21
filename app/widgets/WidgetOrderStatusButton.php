<?php

use Teepluss\Theme\Theme;
use Teepluss\Theme\Widget;

class WidgetOrderStatusButton extends Widget {

    /**
     * Widget template.
     *
     * @var string
     */
    public $template = 'orderstatusbutton';

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
		$actions = array();

		if ($attrs['user']->hasAccess('track-Order.act-as-fulfillment-to'))
		{
			if( $attrs['order']->order_status==='new' || $attrs['order']->order_status==='paid' )
			{
				$actions = array(
					'check' => 'Check',
					'add_gift' => 'Add Gift',
					'refund' => 'Refund',
				);
			}
			elseif( $attrs['order']->order_status==='delivered' || $attrs['order']->order_status==='sent' || $attrs['order']->order_status==='refund' )
			{
				$actions = array(
					'finish' => 'Complete'
				);
			}
		}
		else if (
			$attrs['user']->hasAccess('track-Order.act-as-sourcing-to') ||
			$attrs['user']->hasAccess('track-Order.act-as-logistic-to')
		)
		{
			if( $attrs['order']->order_status==='ready' )
			{
				$actions = array(
					'shipping' => 'Shipping',
				);
			}
			elseif( $attrs['order']->order_status==='shipping' )
			{
				$actions = array(
					'sent' => 'Sent',
				);
			}
			elseif( $attrs['order']->order_status==='unshippable' )
			{
				$actions = array(
					'refund' => 'Refund',
					'ready' => 'Ready',
					'waiting' => 'Waiting',
				);
			}
			elseif( $attrs['order']->order_status==='checked' || $attrs['order']->order_status==='unshippable' || $attrs['order']->order_status==='waiting' )
			{
				$actions = array(
					'ready' => 'Ready',
					'unshippable' => 'Unshippable',
					'waiting' => 'Waiting',
				);
			}
		}
		else if ($attrs['user']->hasAccess('track-Order.act-as-callcenter-to'))
		{
			if( $attrs['order']->order_status==='paid' || ($attrs['order']->payment_channel==='COD' && $attrs['order']->order_status==='new') )
			{
				$actions = array(
					'confirm_address' => 'Confirm address',
					'unreachable' => 'Unreachable',
					'call_later' => 'Call later',
					//'' => 'Not contact yet'
				);
			}
		}

		$attrs['actions'] = $actions;

        return $attrs;
    }

}