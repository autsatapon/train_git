<?php

class ApiOtpsController extends ApiBaseController {

    protected $otp;

    public function __construct(OrderRepositoryInterface $order)
    {
        parent::__construct();

        $this->order = $order;
    }

    /**
     * Request OTP to register.
     *
     * @param  PApp   $app
     * @return string
     */
    public function getRequest(PApp $app)
    {
        $mssisdn = Input::get('mobile');

        if ( ! $mssisdn)
        {
            return API::createResponse('Error, mobile is required.', 400);
        }

        $config = Config::get('endpoints.truesms');

        $curlurl = $config['endpoint'].'/?method=request_otp';

        $params  = array(
            'app_id'  => $config['appId'],
            'secret'  => $config['secret'],
            'project' => 'pcms',
            'type'    => 'register',
            'msisdn'  => $mssisdn,
            'channel' => 'mobile'
        );

        $result = with(new Curl)->simple_post($curlurl, $params);
        $result = new SimpleXMLElement($result);

        $returnData = array(
            'code'         => $result->code,
            'description'  => $result->description,
            'operator'     => $result->operator,
            'msg'          => ($result->msg) ? $result->msg : '',
            'method'       => $result->method,
            'execute_time' => $result->execute_time
        );

        if ($returnData['code'] == 200)
        {
            return API::createResponse($returnData, 200);
        }

        return API::createResponse((string) $result->msg, 400);

    }

    public function getValidate(PApp $app)
    {
        $mssisdn = Input::get('mobile');
        $otp     = Input::get('otp');

        if ( ! $mssisdn or ! $otp)
        {
            return API::createResponse('Error, mobile, otp are required.', 400);
        }

        $config = Config::get('endpoints.truesms');

        $curlurl = $config['endpoint'].'/?method=validate_otp';

        $params  = array(
            'app_id'  => $config['appId'],
            'secret'  => $config['secret'],
            'project' => 'pcms' ,
            'type'    => 'register' ,
            'msisdn'  => $mssisdn ,
            'channel' => 'mobile' ,
            'otp'     => $otp
		);

        $result = with(new Curl)->simple_post($curlurl, $params);

        $result = new SimpleXMLElement($result);

        //sd($result);

        $returnData = array(
            'code'         => $result->code,
            'description'  => $result->description,
            'operator'     => $result->operator,
            'msg'          => ($result->msg) ? $result->msg : '',
            'method'       => $result->method,
            'execute_time' => $result->execute_time
        );

        if ($returnData['code'] == 200)
        {
            return API::createResponse($returnData, 200);
        }

        return API::createResponse((string) $result->msg, 400);
    }

}