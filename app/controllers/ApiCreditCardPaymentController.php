<?php
class ApiCreditCardPaymentController extends ApiBaseController{
    Const HTTP_SUCCESS_CODE = 200;
    public function getSanityCheck(PApp $app){
        $response = API::createResponse($app->toArray(), $this::HTTP_SUCCESS_CODE);
        return $response;
    }
}