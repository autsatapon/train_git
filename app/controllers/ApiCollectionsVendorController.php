<?php
class ApiCollectionsVendorController extends ApiCollectionsController {

    protected $page, $skip, $take;

    public function getListProducts($app, $collectionKey)
    {
        $response = parent::getListProducts($app, $collectionKey);        

        $newResponse = array(); 

        if ( ! empty($response['data']['products']))
        {
            $products = array();
            foreach ($response['data']['products'] as $key => $value)
            {
                $value['url'] = URLManager::iTruemartlevelDUrl($value['slug'], $value['pkey']); 
                $products[] = $value; 
            }
            $response['data']['products'] = $products; 
        }

        return $response; 
    }

    
}