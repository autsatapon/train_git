<?php namespace TrueCard;

class TrueCard {

    /**
     * Agent URL.
     *
     * @var string
     */
    protected $agentUrl = 'http://tma.truelife.com/tma/truecrm_agent/agent.aspx';

    /**
     * Endpoint of agent api.
     *
     * @var string
     */
    protected $endpoint = 'http://truecardbn.truelife.com/truecardsrv/services/api.aspx';

    /**
     * CURL default options.
     *
     * @var array
     */
    public static $CURL_OPTS = array(
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 60,
        CURLOPT_USERAGENT      => 'agent-0.1',
    );

    /**
     * SDK construct.
     *
     * @param array $params
     */
    public function __construct($params = array())
    {
        $this->initialize($params);
    }

    /**
     * Intialize config.
     *
     * @param  array $params
     * @return void
     */
    public function initialize($params)
    {
        if (count($params)) foreach ($params as $key => $val)
        {
            $method = 'set'.ucfirst($key);

            $this->$method($val);
        }
    }

    public function setAgentUrl($url)
    {
        $this->agentUrl = $url;
    }

    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
    }

    public function api($method, $params = array())
    {
        $params = http_build_query($params);

        $url = $this->agentUrl.'?apiUrl='.$this->endpoint.'&method='.$method.'&'.$params;

        return $this->makeRequest($url, array(), 'GET');
    }

    public function getInfoByThaiId($thaiId)
    {
        return $this->api('get_card_information', array(
            'thaiid' => $thaiId
        ));
    }

    /**
     * Makes an HTTP request. This method can be overridden by subclasses if
     * developers want to do fancier things or use something other than curl to
     * make the request.
     *
     * @param  string $url
     * @param  array  $args
     * @param  CURL   $ch
     * @return string
     */
    protected function makeRequest($url, array $args, $method = 'POST')
    {
        // Because office environemnt cannot connect to vpn.
        if (\App::environment() == 'office' || \App::environment() == 'local')
        {
            $result = '
                <response>
                    <code>200</code>
                    <description>success</description>
                    <transaction_id>20140114154341926950990665</transaction_id>
                    <service_desc>Truecard::get_card_information</service_desc>
                    <method>get_card_information</method>
                    <response_method>UNKNOW</response_method>
                    <ext_transaction_id></ext_transaction_id>
                    <moreinfo></moreinfo>
                    <text></text>
                    <paging></paging>
                    <body>
                        <items>
                            <card_information>
                                <item>
                                    <recid>1964214</recid>
                                    <thaiid>5100799036048</thaiid>
                                    <cardid>8881188881068878</cardid>
                                    <card10>8881068878</card10>
                                    <mifare><![CDATA[56E67006]]></mifare>
                                    <sentdate><![CDATA[2012-11-16]]></sentdate>
                                    <grade><![CDATA[G]]></grade>
                                    <expired>1512</expired>
                                    <cardname><![CDATA[คุณพัฒธานัย กวินวศิน]]></cardname>
                                    <empid></empid>
                                    <cardaddress><![CDATA[53 รองเมือง  รองเมือง ปทุมวัน กรุงเทพมหานคร 10330]]></cardaddress>
                                    <invitedate><![CDATA[2012-11-16]]></invitedate>
                                    <startdate><![CDATA[2012-11-16]]></startdate>
                                    <enddate><![CDATA[2014-01-31]]></enddate>
                                    <status><![CDATA[A]]></status>
                                    <activated><![CDATA[N]]></activated>
                                    <language><![CDATA[TH]]></language>
                                    <customertype><![CDATA[NOR]]></customertype>
                                    <returncard></returncard>
                                    <extra1></extra1>
                                    <extra2><![CDATA[G]]></extra2>
                                    <stampdate><![CDATA[2012-11-16 20:00:59]]></stampdate>
                                    <gradingdate><![CDATA[2012-11-16]]></gradingdate>
                                    <expired_date></expired_date>
                                </item>
                            </card_information>
                        </items>
                    </body>
                </response>
            ';
        }
        else
        {

            $ch = curl_init();

            $opts = self::$CURL_OPTS;

            $opts[CURLOPT_URL] = $url;

            // Using POST request.
            if (strcasecmp($method, 'POST') == 0)
            {
                $opts[CURLOPT_POST] = true;
                $opts[CURLOPT_POSTFIELDS] = http_build_query($args, null, '&');
            }

            // disable the 'Expect: 100-continue' behaviour. This causes CURL to wait
            // for 2 seconds if the server does not support this header.
            // if (isset($opts[CURLOPT_HTTPHEADER]))
            // {
            //     $existing_headers = $opts[CURLOPT_HTTPHEADER];
            //     $existing_headers[] = 'Expect:';
            //     $opts[CURLOPT_HTTPHEADER] = $existing_headers;
            // }
            // else
            // {
            //     $opts[CURLOPT_HTTPHEADER] = array('Expect:');
            // }

            curl_setopt_array($ch, $opts);

            $result = curl_exec($ch);
        }

        return new Process($result);
    }

}