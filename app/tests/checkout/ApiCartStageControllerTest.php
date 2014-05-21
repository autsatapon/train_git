<?php

class ApiCartStageControllerTest extends \TestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {

    }

    public function testpostSaveStageIncorrectParameter()
    {
        $params = array
        (
            'customer_ref_id' => '2638127',
            'customer_type'=>'user',
            'stage' => ''
        );

        $url = 'cart/save-stage';
        $response = $this->call('POST',$this->makeRequestUrl($url),$params);
        //var_dump($response);die;
        $this->assertStatusCode(400, 'error', $response, false, $url, $params);

    }

    public function testpostSaveStageCorrectParameter()
    {
        $stage = array(
            'current_stage' => 'checkout1',
            'history' => array(
                'stage1' => 'N',
                'stage2' => 'N',
                'stage3' => 'N',
            )
        );
        $params = array
        (
            'customer_ref_id' => '2638127',
            'customer_type'=>'user',
            'stage' => json_encode($stage, TRUE)
        );

        $url = 'cart/save-stage';
        $response = $this->call('POST',$this->makeRequestUrl($url),$params);
        //var_dump($response);die;
        $this->assertStatusCode(200, 'success', $response, false, $url, $params);
    }

    public function testgetStageCorrectParameter()
    {

        $params = array
        (
            'customer_ref_id' => '2638127',
            'customer_type' => 'user',
        );

        $url = 'cart/stage';
        $response = $this->call('GET',$this->makeRequestUrl($url),$params);
        //var_dump($response);die;
        $this->assertStatusCode(200, 'success', $response, false, $url, $params);
    }


}