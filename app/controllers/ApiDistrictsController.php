<?php

class ApiDistrictsController extends ApiBaseController {

    /**
     * @api {get} /districts Get Districts by city_id
     * @apiName Get Districts
     * @apiGroup Address
     * 
     * @apiParam {Number} city_id City Id.
     *
     * @apiSuccess (200) {Array} data List of districts.
     */
    public function getIndex()
    {
        $districts = array();
        
        $name = Input::get('lang', 'th')=='th'?'name':'name_1';

        foreach (District::whereCityId(Input::get('city_id'))->orderBy($name)->get() as $d)
        {
            $districts[] = array('id' => $d['id'], 'name' => $d[$name]);
        }

        return API::createResponse($districts, 200);
    }

}

