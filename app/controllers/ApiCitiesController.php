<?php

class ApiCitiesController extends ApiBaseController {
    
    /**
     * @api {get} /cities Get Cities by province_id
     * @apiName Get Cities
     * @apiGroup Address
     * 
     * @apiParam {Number} province_id Province Id.
     *
     * @apiSuccess (200) {Array} data List of cities.
     */
    public function getIndex()
    {
        $cities = array();
        
        $name = Input::get('lang', 'th')=='th'?'name':'name_1';

        foreach (City::whereProvinceId(Input::get('province_id'))->orderBy($name)->get() as $c)
        {
            $cities[] = array('id' => $c['id'], 'name' => $c[$name]);
        }

        return API::createResponse($cities, 200);
    }

}

