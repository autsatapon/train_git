<?php

class Verb extends Eloquent {

    protected $softDelete = false;

	public $timestamps = false;

	public static function getVid($name)
	{
		return VerbUtil::getVid($name);
	}

	public static function getVerb($vid)
	{
		return VerbUtil::getVerb($vid);
	}

	public function pcmsKey()
	{
		return $this->hasMany('PCMSKey', 'vid');
	}

}