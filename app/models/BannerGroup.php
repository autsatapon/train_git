<?php
/**
 *  @author :  Preme W. <preme_won@truecorp.co.th>
 *  @since  :  Jan 16, 2014
 *  @version :  1.0
 *  @package   :  PCMS 
 *
 */
class BannerGroup extends PCMSModel { 
	public $softDelete = FALSE; 

	public static $rules = array(
		'name' 					=> 'Required',
		'banner_position_id' 	=> 'Required',
		'show_per_time' 		=> 'Required|Numeric',
		'status_flg' 			=> 'Required'

	);

	/**
	 *	@author: Preme W. <preme_won@truecorp.co.th>
	 *	@desc:   Trigger banner model on event creating and updating 
	 *	@params: None
	 *	@return: void 
	 *
	 */
	public static function boot()
	{
		parent::boot();

		static::creating(function($banner)
	    {
	       $banner->created_by = Sentry::getUser()->id; 
	    });

	    static::updating(function($banner)
	    {
	    	$banner->updated_by = Sentry::getUser()->id; 
	    });
	} 
	
	public function position()
	{
		return $this->belongsTo('BannerPosition', 'banner_position_id');
	}

	
}