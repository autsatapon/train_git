<?php

class SafeDebug {

	protected static function notProduction()
	{
		return strtolower(App::environment()) !== 'production';
	}

	public static function d($data, $activation = null)
	{
		if (static::notProduction() && ($activation === null || Input::has($activation)))
		{
			d($data);
		}
	}
	public static function dd($data, $activation = null)
	{
		if (static::notProduction() && ($activation === null || Input::has($activation)))
		{
			dd($data);
		}
	}
	public static function sd($data, $activation = null)
	{
		if (static::notProduction() && ($activation === null || Input::has($activation)))
		{
			sd($data);
		}
	}

}