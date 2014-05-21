<?php
/**
 *  @author :  Preme W. <preme_won@truecorp.co.th>
 *  @since  :  Jan 21, 2014
 *  @version :  1.0
 *  @package   :  PCMS 
 */

class Banner extends PCMSModel {
	public $softDelete = FALSE; 

	public static $rules = array(
		'name' 					=> 'Required',
		'banner_type' 			=> 'Required',
		#'url_link' 				=> 'Required|url',
		'target' 				=> 'Required',
		'status_flg' 			=> 'Required'

	);


	public static $messages = array(
		'name.required' 			=> 'Banner Name is require',
		'banner_type.required' 		=> 'Banner Type is require',
		/* 'url_link.required' 		=> 'Link URL is require',
		'url_link.url' 				=> 'Link URL must be URL', */
		'target.required' 			=> 'Target is require',
		'status_flg.required' 		=> 'Status is require',
	);

	public function groups()
	{
		return $this->belongsTo('BannerGroup', 'banner_group_id');
	}

	/*
	* Blog has many files upload.
    *ss
    * @return AttachmentRelate
    */
   public function files()
   {
       return $this->morphMany('\Teepluss\Up\AttachmentRelates\Eloquent\AttachmentRelate', 'fileable');
   }
	
	public static function boot()
	{
		parent::boot();

		static::creating(function($model)
		{
		   $model->created_by = Sentry::getUser()->id;
		});
	}

	

}