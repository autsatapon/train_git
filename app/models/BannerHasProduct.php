<?php
/**
 * 
 * @name Banner has product [banner_has_products] Model file controller
 * @author x3dev
 * @since 27/01/2014 11.25
 */
 
class BannerHasProduct extends PCMSModel { 
	
	public $softDelete = FALSE; 
	
	public static $rules = array (
		'banner_id' => 'Required|Numeric'
	);
	

}