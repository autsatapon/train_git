<?php


class ApiZipcodeController extends ApiBaseController 
{
    /**
     * @api {get} /zipcodes Get Districts by city_id
     * @apiName Get Zipcodes
     * @apiGroup Address
     * 
     * @apiParam {Number} city_id City Id.
     *
     * @apiSuccess (200) {Array} data List of zipcode.
     */
    public function getIndex()
    {
        $zipcode = array();
        
        $name = Input::get('lang', 'th')=='th'?'name':'name_1';

        foreach (District::whereId(Input::get('district_id'))->orderBy($name)->get() as $d)
        {
            $zipcode[] = array('id' => $d['id'], 'zipcode' => !empty($d['zip_code'])? $d['zip_code'] : '');
        }

        return API::createResponse($zipcode, 200);
    }

}
