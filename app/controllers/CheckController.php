<?php set_time_limit(0);

class CheckController extends BaseController {

    public function getEnvironment()
    {
        s(App::environment(), Config::get('database.connections.mysql'));
    }

    public function getResolve()
    {
        $services = array(
            'truesms' => array(
                'name'   => 'True SMS',
                'url'    => Config::get('endpoints.truesms.endpoint'),
                'method' => 'ping',
            ),
            'truecard-agentUrl' => array(
                'name'   => 'True Card',
                'url'    => Config::get('endpoints.truecard.agentUrl').'?method=get_card_information&thaiid=5300799036049',
                'method' => 'get',
            ),
        );

        foreach ($services as $key => $service)
        {

            if($service['method'] == 'ping')
            {
                $hostname = parse_url($service['url'], PHP_URL_HOST);
                $pingtime = static::ping($hostname);

                $services[$key]['status'] = ($pingtime >= 0) ? 'OK' : 'FAILED';
                $services[$key]['time']   = $pingtime;

            }else{

                $curlInfo = static::call($service['url'], array(), strtoupper($service['method']));
                $services[$key]['status'] = ($curlInfo['http_code'] >= 200 AND $curlInfo['http_code'] < 300 ) ? 'OK' : 'FAILED';
                $services[$key]['time']   = $curlInfo['total_time'];

            }


        }

        return View::make('check.resolve', compact('services'));
    }

    public static function ping($domain, $port = 80, $timeout = 10)
    {
        $starttime = microtime(true);
        $file      = @fsockopen ($domain, $port, $errno, $errstr, $timeout);
        $stoptime  = microtime(true);
        $status    = 0;

        if ( ! $file) $status = -1;  // Site is down
        else
        {
            fclose($file);
            $status = ($stoptime - $starttime) * 1000;
            $status = floor($status);
        }

        return $status;
    }

    public static function call($url, $data = array(), $method = 'POST')
    {
        $ch = curl_init($url);

        $data = http_build_query($data);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        curl_exec($ch);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($httpCode >= 200 && $httpCode < 300);
    }

}