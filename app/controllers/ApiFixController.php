<?php

set_time_limit(0);

class ApiFixController extends ApiBaseController {

    /**
     * @api {post} /fix/rebuild Update products on elasticsearch
     * @apiName Update elasticsearch
     * @apiGroup Fix
     *
     * @apiParam {Number} Product id.
     *
     * @apiSuccess {Array} data Member profile.
     */
    public function getRebuild($app, $id = null)
    {
        if (is_null($id))
        {
            $products = Product::whereStatus('publish')->get();
        }
        else
        {
            $products = Product::whereId($id)->get();
        }

        foreach ($products as $product)
        {
            \ElasticUtils::updateProduct($product);
        }


        return API::createResponse('Done', 200);
    }

}
