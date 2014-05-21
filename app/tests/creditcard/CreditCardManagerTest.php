<?php
use \CreditCardManager;
class CreditCardManagerTest extends TestCase{
    private $manager;
    private $availableMemberId = 68;
    private function seedCreditCardTableZeroCard(){
        DB::table('credit_cards')->delete();
    }
    private function seedCreditCardTableAtMaxCardLimit(){
        // Assumes 5 cards = max limit
        DB::table('credit_cards')->delete();
        $dataRow = array(
                array(
                        'card_number' => 'xxxxxxxxxxxx1111',
                        'card_expiry_date' => '01-2014',
                        'card_type' => '001',
                        'payment_token' => '3644783643210170561946',
                        'member_id' => $this->availableMemberId
                ),
                array(
                        'card_number' => '123456xxxxxxxxxx',
                        'card_expiry_date' => '04-2020',
                        'card_type' => '002',
                        'payment_token' => '3427075830000181552556',
                        'member_id' => $this->availableMemberId
                ),
                array(
                        'card_number' => '411111xxxxxx1111',
                        'card_expiry_date' => '12-2019',
                        'card_type' => '001',
                        'payment_token' => '3483643210170255612345',
                        'member_id' => $this->availableMemberId
                ),
                array(
                        'card_number' => '322222xxxxxx2222',
                        'card_expiry_date' => '12-2019',
                        'card_type' => '002',
                        'payment_token' => '1234543210170255612345',
                        'member_id' => $this->availableMemberId
                ),
                array(
                        'card_number' => '423456xxxxxxxxxx',
                        'card_expiry_date' => '04-2015',
                        'card_type' => '001',
                        'payment_token' => '3483641234570255698765',
                        'member_id' => $this->availableMemberId
                )
        );
        foreach($dataRow as $row){
            $newId = CreditCard::create($row);
        }
        return $dataRow;
    }
    private function seedCreditCardTableForThreeCard(){
        DB::table('credit_cards')->delete();

        $dataRow = array(
                array(
                        'card_number' => 'xxxxxxxxxxxx1111',
                        'card_expiry_date' => '01-2014',
                        'card_type' => '001',
                        'payment_token' => '3644783643210170561946',
                        'member_id' => $this->availableMemberId
                ),
                array(
                        'card_number' => '123456xxxxxxxxxx',
                        'card_expiry_date' => '04-2020',
                        'card_type' => '002',
                        'payment_token' => '3427075830000181552556',
                        'member_id' => $this->availableMemberId
                ),
                array(
                        'card_number' => '411111xxxxxx1111',
                        'card_expiry_date' => '12-2019',
                        'card_type' => '001',
                        'payment_token' => '3483643210170255612345',
                        'member_id' => $this->availableMemberId
                )
        );
        foreach($dataRow as $row){
            $newId = CreditCard::create($row);
        }
        return $dataRow;
    }
    private function seedCreditCardTableForOnlyOneCard(){
        DB::table('credit_cards')->delete();
        $dataRow = array(
                'card_number' => 'xxxxxxxxxxxx1111',
                'card_expiry_date' => '01-2014',
                'card_type' => '001',
                'payment_token' => '3644783643210170561946',
                'member_id' => $this->availableMemberId
        );
        $newId = CreditCard::create($dataRow);
        return $dataRow;
    }
    public function testGetMaxCreditCardPerUser(){
        $manager = new CreditCardManager();
        $this->assertSame(5, $manager->getMaxCreditCardPerUser(), __FUNCTION__);

        $manager = new CreditCardManager(10);
        $this->assertSame(10, $manager->getMaxCreditCardPerUser(), __FUNCTION__);

        try{
            $manager = new CreditCardManager(-1);
        }catch(Exception $e){
            $this->assertSame('Invalid maximum credit card number.', $e->getMessage(), __FUNCTION__);
            return;
        }
    }
    public function testAddCreditCardWithIncorrectParameter(){
        $manager = new CreditCardManager();
        $tooLongCardNumber = array(
                'card_number' => 'xxxxxxxxxxxx1111xxx',
                'card_expiry_date' => '01-2014',
                'card_type' => '001',
                'payment_token' => '3644783643210170561946'
        );
        $tooShortCardNumber = array(
                'card_number' => 'xxxxx',
                'card_expiry_date' => '01-2014',
                'card_type' => '001',
                'payment_token' => '3644783643210170561946'
        );
        $emptyCardNumber = array(
                'card_number' => '',
                'card_expiry_date' => '01-2014',
                'card_type' => '001',
                'payment_token' => '3644783643210170561946'
        );
        $wrongDataTypeCardNumber = array(
                'card_number' => array(),
                'card_expiry_date' => '01-2014',
                'card_type' => '001',
                'payment_token' => '3644783643210170561946'
        );
        $correctCardData = array(
                'card_number' => 'xxxxxxxxxxxx1111',
                'card_expiry_date' => '01-2018',
                'card_type' => '002',
                'payment_token' => '3644783643210170000046'
        );
        $emptyExpiryCardData = array(
                'card_number' => 'xxxxxxxxxxxx1111',
                'card_expiry_date' => '',
                'card_type' => '002',
                'payment_token' => '3644783643210170000046'
        );
        $tooShortExpiryCardData = array(
                'card_number' => 'xxxxxxxxxxxx1111',
                'card_expiry_date' => '09-14',
                'card_type' => '002',
                'payment_token' => '3644783643210170000046'
        );
        $tooLongExpiryCardData = array(
                'card_number' => 'xxxxxxxxxxxx1111',
                'card_expiry_date' => '01-2014xx',
                'card_type' => '002',
                'payment_token' => '3644783643210170000046'
        );
        $wrongExpiryFormatCardData = array(
                'card_number' => 'xxxxxxxxxxxx1111',
                'card_expiry_date' => '2014_xx',
                'card_type' => '002',
                'payment_token' => '3644783643210170000046'
        );
        $wrongDataTypeExpiryCardData = array(
                'card_number' => 'xxxxxxxxxxxx1111',
                'card_expiry_date' => $manager,
                'card_type' => '002',
                'payment_token' => '3644783643210170000046'
        );
        $emptyCardTypeCardData = array(
                'card_number' => 'xxxxxxxxxxxx1111',
                'card_expiry_date' => '08-2222',
                'card_type' => '',
                'payment_token' => '3644783643210170000046'
        );
        $emptyCardTypeCardData = array(
                'card_number' => 'xxxxxxxxxxxx1111',
                'card_expiry_date' => '08-2222',
                'card_type' => '',
                'payment_token' => '3644783643210170000046'
        );
        $tooShortCardTypeCardData = array(
                'card_number' => 'xxxxxxxxxxxx1111',
                'card_expiry_date' => '08-2222',
                'card_type' => '2',
                'payment_token' => '3644783643210170000046',
                'member_id' => $this->availableMemberId
        );
        $tooLongCardTypeCardData = array(
                'card_number' => 'xxxxxxxxxxxx1111',
                'card_expiry_date' => '08-2222',
                'card_type' => '222222',
                'payment_token' => '3644783643210170000046'
        );
        $wrongFormatCardTypeCardData = array(
                'card_number' => 'xxxxxxxxxxxx1111',
                'card_expiry_date' => '08-2222',
                'card_type' => 'xxx',
                'payment_token' => '3644783643210170000046'
        );
        $wrongCardTypeCardData = array(
                'card_number' => 'xxxxxxxxxxxx1111',
                'card_expiry_date' => '08-2222',
                'card_type' => array(),
                'payment_token' => '3644783643210170000046'
        );

        $emptyTokenCardData = array(
                'card_number' => 'xxxxxxxxxxxx1111',
                'card_expiry_date' => '08-2222',
                'card_type' => '001',
                'payment_token' => ''
        );
        $tooShortTokenCardData = array(
                'card_number' => 'xxxxxxxxxxxx1111',
                'card_expiry_date' => '08-2222',
                'card_type' => '002',
                'payment_token' => '3644783643210'
        );
        $tooLongTokenCardData = array(
                'card_number' => 'xxxxxxxxxxxx1111',
                'card_expiry_date' => '08-2222',
                'card_type' => '003',
                'payment_token' => '36447836432101700000463644783643210170000046'
        );
        $wrongFormatTokenCardData = array(
                'card_number' => 'xxxxxxxxxxxx1111',
                'card_expiry_date' => '08-2222',
                'card_type' => '003',
                'payment_token' => 'xx345678901234567890aa'
        );
        $wrongDataTypeTokenCardData = array(
                'card_number' => 'xxxxxxxxxxxx1111',
                'card_expiry_date' => '08-2222',
                'card_type' => '003',
                'payment_token' => $manager
        );

        // TODO: overflow member_id case
        $actual = $manager->addCreditCard('', $correctCardData);
        $this->assertSame('Member id  not found.', $actual, __FUNCTION__);

        $actual = $manager->addCreditCard(-1, $correctCardData);
        $this->assertSame('Member id -1 not found.', $actual, __FUNCTION__);

        $actual = $manager->addCreditCard($manager, $correctCardData);
        $this->assertSame('Member id  not found.', $actual, __FUNCTION__);

        $actual = $manager->addCreditCard($this->availableMemberId, $emptyTokenCardData);
        $this->assertSame('The payment token field is required.', $actual, __FUNCTION__);

        $actual = $manager->addCreditCard($this->availableMemberId, $tooShortTokenCardData);
        $this->assertSame('The payment token must be at least 22 characters.', $actual, __FUNCTION__);

        $actual = $manager->addCreditCard($this->availableMemberId, $tooLongTokenCardData);
        $this->assertSame('The payment token may not be greater than 30 characters.', $actual, __FUNCTION__);

        $actual = $manager->addCreditCard($this->availableMemberId, $wrongFormatTokenCardData);
        $this->assertSame('The payment token format is invalid.', $actual, __FUNCTION__);

        $actual = $manager->addCreditCard($this->availableMemberId, $wrongDataTypeTokenCardData);
        $this->assertSame('preg_match() expects parameter 2 to be string, object given', $actual, __FUNCTION__);

        $actual = $manager->addCreditCard('', NULL);
        $this->assertSame('Member id  not found.', $actual, __FUNCTION__);

        $actual = $manager->addCreditCard(NULL, 1);
        $this->assertSame('Member id  not found.', $actual, __FUNCTION__);

        $actual = $manager->addCreditCard($manager, $correctCardData);
        $this->assertSame('Member id  not found.', $actual, __FUNCTION__);

        $actual = $manager->addCreditCard(array(
                '1',
                '2'
        ), $correctCardData);
        $this->assertSame('Member id 1,2 not found.', $actual, __FUNCTION__);

        $actual = $manager->addCreditCard(0, array());
        $this->assertSame('Member id 0 not found.', $actual, __FUNCTION__);

        $actual = $manager->addCreditCard(-1, 'xxx');
        $this->assertSame('Member id -1 not found.', $actual, __FUNCTION__);

        $actual = $manager->addCreditCard($this->availableMemberId, 'xxx');
        $this->assertSame('The card number field is required.', $actual, __FUNCTION__);

        $actual = $manager->addCreditCard($this->availableMemberId, $tooLongCardNumber);
        $this->assertSame('The card number must be 16 characters.', $actual, __FUNCTION__);

        $actual = $manager->addCreditCard($this->availableMemberId, $tooShortCardNumber);
        $this->assertSame('The card number must be 16 characters.', $actual, __FUNCTION__);

        $actual = $manager->addCreditCard($this->availableMemberId, $emptyCardNumber);
        $this->assertSame('The card number field is required.', $actual, __FUNCTION__);

        $actual = $manager->addCreditCard($this->availableMemberId, $wrongDataTypeCardNumber);
        $this->assertSame('preg_match() expects parameter 2 to be string, array given', $actual, __FUNCTION__);

        $actual = $manager->addCreditCard($this->availableMemberId, $emptyExpiryCardData);
        $this->assertSame('The card expiry date field is required.', $actual, __FUNCTION__);

        $actual = $manager->addCreditCard($this->availableMemberId, $wrongExpiryFormatCardData);
        $this->assertSame('The card expiry date format is invalid.', $actual, __FUNCTION__);

        $actual = $manager->addCreditCard($this->availableMemberId, $tooLongExpiryCardData);
        $this->assertSame('The card expiry date must be 7 characters.', $actual, __FUNCTION__);

        $actual = $manager->addCreditCard($this->availableMemberId, $tooShortExpiryCardData);
        $this->assertSame('The card expiry date must be 7 characters.', $actual, __FUNCTION__);

        $actual = $manager->addCreditCard($this->availableMemberId, $wrongDataTypeExpiryCardData);
        $this->assertSame('preg_match() expects parameter 2 to be string, object given', $actual, __FUNCTION__);

        $actual = $manager->addCreditCard($this->availableMemberId, $emptyCardTypeCardData);
        $this->assertSame('The card type field is required.', $actual, __FUNCTION__);

        $actual = $manager->addCreditCard($this->availableMemberId, $tooShortCardTypeCardData);
        $this->assertSame('The card type must be 3 characters.', $actual, __FUNCTION__);

        $actual = $manager->addCreditCard($this->availableMemberId, $tooLongCardTypeCardData);
        $this->assertSame('The card type must be 3 characters.', $actual, __FUNCTION__);

        $actual = $manager->addCreditCard($this->availableMemberId, $wrongFormatCardTypeCardData);
        $this->assertSame('The card type format is invalid.', $actual, __FUNCTION__);

        $actual = $manager->addCreditCard($this->availableMemberId, $wrongDataTypeCardNumber);
        $this->assertSame('preg_match() expects parameter 2 to be string, array given', $actual, __FUNCTION__);
    }
    public function testAddCreditCardExceed(){
        $this->seedCreditCardTableAtMaxCardLimit();
        $manager = new CreditCardManager();
        $correctCardData = array(
                'card_number' => 'xxxxxxxxxxxx9999',
                'card_expiry_date' => '01-2222',
                'card_type' => '002',
                'payment_token' => '3644783643210170099999'
        );
        $actual = $manager->addCreditCard($this->availableMemberId, $correctCardData);
        $newCard = CreditCard::whereMemberId($this->availableMemberId)->get(array(
                'id',
                'card_number',
                'card_expiry_date',
                'card_type',
                'payment_token',
                'member_id'
        ));
        $this->assertSame(5, count($newCard), __FUNCTION__);
        $this->assertSame('Maximum credit card limit reached. Cannot add the given credit card.', $actual, __FUNCTION__);
        $count = CreditCard::whereMemberId($this->availableMemberId)->whereCardNumber('xxxxxxxxxxxx9999')->get()->count();
        $this->assertSame(0, $count, __FUNCTION__);
        $count = CreditCard::whereMemberId($this->availableMemberId)->wherePaymentToken('3644783643210170099999')->get()->count();
        $this->assertSame(0, $count, __FUNCTION__);
    }
    public function testAddCreditCardNotExceed(){
        $this->seedCreditCardTableZeroCard();
        $manager = new CreditCardManager();
        $correctCardData = array(
                'card_number' => 'xxxxxxxxxxxx1111',
                'card_expiry_date' => '01-2018',
                'card_type' => '002',
                'payment_token' => '3644783643210170000046'
        );
        $actual = $manager->addCreditCard($this->availableMemberId, $correctCardData);
        $newCard = CreditCard::whereMemberId($this->availableMemberId)->get(array(
                'id',
                'card_number',
                'card_expiry_date',
                'card_type',
                'payment_token',
                'member_id'
        ));
        $this->assertSame(1, count($newCard), __FUNCTION__);
        $this->assertSame('The credit card is added successfully. Newly created id is ' . $newCard->first()->id . '.', $actual, __FUNCTION__);
        $this->assertSame($correctCardData['card_number'], $newCard->first()->card_number, __FUNCTION__);
        $this->assertSame($correctCardData['card_expiry_date'], $newCard->first()->card_expiry_date, __FUNCTION__);
        $this->assertSame($correctCardData['card_type'], $newCard->first()->card_type, __FUNCTION__);
        $this->assertSame($correctCardData['payment_token'], $newCard->first()->payment_token, __FUNCTION__);
        $this->assertSame($this->availableMemberId, $newCard->first()->member_id, __FUNCTION__);
    }
    public function testRemoveCreditCardWithIncorrectParameter(){
        $manager = new CreditCardManager();
        $actual = $manager->removeCreditCard($manager, '');
        $this->assertSame('Member id  not found.', $actual, __FUNCTION__);

        $actual = $manager->removeCreditCard(NULL, NULL);
        $this->assertSame('Member id  not found.', $actual, __FUNCTION__);

        $actual = $manager->removeCreditCard('', 1);
        $this->assertSame('Member id  not found.', $actual, __FUNCTION__);

        $actual = $manager->removeCreditCard('', -1);
        $this->assertSame('Member id  not found.', $actual, __FUNCTION__);

        $actual = $manager->removeCreditCard(0, 1);
        $this->assertSame('Member id 0 not found.', $actual, __FUNCTION__);

        $actual = $manager->removeCreditCard(array(), 1);
        $this->assertSame('Member id  not found.', $actual, __FUNCTION__);

        $actual = $manager->removeCreditCard('xxx', 1);
        $this->assertSame('Member id xxx not found.', $actual, __FUNCTION__);

        $actual = $manager->removeCreditCard(987654321, 1);
        $this->assertSame('Member id 987654321 not found.', $actual, __FUNCTION__);

        $actual = $manager->removeCreditCard($this->availableMemberId, NULL);
        $this->assertSame('Found member id ' . $this->availableMemberId . ' but no card id  attached.', $actual, __FUNCTION__);

        $this->seedCreditCardTableZeroCard();
        $actual = $manager->removeCreditCard($this->availableMemberId, 1);
        $this->assertSame('Found member id ' . $this->availableMemberId . ' but no card id 1 attached.', $actual, __FUNCTION__);

        $actual = $manager->removeCreditCard($this->availableMemberId, 'xx');
        $this->assertSame('Found member id ' . $this->availableMemberId . ' but no card id xx attached.', $actual, __FUNCTION__);

        $actual = $manager->removeCreditCard($this->availableMemberId, 0);
        $this->assertSame('Found member id ' . $this->availableMemberId . ' but no card id 0 attached.', $actual, __FUNCTION__);

        $actual = $manager->removeCreditCard($this->availableMemberId, -5);
        $this->assertSame('Found member id ' . $this->availableMemberId . ' but no card id -5 attached.', $actual, __FUNCTION__);

        $actual = $manager->removeCreditCard($this->availableMemberId, array());
        $this->assertSame('Found member id ' . $this->availableMemberId . ' but no card id  attached.', $actual, __FUNCTION__);
    }
    public function testRemoveCreditCardWithNoCreditCardAttached(){
        $this->seedCreditCardTableZeroCard();
        $manager = new CreditCardManager();
        $actual = $manager->removeCreditCard(65, 2);
        $this->assertSame('Found member id 65 but no card id 2 attached.', $actual, __FUNCTION__);

        $this->seedCreditCardTableForThreeCard();
        $manager = new CreditCardManager();
        $actual = $manager->removeCreditCard(66, 2);
        $this->assertSame('Found member id 66 but no card id 2 attached.', $actual, __FUNCTION__);
    }
    public function testRemoveCreditCard(){
        $this->seedCreditCardTableForThreeCard();
        $manager = new CreditCardManager();
        $cards = CreditCard::whereMemberId($this->availableMemberId)->get(array(
                'id'
        ));
        foreach($cards as $card){
            $actual = $manager->removeCreditCard($this->availableMemberId, $card->id);
            $this->assertSame('Card id ' . $card->id . ' of the member id ' . $this->availableMemberId . ' is removed successfully.', $actual, __FUNCTION__);
        }
        $cards = CreditCard::whereMemberId($this->availableMemberId)->get(array(
                'id'
        ));
        $this->assertSame(0, count($cards), __FUNCTION__);

        $this->seedCreditCardTableForThreeCard();
        $cards = CreditCard::whereMemberId($this->availableMemberId)->get(array(
                'id'
        ));
        foreach($cards as $card){
            $actual = $manager->removeCreditCard(67, $card->id);
            $this->assertSame('Found member id 67 but no card id ' . $card->id . ' attached.', $actual, __FUNCTION__);
        }
    }
    public function testGetCreditCardListFromUserWithIncorrectParameter(){
        $manager = new CreditCardManager();
        $actual = $manager->getCreditCardListFromUser(0);
        $this->assertSame(array(), $actual, __FUNCTION__);

        $actual = $manager->getCreditCardListFromUser(-1);
        $this->assertSame(array(), $actual, __FUNCTION__);

        $actual = $manager->getCreditCardListFromUser(NULL);
        $this->assertSame(array(), $actual, __FUNCTION__);

        $actual = $manager->getCreditCardListFromUser(array());
        $this->assertSame(array(), $actual, __FUNCTION__);

        $actual = $manager->getCreditCardListFromUser(0);
        $this->assertSame(array(), $actual, __FUNCTION__);

        $actual = $manager->getCreditCardListFromUser(-1);
        $this->assertSame(array(), $actual, __FUNCTION__);

        $actual = $manager->getCreditCardListFromUser(NULL);
        $this->assertSame(array(), $actual, __FUNCTION__);

        $actual = $manager->getCreditCardListFromUser(array());
        $this->assertSame(array(), $actual, __FUNCTION__);
    }
    public function testGetCreditCardListFromUserWithCorrectParameter(){
        $MAX_CC_PER_USER = 5;
        $manager = new CreditCardManager($MAX_CC_PER_USER);

        $this->seedCreditCardTableZeroCard();
        $actual = $manager->getCreditCardListFromUser($this->availableMemberId);
        $this->assertSame(count($actual), 0, __FUNCTION__);
        $this->assertSame(array(), $actual, __FUNCTION__);

        $rowUpdate = $this->seedCreditCardTableForOnlyOneCard();
        $actual = $manager->getCreditCardListFromUser($this->availableMemberId);
        $this->assertSame(count($actual), 1, __FUNCTION__);
        $this->assertSame($rowUpdate['card_number'], $actual[0]['card_number'], __FUNCTION__);
        $this->assertSame($rowUpdate['card_expiry_date'], $actual[0]['card_expiry_date'], __FUNCTION__);
        $this->assertSame($rowUpdate['card_type'], $actual[0]['card_type'], __FUNCTION__);
        $this->assertSame($rowUpdate['payment_token'], $actual[0]['payment_token'], __FUNCTION__);
        $this->assertSame($rowUpdate['member_id'], $actual[0]['member_id'], __FUNCTION__);

        $rowUpdate = $this->seedCreditCardTableForThreeCard();
        $actual = $manager->getCreditCardListFromUser($this->availableMemberId);
        $this->assertSame(count($actual), 3, __FUNCTION__);
        for($ii = 0; $ii < count($rowUpdate); $ii++){
            $this->assertSame($rowUpdate[$ii]['card_number'], $actual[$ii]['card_number'], __FUNCTION__);
            $this->assertSame($rowUpdate[$ii]['card_expiry_date'], $actual[$ii]['card_expiry_date'], __FUNCTION__);
            $this->assertSame($rowUpdate[$ii]['card_type'], $actual[$ii]['card_type'], __FUNCTION__);
            $this->assertSame($rowUpdate[$ii]['payment_token'], $actual[$ii]['payment_token'], __FUNCTION__);
            $this->assertSame($rowUpdate[$ii]['member_id'], $actual[$ii]['member_id'], __FUNCTION__);
        }
    }
}