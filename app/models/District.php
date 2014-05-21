<?php

class District extends Eloquent
{

	public $timestamps = false;

	public function province()
	{
		return $this->belongsTo('Province');
	}

	public function city()
	{
		return $this->belongsTo('City');
	}

	public function country()
	{
		return $this->province->country;
	}

}
