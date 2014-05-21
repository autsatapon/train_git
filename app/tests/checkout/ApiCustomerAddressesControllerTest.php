<?php
class ApiCustomerAddressesControllerTest extends \TestCase
{

    public $addres_id_add = null;

    public function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {

    }

    public function testGetAddressInvalidParameter()
    {
        $params = array
        (
            'customer_ref_id' => '',
        );

        $url = $this->makeRequestUrl('customerAddresses/address');
        $response = $this->call('GET', $url, $params);

        $this->assertStatusCode(400, 'error', $response, false, $url, $params);
    }

    public function testGetAddressValidParameter()
    {
        $params = array
        (
            'customer_ref_id' => '2638127',
        );

        $url = 'customerAddresses/address';
        $response = $this->call('GET', $this->makeRequestUrl($url), $params);
        //var_dump($response);die;
        $this->assertStatusCode(200, 'success', $response, false, $url, $params);
    }

    public function testpostCreateInvalidParameter()
    {
        // Input Null
        $params = array
        (
            'customer_ref_id' => '2638127'
        );

        $url = 'customerAddresses/create';
        $response = $this->call('POST', $this->makeRequestUrl($url), $params);
        $this->assertStatusCode(400, 'error', $response, false, $url, $params);
    }


    public function testpostCreateInvalidParameterNull()
    {
        // Input Null Error 500
        $params = array
        (
            'customer_ref_id' => '2638127',
            'name' => NULL,
            'address' => '',
            'district_id' => '',
            'city_id' => '',
            'province_id' => '',
            'province_id' => '',
            'postcode' => '',
            'phone' => '',
            'email' => NULL,
        );

        $url = 'customerAddresses/create';
        $response = $this->call('POST',$this->makeRequestUrl($url), $params);
        //var_dump($response);die;
        $this->assertStatusCode(400, 'error', $response, false, $url, $params);
    }

    public function testpostCreateValidParameter()
    {
        $params = array
        (
            'address_id' => 127,
            'customer_ref_id' => '2638127',
            'name' => 'ทดสอบที่อยู่',
            'address' => '1057 m.7',
            'district_id' => '1',
            'city_id' => '2',
            'province_id' => '1',
            'province_id' => '1',
            'postcode' => '10270',
            'phone' => '0814596611',
            'email' => 'john.kuae@gmail.com',
        );

        $url = 'customerAddresses/create';
        $response = $this->call('POST',$this->makeRequestUrl($url), $params);

        $this->assertStatusCode(200, 'success', $response, false, $url, $params);
    }

    public function testpostDeleteInvalidParameter()
    {
        $params = array
        (
            'customer_ref_id' => '2638127',
            'address_id' => ''
        );

        $url = 'customerAddresses/create';
        $response = $this->call('POST',$this->makeRequestUrl($url), $params);
        //var_dump($response);die;
        $this->assertStatusCode(400, 'error', $response, false, $url, $params);
    }

    public function testpostDeleteInvalidParameterAddressID()
    {
        $params = array
        (
            'customer_ref_id' => '2638127',
            'address_id' => '10010101011'
        );

        $url = 'customerAddresses/create';
        $response = $this->call('POST',$this->makeRequestUrl($url), $params);
        //var_dump($response);die;
        $this->assertStatusCode(400, 'error', $response, false, $url, $params);
    }

    public function testpostDeleteValidParameter()
    {
        $params = array
        (
            'customer_ref_id' => '2638127',
            'address_id' => 127
        );

        $url = 'customerAddresses/delete';
        $response = $this->call('POST',$this->makeRequestUrl($url), $params);

        $this->assertStatusCode(404, 'error', $response, false, $url, $params);
    }


}