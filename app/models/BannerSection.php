<?php

class BannerSection extends PCMSModel {

	protected $softDelete = false;

	public static $rules = array(
		'name' => 'required|min:5',
		'app_id' => 'exists:apps,id',
	);

	public static $labels = array(
		'pkey' => 'Section Key',
		'app' => 'Application',
	);

	public function app()
	{
		return $this->belongsTo('PApp');
	}

}
