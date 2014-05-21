<?php

class ApiCartControllerTest extends \TestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {

    }

    /**
     * ใส่ Email ผิด
     */
    public function testUpdateCartWithIncorrectParameter()
    {
        $params = array
        (
            'customer_email' => '',
            'customer_ref_id' => '2638127',
            'customer_type' => 'user'
        );

        $url = 'cart/apply-email';
        $response = $this->call('POST',$this->makeRequestUrl($url), $params);
        $this->assertStatusCode(400, 'error', $response, false, $url, $params);
    }

    /**
     * ใส่ข้อมูลครบ id member but guest
     */
    public function testUpdateCartWithCorrectParameterMemberIdGuest()
    {
        $params = array
        (
            'customer_email' => 'john.kuae@gmail.com',
            'customer_ref_id' => '2638127',
            'customer_type' => 'non-user'
        );

        $url = 'cart/apply-email';
        $response = $this->call('POST',$this->makeRequestUrl($url), $params);

        $this->assertStatusCode(200, 'success', $response, false, $url, $params);
    }

    /**
     * ใส่ข้อมูลครบ member
     */
    public function testUpdateCartWithCorrectParameterMember()
    {
        $params = array
        (
            'customer_email' => 'john.kuae@gmail.com',
            'customer_ref_id' => '2638127',
            'customer_type' => 'user'
        );

        $url = 'cart/apply-email';
        $response = $this->call('POST',$this->makeRequestUrl($url), $params);

        $this->assertStatusCode(200, 'success', $response, false, $url, $params);
    }

    /**
     * ใส่ข้อมูลครบ non-member
     */
    public function testUpdateCartWithCorrectParameterNonMember()
    {
        $params = array
        (
            'customer_email' => 'john.kuae@gmail.com',
            'customer_ref_id' => '123213123123213',
            'customer_type' => 'non-user'
        );

        $url = 'cart/apply-email';
        $response = $this->call('POST',$this->makeRequestUrl($url), $params);

        $this->assertStatusCode(200, 'success', $response, false, $url, $params);
    }

    /**
     * ใส่ข้อมูลครบ non-member
     */
    public function testRemoveWithInvalidParameter()
    {
        $params = array
        (
            'customer_email' => 'john.kuae@gmail.com',
            'customer_ref_id' => '123213123123213',
            'customer_type' => 'non-user'
        );

        $url = 'cart/remove-email';
        $response = $this->call('POST',$this->makeRequestUrl($url), $params);

        $this->assertStatusCode(200, 'success', $response, false, $url, $params);
    }

}