<?php

class ApiProvincesController extends ApiBaseController {

    /**
     * @api {get} /provinces Get Provinces
     * @apiName Get Provinces
     * @apiGroup Address
     *
     * @apiSuccess (200) {Array} data List of provinces.
     */
    public function getIndex()
    {
//        if (Input::has('id'))
//        {
//            return $this->getById(Input::get('id'));
//        }

        $provinces = array();
        
        $name = Input::get('lang')=='th'?'name':'name_1';

        foreach (Province::orderBy($name)->get() as $p)
        {
            $provinces[] = array('id' => $p['id'], 'name' => $p[$name]);
        }

        return API::createResponse($provinces, 200);
    }

//    private function getById($id)
//    {
//        $provinces = Province::with('cities.district')->get();
//        
//        $provinces->each(function($province)
//        {
//            $province->setHidden(array('country_id', 'name_1', 'delivery_area_id'));
//            
//            $province->cities->each(function($city)
//            {
//                $city->setHidden(array('province_id', 'name_1', ''));
//            });
//        });
//
//        return API::createResponse($provinces->toArray());
//    }
}

