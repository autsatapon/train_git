<?php
class ApiCreditCardPaymentControllerTest extends TestCase{
    Const CARD_LIST_URI = 'api/45311375168544/credit-card/card-list';
    public function testGetSanityCheck(){
        $response = $this->call('GET', 'api/45311375168544/credit-card-payment/sanity-check');
        $data = $response->getOriginalContent();
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertSame(4, count($data));
        $this->assertSame('success', $data['status']);
        $this->assertSame(200, $data['code']);
        $this->assertSame('200 OK', $data['message']);
        $this->assertArrayHasKey('max_cc_per_user', $data['data']);
    }
}