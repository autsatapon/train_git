<?php

class Cron extends Eloquent {

	public static function getValue($key, $default = null)
	{
		$cron = self::where('key', $key)->first();
		if ($cron == false)
			return $default;

		return $cron->value;
	}

	public static function setValue($key, $value = null)
	{
		$cron = self::where('key', $key)->first();
		if ($cron == false)
		{
			$cron = new Cron;
			$cron->key = $key;
		}

		$cron->value = $value;
		$cron->save();
	}

}