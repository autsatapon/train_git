<?php

use Teepluss\Theme\Theme;
use Teepluss\Theme\Widget;

class WidgetOrderDetailShipment extends Widget {

    /**
     * Widget template.
     *
     * @var string
     */
    public $template = 'orderdetailshipment';

    /**
     * Arrtibutes pass from a widget.
     *
     * @var array
     */
    public $attributes = array(
		'user' => '',
		'order' => '',
		'shipment' => '',
		'i' => ''
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

		// get vendor name
		$vendor = VVendor::whereVendorId($attrs['shipment']->vendor_id)->first();
		$attrs['ship_by'] = ($vendor)?$vendor->vendor:'-';

		// stock_type is 4 or 6 mean non-stock
		//$stock_type = ($attrs['shipment']->stock_type == 4 || $attrs['shipment']->stock_type == 6)?'non-stock':'stock';
      
      $stock_type_word = ($attrs['shipment']->stock_type == 4 || $attrs['shipment']->stock_type == 6)?'non-stock':'stock';
      
		$attrs['stock_type'] = ProductVariant::getStockType($attrs['shipment']->stock_type);

		// sourcing able to edit non-stock, logistic able to see stock
		$attrs['editable'] = (
            (
                ($attrs['user']->hasAccess('track-Order.act-as-sourcing-to') && $stock_type_word == 'non-stock') 
                || ($attrs['user']->hasAccess('track-Order.act-as-logistic-to') && $stock_type_word == 'stock')
            ) && ( $attrs['order']->order_status !== 'delivered' && $attrs['order']->order_status !== 'completed' && $attrs['order']->order_status !== 'done' )
        );

        if($attrs['shipment']->shipment_status==='packed')
        {
           $attrs['shipment_status'] = array(
                'packed' => 'Packed',
                'shipping' => 'Shipping',
                'preparing' => 'Prepairing',
             ); 
        }
        elseif($attrs['shipment']->shipment_status==='shipping')
        {
           $attrs['shipment_status'] = array(
                'shipping' => 'Shipping',
                'sent' => 'Sent',
            ); 
        }
        elseif($attrs['shipment']->shipment_status==='sent')
        {
            $attrs['shipment_status'] = array(
                'sent' => 'Sent',
                'delivered' => 'Delivered',
                'rejected' => 'Rejected',
            ); 
        }
        elseif($attrs['shipment']->shipment_status==='delivered')
        {
            $attrs['shipment_status'] = array(
                'delivered' => 'Delivered',
            ); 
        }
        elseif($attrs['shipment']->shipment_status==='rejected')
        {
            $attrs['shipment_status'] = array(
                'rejected' => 'Rejected',
                'preparing' => 'Prepairing',
            );
        }
        else
        {
            $attrs['shipment_status'] = array(
                'preparing' => 'Prepairing',
                'packed' => 'Packed',
                'waiting' => 'Waiting',
                'unpackable' => 'Unpackable',
            ); 
        }
        
        $attrs['editable'] = 1;

        return $attrs;
    }

}