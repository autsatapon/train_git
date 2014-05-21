<?php
use \Member;
use \CreditCard;
class CreditCardManager{
    private $maxCreditCardPerUser;
    public function __construct($maxCreditCardPerUser = 5){
        if(is_numeric($maxCreditCardPerUser) && $maxCreditCardPerUser > 0){
            $this->maxCreditCardPerUser = $maxCreditCardPerUser;
        }else{
            $message = 'Invalid maximum credit card number.';
            throw new Exception($message);
        }
    }
    private function printMemberFoundButNoCard($memberId, $cardId){
        return 'Found member id ' . $memberId . ' but no card id ' . $cardId . ' attached.';
    }
    private function printMemberNotFound($memberId){
        return 'Member id ' . $memberId . ' not found.';
    }
    public function addCreditCard($memberId, $cardData){
        if(is_numeric($memberId)){
            $member = Member::find($memberId);
            if(empty($member)){
                $data = $this->printMemberNotFound($memberId);
            }else{
                // Validate Card Data
                try{
                    if(is_array($cardData)){
                        $cardData['member_id'] = $memberId;
                    }else{
                        $cardData = array();
                    }
                    $validator = Validator::make($cardData, CreditCard::$rules);
                    if($validator->fails()){
                        $data = $validator->errors()->first(NULL, ':message');
                    }else{
                        // Data is valid, check max limit before adding
                        $count = CreditCard::whereMemberId($memberId)->get(array(
                                'id'
                        ))->count();
                        if($count < $this->maxCreditCardPerUser){
                            $newCard = CreditCard::create($cardData);
                            $data = 'The credit card is added successfully. Newly created id is ' . $newCard->id . '.';
                        }else{
                            $data = 'Maximum credit card limit reached. Cannot add the given credit card.';
                        }
                    }
                }catch(Exception $e){
                    $data = $e->getMessage();
                }
            }
        }else{
            if(is_array($memberId)){
                $memberId = implode(',', $memberId);
            }
            if(!is_string($memberId)){
                $memberId = '';
            }
            $data = $this->printMemberNotFound($memberId);
        }
        return $data;
    }
    public function removeCreditCard($memberId, $cardId){
        if(is_numeric($memberId)){
            $member = Member::find($memberId);
            if(empty($member)){
                $data = $this->printMemberNotFound($memberId);
            }else{
                // Member available
                if(is_numeric($cardId)){
                    $card = CreditCard::find($cardId);
                    if(empty($card)){
                        $data = $this->printMemberFoundButNoCard($memberId, $cardId);
                    }else{
                        if($card->member_id == $memberId){
                            $card->delete();
                            $data = 'Card id ' . $cardId . ' of the member id ' . $memberId . ' is removed successfully.';
                        }else{
                            $data = $this->printMemberFoundButNoCard($memberId, $cardId);
                        }
                    }
                }else{
                    if(is_array($cardId)){
                        $cardId = implode(',', $cardId);
                    }
                    $data = $this->printMemberFoundButNoCard($memberId, $cardId);
                }
            }
        }else{
            if(is_array($memberId)){
                $memberId = implode(',', $memberId);
            }
            if(!is_string($memberId)){
                $memberId = '';
            }
            $data = $this->printMemberNotFound($memberId);
        }
        return $data;
    }
    public function getMaxCreditCardPerUser(){
        return $this->maxCreditCardPerUser;
    }
    public function getCreditCardListFromUser($id){
        $output = array();
        if(is_numeric($id)){
            $cards = CreditCard::whereMemberId($id)->get(array(
                    'card_number',
                    'card_expiry_date',
                    'card_type',
                    'payment_token',
                    'member_id'
            ));
            foreach($cards as $card){
                $output[] = $card->toArray();
            }
        }
        return $output;
    }
}
