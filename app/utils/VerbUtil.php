<?php

class VerbUtil {
	
	public static function getVid($name)
	{
		$name = str_plural(strtolower($name));

		if($name==false)
			return null;
		
		$key = "verb-name-$name";
		$vid = Cache::remember($key, 86400, function() use ($name)
		{
			$verb = Verb::whereName($name)->first();
			if($verb==false)
				throw new Exception("Verb $name not found");

			return $verb->id;
		});
		
		return $vid;
	}
	
	public static function getVerb($vid)
	{
		$vid = intval($vid);

		if($vid==false)
			return null;
		
		$key = "verb-$vid";
		$verb = Cache::remember($key, 86400, function() use ($vid)
		{
			$verb = Verb::find($vid);
			if($verb==false)
				return false;

			return $verb->name;
		});

		return $verb;
	}
	
}