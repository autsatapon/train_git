<?php
class ApiCreditCardControllerTest extends TestCase{
    Const CARD_LIST_URI = 'api/45311375168544/credit-card/card-list';
    Const REMOVE_CARD_URI = 'api/45311375168544/credit-card/remove-credit-card';
    private $availableMemberSSOId = 18600366;
    private $availableMemberId = 68;
    public function testGetSanityCheck(){
        $response = $this->call('GET', 'api/45311375168544/credit-card/sanity-check');
        $data = $response->getOriginalContent();
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertSame(4, count($data));
        $this->assertSame('success', $data['status']);
        $this->assertSame(200, $data['code']);
        $this->assertSame('200 OK', $data['message']);
        $this->assertArrayHasKey('max_cc_per_user', $data['data']);
    }
    public function testGetCardListWithNoParameter(){
        $param = array(
                '' => ''
        );
        $response = $this->call('GET', $this::CARD_LIST_URI, $param);
        $data = $response->getOriginalContent();
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertSame('Member sso id  not found.', $data['message']);
    }
    public function testGetCardListWithNotExistsMemberId(){
        $param = array(
                'ssoId' => 0
        );
        $response = $this->call('GET', $this::CARD_LIST_URI, $param);
        $data = $response->getOriginalContent();
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertSame('Member sso id 0 not found.', $data['message']);

        $this->refreshApplication();
        $param = array(
                'ssoId' => 999999
        );
        $response = $this->call('GET', $this::CARD_LIST_URI, $param);
        $data = $response->getOriginalContent();
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertSame('Member sso id 999999 not found.', $data['message']);

        $this->refreshApplication();
        $param = array(
                'ssoId' => 'xxx'
        );
        $response = $this->call('POST', $this::CARD_LIST_URI, $param);
        $data = $response->getOriginalContent();
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertSame('Member sso id xxx not found.', $data['message']);
    }
    public function testGetCardListWithZeroCardAttached(){
        // member id 8 has sso id 1798931
        $param = array(
                'ssoId' => 1798931
        );
        $response = $this->call('GET', $this::CARD_LIST_URI, $param);
        $data = $response->getOriginalContent();
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertSame('200 OK', $data['message']);
    }
    public function testGetCardListWithCardAttached(){
        $this->seedCreditCardTableOneCard();
        $param = array(
                'ssoId' => $this->availableMemberSSOId
        );
        $response = $this->call('GET', $this::CARD_LIST_URI, $param);
        $data = $response->getOriginalContent();
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertSame('200 OK', $data['message']);
        $this->assertGreaterThan(0, count($data['data']));
    }
    public function testRemoveCardNotSuccess(){
        $param = array(
                'ssoId' => $this->availableMemberSSOId,
                'card_id' => 0
        );
        $response = $this->call('GET', $this::REMOVE_CARD_URI, $param);
        $data = $response->getOriginalContent();
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertSame(404, $data['code']);
        $this->assertSame('Found member id 68 but no card id 0 attached.', $data['message']);

        $this->refreshApplication();
        $param = array(
                'ssoId' => 0,
                'card_id' => 0
        );
        $response = $this->call('GET', $this::REMOVE_CARD_URI, $param);
        $data = $response->getOriginalContent();
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertSame(404, $data['code']);
        $this->assertSame('Member sso id 0 not found.', $data['message']);
    }
    public function testRemoveCardSuccess(){
        $this->seedCreditCardTableOneCard();
        $card = CreditCard::whereMemberId($this->availableMemberId)->get()->first();
        if(!empty($card)){
            $cardId = $card->id;
            $param = array(
                    'ssoId' => $this->availableMemberSSOId,
                    'card_id' => $cardId
            );
            $response = $this->call('GET', $this::REMOVE_CARD_URI, $param);
            $data = $response->getOriginalContent();
            $this->assertTrue($this->client->getResponse()->isOk());
            $this->assertSame(200, $data['code']);
            $this->assertSame('Card id ' . $cardId . ' of the member id ' . $this->availableMemberId . ' is removed successfully.', $data['message']);
        }
    }
    private function seedCreditCardTableOneCard(){
        DB::table('credit_cards')->delete();
        $dataRow = array(
                'card_number' => '323456xxxxxx1111',
                'card_expiry_date' => '11-2019',
                'card_type' => '002',
                'payment_token' => '1234583643210170561946',
                'member_id' => $this->availableMemberId
        );
        $newId = CreditCard::create($dataRow);
        return $dataRow;
    }
}