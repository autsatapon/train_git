<?php
class ApiCreditCardController extends ApiBaseController{
    Const HTTP_SUCCESS_CODE = 200;
    Const HTTP_NOT_FOUND_CODE = 404;
    Const HTTP_INTERNAL_SEVER_ERROR_CODE = 500;
    public function getSanityCheck(PApp $app){
        $response = API::createResponse($app->toArray(), $this::HTTP_SUCCESS_CODE);
        return $response;
    }
    private function printMemberSSOIdNotFound($memberId){
        return 'Member sso id ' . $memberId . ' not found.';
    }
    private function extractMemberIdFromSSOId($memberSSOId, $appId){
        $output = array();
        if(!empty($memberSSOId)){
            $members = Member::whereSsoId($memberSSOId)->whereAppId($appId)->get(array(
                    'id'
            ));
            foreach($members as $member){
                $output[] = $member->id;
            }
        }
        return $output;
    }
    public function anyCardList(PApp $app){
        $data = array();
        $response = '';
        $manager = new CreditCardManager($app->max_cc_per_user);
        $memberSSOId = Input::get('ssoId');
        $members = $this->extractMemberIdFromSSOId($memberSSOId, $app->id);
        if(count($members) === 1){
            $memberId = $members[0];
            if(is_numeric($memberId)){
                $member = Member::find($memberId);
                if(empty($member)){
                    $data = $this->printMemberSSOIdNotFound($memberId);
                    $response = API::createResponse($data, $this::HTTP_NOT_FOUND_CODE);
                }else{
                    $data = $manager->getCreditCardListFromUser($memberId);
                    $response = API::createResponse($data, $this::HTTP_SUCCESS_CODE);
                }
            }else{
                $data = $this->printMemberSSOIdNotFound($memberId);
                $response = API::createResponse($data, $this::HTTP_NOT_FOUND_CODE);
            }
        }elseif(count($members) > 1){
            $data = 'Duplicated records found with member sso id ' . $memberSSOId . ', app id ' . $app->id;
            $response = API::createResponse($data, $this::HTTP_NOT_FOUND_CODE);
        }else{
            // count = 0, not found
            $data = $this->printMemberSSOIdNotFound($memberSSOId);
            $response = API::createResponse($data, $this::HTTP_NOT_FOUND_CODE);
        }
        return $response;
    }
    public function anyRemoveCreditCard(PApp $app){
        $data = array();
        $response = '';
        $manager = new CreditCardManager();
        $memberSSOId = Input::get('ssoId');
        $members = $this->extractMemberIdFromSSOId($memberSSOId, $app->id);
        $cardId = Input::get('card_id');
        if(count($members) === 1){
            $memberId = $members[0];
            $data = $manager->removeCreditCard($memberId, $cardId);
            if(is_string($data)){
                if(strpos($data, 'success') !== FALSE){
                    $response = API::createResponse($data, $this::HTTP_SUCCESS_CODE);
                }else{
                    $response = API::createResponse($data, $this::HTTP_NOT_FOUND_CODE);
                }
            }else{
                $response = API::createResponse('Unexpected error', $this::HTTP_SUCCESS_CODE);
            }
        }elseif(count($members) > 1){
            $data = 'Duplicated records found with member sso id ' . $memberSSOId . ', app id ' . $app->id;
            $response = API::createResponse($data, $this::HTTP_NOT_FOUND_CODE);
        }else{
            $data = $this->printMemberSSOIdNotFound($memberSSOId);
            $response = API::createResponse($data, $this::HTTP_NOT_FOUND_CODE);
        }
        return $response;
    }
}