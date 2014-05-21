<?php

/**
 * Language helper.
 *
 * @param  string $line
 * @param  array  $args
 * @return string
 */
function __($line, $args = array())
{
    return preg_replace_callback('/:([a-z]+)/', function($bind) use ($args)
    {
        return isset($args[$bind[1]]) ? $args[$bind[1]] : $bind[0];
    }
    , $line);
}

function explodeFilter($delimiter, $string)
{
    $array = is_array($string) ? $string : explode($delimiter, trim($string));
    return array_values(array_map('trim', array_filter($array)));
}


function getFilterPkey(&$pkey, $table = null)
{
    if (! is_array($pkey))
    {
        $pkey = explodeFilter(',', $pkey);
    }

    $pkey = DB::table($table)->whereIn('pkey', $pkey)->lists('pkey');

    // $pcmsQuery = PCMSKey::whereIn('code', $pkey);

    // if ($table)
    // {
    //     $verb = Verb::whereName($table)->remember(60)->first();
    //     if (! $verb)
    //     {
    //         throw new Exception("Name {$table} in verbs table isn't exists.");
    //         // exception should throwed but for make sure - add after 2 lines
    //         $pkey = array();
    //         return $pkey;
    //     }

    //     // found verb
    //     $pcmsQuery->whereVid($verb->getKey());
    // }

    // $pkey = $pcmsQuery->lists('code');

    return $pkey;
}

/**
 * create date/time string from another string
 * @param  string  $format   format name or date() format
 * @param  string  $string   datetime string
 * @param  boolean $thaiData set true for swapping day and month
 * @return string            datetime string
 */
function strtodate($format, $string, $thaiData = false)
{
    switch ($format) {
        case 'date':
            $format = "Y-m-d";
            break;

        case 'time':
            $format = "H:i:s";
            break;

        case 'datetime':
            $format = "Y-m-d H:i:s";
            break;

        default:
            break;
    }

    if ($thaiData !== false)
    {
        // if I get date in thai fomat,
        // change it to international format
        // swapping day and month
        $delimiter = strpos($string, "/") !== false ? "/" : "-";
        if (preg_match("#^[0-9]+{$delimiter}[0-9]+{$delimiter}#", $string))
        {
            list($day, $month, $after) = explode($delimiter, $string, 3);

            // swap!
            $string = $month.$delimiter.$day.$delimiter.$after;
        }
    }

    $timestamp = is_numeric($string) ? $string : strtotime($string);

    return $timestamp === false ? null : date($format, $timestamp);
}

### exec_curl_url ####
function execcurlurl($url = NULL, $post = NULL, $method_type = 'get', $debug = FALSE, $timeout = 25)
{
	if ($method_type == 'get')
	{
		if ( ! empty($post))
		{
			$url .= '?' . http_build_query($post) . '';
		}
	}

	$curl = curl_init($url);
	if (is_resource($curl) === TRUE)
	{
		curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);

		$headers[] = 'charset=UTF-8';

		if (is_array($headers))
		{
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		}

		if ($method_type == 'post')
		{
			if ( ! empty($post))
			{
				//print_r($post);die;
				curl_setopt($curl, CURLOPT_POST, TRUE);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
			}
		}

		$result = curl_exec($curl);

		if ($debug == TRUE)
		{
			debug_curl($curl, $result);
		}
		curl_close($curl);
		return $result;
	}
	return FALSE;
}

function debug_curl($curl, $response)
{
	echo "<br /><br />=============================================<br/>\n";
	echo "<h2>CURL Test</h2>\n";
	echo "=============================================<br/>\n";
	echo "<h3>Response</h3>\n";
	echo "<code>" . nl2br(htmlentities($response)) . "</code><br/>\n\n";

	if (curl_error($curl))
	{
		echo "=============================================<br/>\n";
		echo "<h3>Errors</h3>";
		echo "<strong>Code:</strong> " . curl_errno($curl) . "<br/>\n";
		echo "<strong>Message:</strong> " . curl_error($curl) . "<br/>\n";
	}
	echo "=============================================<br/>\n";
	echo "<h3>Info</h3>";
	echo '<pre style="text-align:left;">';
	print_r(curl_getinfo($curl));
	echo "</pre>";
}

function getPaymentMethod($payment_method)
{
    switch (strtolower($payment_method))
    {
        case '8':
            return 'เค้าท์เตอร์เซอร์วิส';
            break;
        case '1':
            return 'บัตรเครดิต';
            break;
        case '2':
            return 'ทรูมันนี่ วอเลท';
            break;
        case '3':
            return 'ผ่อนชำระ';
            break;
        case '4':
            return 'ตู้ ATM';
            break;
        case '5':
            return 'iBanking';
            break;
        case '6':
            return 'เค้าท์เตอร์ธนาคาร';
            break;
        case '7':
            return 'จ่ายเมื่อได้รับสินค้า';
            break;
        default:
            return null;
            break;
    }

}


/**
 * Temp log.
 *
 * @param string $name
 * @param array  $data
 */
function LOGGER($name, $data)
{
    if (App::environment() != 'production')
    {
        Log::info($name, $data);
    }
}
