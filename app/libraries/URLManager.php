<?php
class URLManager { 
	public static function iTruemartlevelDUrl($productName, $pkey)
	{
		$level_d_url = Config::get('url_manager.product_level_d_url');

		$search = array(
						'{PRODUCT_SLUG}', '{PKEY}'
					);

		$replace= array(
						static::getSlug($productName), $pkey
					);

		return Config::get('url_manager.url_itruemart') . str_replace($search, $replace, $level_d_url);
	}

	private static function getSlug($str, $separator = 'dash')
	{
		if ($separator == 'dash')
		{
			$search		= '_';
			$replace	= '-';
		}
		else
		{
			$search		= '-';
			$replace	= '_';
		}

		$str = strtolower($str); 
		
		$trans = array(
						$search								=> $replace,
						"\""							=> '',
						'\\\\'							=> '',
						'\s+'							=> $replace,
						'\/'							=> $replace,
						" "								=> $replace,
						"\."								=> '',
						"\#"							=> '',
						"\("								=> '',
						"\)"								=> '',
						"\?"								=> '',
						"\:"								=> '',
						"\;"								=> '',
						"\'"								=> '',
						","									=> '',
						"\&"									=> '',
						"\%"								=> '',
						"@"								=> '',
						"\*"								=> '',
						"\+"								=> '',
						$replace."+"						=> $replace,
						$replace."$"						=> '',
						"^".$replace						=> ''
					   );

		foreach ($trans as $key => $val)
		{
			$str = preg_replace("/".$key."/", $val, $str);
		}
		$str = strtolower($str); 

		return trim(stripslashes($str));
	}
}