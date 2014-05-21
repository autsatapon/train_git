<?php

class ApiCollectionsController extends ApiBaseController {

    protected $page, $skip, $take, $depth, $isCategory;

    public function __construct()
    {
        $this->page = (int) Input::get('page');
        if ($this->page < 1)
        {
            $this->page = 1;
        }

        $this->take = (int) Input::get('limit');
        if ($this->take < 1)
        {
            $this->take = 20;
        }

        $this->skip = $this->take * ($this->page - 1);

        // Set $this->depth
        $this->depth = Input::get('depth', 10);

        $this->isCategory = Input::get('is_category', 0);
    }

    /**
     * @api {get} /collections List collections
     * @apiName Get Collections
     * @apiGroup Collection
     *
     * @apiParam {Number} [depth=10] Level deep limit.
     * @apiParam {Number} [is_category=0] If 1 will filter only category.
     * @apiParam {Number} [page=1] Page number.
     * @apiParam {Number} [limit=20] Total root collections per page.
     *
     * @apiSuccess {Array} data List of collections and sub-collections.
     */
    public function getIndex($app)
    {
        // get brand data from cache
        $collectionsArr = Cache::getCurrentApiData();
        if ( !empty($collectionsArr) )
        {
            return API::createResponse($collectionsArr);
        }

        $arrWith = $this->getArrWith($this->depth);

        // get only Category Type
        if ($this->isCategory == 1)
        {
            $collections = $app->collections()->with($arrWith)->where('parent_id', 0)->where('is_category', 1)->skip($this->skip)->take($this->take)->get();
        }
        else
        {
            $collections = $app->collections()->with($arrWith)->where('parent_id', 0)->skip($this->skip)->take($this->take)->get();
        }

        $this->reformatting($collections);

        $collectionsArr = $collections->toArray();

        // Set Cache data
        $timeout = ( Config::has('cache.timeout.long') ) ? 60 : Config::get('cache.timeout.long') ;
        Cache::setCurrentApiData($collectionsArr, $timeout);

        return API::createResponse($collectionsArr);
    }

    /**
     * @api {get} /collections/:pkey Get collection by pkey
     * @apiName Get Collection
     * @apiGroup Collection
     *
     * @apiSuccess {Array} data Detail of collection.
     */
    public function getByPkey($app, $collectionKey)
    {
        $cacheKey = $app->pkey . '_' . $collectionKey;
        // Get collection data from cache.
        if ( Cache::tags('collection')->has($cacheKey) )
        {
            $collectionArr = Cache::tags('collection')->get($cacheKey);
            return API::createResponse($collectionArr);
        }

        $arrWith = $this->getArrWith($this->depth);

        $collections = Collection::with($arrWith)->where('pkey', $collectionKey)->get();

        // If Empty $collection, Return 404 Response.
        if ($collections->isEmpty())
        {
            return API::createResponse(FALSE, 404);
        }

        $this->reformatting($collections);

        $collectionArr = $collections->first()->toArray();

        // Set Cache Data
        $timeout = ( Config::has('cache.timeout.long') ) ? 60 : Config::get('cache.timeout.long') ;
        Cache::tags('collection')->put($cacheKey, $collectionArr, $timeout);

        return API::createResponse($collectionArr);
    }

    /**
     * @api {get} /collections/:pkey/brands List brands of collection
     * @apiName Get Brands in collection
     * @apiGroup Collection
     *
     * @apiSuccess {Array} data List of brands in that collection.
     */
    public function getListBrands($app, $collectionKey)
    {
        // get brand data from cache
        $brandsArr = Cache::getCurrentApiData();
        if ( !empty($brandsArr) )
        {
            return API::createResponse($brandsArr);
        }

        $collection = Collection::where('pkey', $collectionKey)->first();

        // If Empty $collection, Return 404 Response.
        if (empty($collection))
        {
            return API::createResponse(FALSE, 404);
        }

        $brandIdArr = $collection->products()->where('status', 'publish')->lists('brand_id');

        $brands = Brand::whereIn('id', $brandIdArr)->orderBy('name', 'asc')->get();

        // $visibleBrandFields = array('pkey', 'name');
        $visibleBrandFields = array('pkey', 'name', 'metas', 'thumbnail', 'translate', 'slug');
        foreach ($brands as $brand)
        {
            $brand->setVisible($visibleBrandFields);
            $brand->setAppends(array('metas', 'thumbnail'));

            $translate = $brand->translate();
            if (!empty($translate))
            {
                $brand->translate = array_except($brand->translate('en_US')->toArray(),  array('id'));
            }
            else
            {
                $brand->translate = null;
            }
        }

        $brandsArr = $brands->toArray();
        // Set Cache data
        $timeout = ( Config::has('cache.timeout.long') ) ? 60 : Config::get('cache.timeout.long') ;
        Cache::setCurrentApiData($brandsArr, $timeout);

        return API::createResponse($brandsArr);
    }

    /**
     * @api {get} /collections List FlashSale collections
     * @apiName List FlashSale Collections
     * @apiGroup Collection
     *
     * @apiParam {Number} [depth=10] Level deep limit.
     * @apiParam {Number} [is_category=0] If 1 will filter only category.
     * @apiParam {Number} [page=1] Page number.
     * @apiParam {Number} [limit=20] Total root collections per page.
     *
     * @apiSuccess {Array} data List of collections and sub-collections.
     */
    public function getFlashsaleCollections($app)
    {
        return $this->getCampaignCollections($app, 'flash_sale');
    }

    /**
     * @api {get} /collections List iTruemartTV collections
     * @apiName List iTruemartTV Collections
     * @apiGroup Collection
     *
     * @apiParam {Number} [depth=10] Level deep limit.
     * @apiParam {Number} [is_category=0] If 1 will filter only category.
     * @apiParam {Number} [page=1] Page number.
     * @apiParam {Number} [limit=20] Total root collections per page.
     *
     * @apiSuccess {Array} data List of collections and sub-collections.
     */
    public function getItruemartTvCollections($app)
    {
        return $this->getCampaignCollections($app, 'itruemart_tv');
    }

    /**
     * @api {get} /collections List Percent Discount collections
     * @apiName List Percent Discount Collections
     * @apiGroup Collection
     *
     * @apiParam {Number} [depth=10] Level deep limit.
     * @apiParam {Number} [is_category=0] If 1 will filter only category.
     * @apiParam {Number} [page=1] Page number.
     * @apiParam {Number} [limit=20] Total root collections per page.
     *
     * @apiSuccess {Array} data List of collections and sub-collections.
     */
    public function getdiscountCollections($app)
    {
        // get brand data from cache
        $collectionsArr = Cache::getCurrentApiData();
        if ( !empty($collectionsArr) )
        {
            return API::createResponse($collectionsArr);
        }

        $this->isCategory = 1;
        $arrWith = $this->getArrWith($this->depth);

        $now = date('Y-m-d H:i:s');
        $currentApp = PApp::getCurrentApp();

        $variantIds = SpecialDiscount::where('started_at', '<', $now)->where('ended_at', '>', $now)->where('app_id', $currentApp->id)->lists('variant_id');
        $variantIds = array_unique($variantIds);

        if (empty($variantIds))
        {
            return API::createResponse(array());
        }

        $productIdArr = ProductVariant::whereIn('id', $variantIds)->lists('product_id');
        $products = Product::whereIn('id', $productIdArr)->get();

        $collections = new Illuminate\Database\Eloquent\Collection;
        $collectionPkeys = array();

        foreach ($products as $product)
        {
            $productCollections = $product->collections()->with($arrWith)->where('parent_id', 0)->where('is_category', 1)->get();
            $collectionPkeys = array_unique(array_merge($collectionPkeys, $product->collections()->where('is_category', 1)->lists('pkey')));

            if ( !$productCollections->isEmpty() )
            {
                foreach ($productCollections as $collection)
                {
                    if ( !$collections->contains($collection->id) )
                    {
                        $collections->add($collection);
                    }
                }
            }
        }

        $this->reformatting($collections);

        $collectionsArr = $collections->toArray();

        // Not sure. Am I do right ???
        $this->filterOnly($collectionPkeys, $collectionsArr);

        // Set Cache data
        $timeout = ( Config::has('cache.timeout.long') ) ? 60 : Config::get('cache.timeout.long') ;
        Cache::setCurrentApiData($collectionsArr, $timeout);

        return API::createResponse($collectionsArr);
    }

    /**
     * @api {get} /collections List TrueYou collections
     * @apiName List TrueYou Collections
     * @apiGroup Collection
     *
     * @apiParam {Number} [depth=10] Level deep limit.
     * @apiParam {Number} [is_category=0] If 1 will filter only category.
     * @apiParam {Number} [page=1] Page number.
     * @apiParam {Number} [limit=20] Total root collections per page.
     *
     * @apiSuccess {Array} data List of collections and sub-collections.
     */
    public function getTrueyouCollections($app)
    {
        // get brand data from cache
        $collectionsArr = Cache::getCurrentApiData();
        if ( !empty($collectionsArr) )
        {
            return API::createResponse($collectionsArr);
        }

        $this->isCategory = 1;
        $arrWith = $this->getArrWith($this->depth);

        $now = date('Y-m-d H:i:s');
        $currentApp = PApp::getCurrentApp();

        $variantIds = DB::table('variant_promotion')->where('started_at', '<', $now)->where('ended_at', '>', $now)->where('app_id', $currentApp->id)->lists('variant_id');
        $variantIds = array_unique($variantIds);

        if (empty($variantIds))
        {
            return API::createResponse(array());
        }

        $productIdArr = ProductVariant::whereIn('id', $variantIds)->lists('product_id');
        $products = Product::whereIn('id', $productIdArr)->get();

        $collections = new Illuminate\Database\Eloquent\Collection;
        $collectionPkeys = array();

        foreach ($products as $product)
        {
            $productCollections = $product->collections()->with($arrWith)->where('parent_id', 0)->where('is_category', 1)->get();
            $collectionPkeys = array_unique(array_merge($collectionPkeys, $product->collections()->where('is_category', 1)->lists('pkey')));

            if ( !$productCollections->isEmpty() )
            {
                foreach ($productCollections as $collection)
                {
                    if ( !$collections->contains($collection->id) )
                    {
                        $collections->add($collection);
                    }
                }
            }
        }

        $this->reformatting($collections);

        $collectionsArr = $collections->toArray();

        // Not sure. Am I do right ???
        $this->filterOnly($collectionPkeys, $collectionsArr);

        // Set Cache data
        $timeout = ( Config::has('cache.timeout.long') ) ? 60 : Config::get('cache.timeout.long') ;
        Cache::setCurrentApiData($collectionsArr, $timeout);

        return API::createResponse($collectionsArr);
    }

    private function getCampaignCollections($app, $campaignType = 'flash_sale')
    {
        // get brand data from cache
        $collectionsArr = Cache::getCurrentApiData();
        if ( !empty($collectionsArr) )
        {
            return API::createResponse($collectionsArr);
        }

        $this->isCategory = 1;

        $currentDate = date('Y-m-d H:i:s');
        $variantIdArr = SpecialDiscount::where('app_id', $app->id)
                                        ->where('campaign_type', $campaignType)
                                        ->where('started_at', '<', $currentDate)
                                        ->where('ended_at', '>', $currentDate)
                                        ->lists('variant_id');

        if (empty($variantIdArr))
        {
            return API::createResponse(array());
        }

        $arrWith = $this->getArrWith($this->depth);

        $productIdArr = ProductVariant::whereIn('id', $variantIdArr)->lists('product_id');

        $products = Product::whereIn('id', $productIdArr)->get();

        $collections = new Illuminate\Database\Eloquent\Collection;
        $collectionPkeys = array();

        foreach ($products as $product)
        {
            $productCollections = $product->collections()->with($arrWith)->where('parent_id', 0)->where('is_category', 1)->get();
            $collectionPkeys = array_unique(array_merge($collectionPkeys, $product->collections()->where('is_category', 1)->lists('pkey')));

            if ( !$productCollections->isEmpty() )
            {
                foreach ($productCollections as $collection)
                {
                    if ( !$collections->contains($collection->id) )
                    {
                        $collections->add($collection);
                    }
                }
            }
        }

        $this->reformatting($collections);

        $collectionsArr = $collections->toArray();

        // Not sure. Am I do right ???
        $this->filterOnly($collectionPkeys, $collectionsArr);

        // Set Cache data
        $timeout = ( Config::has('cache.timeout.long') ) ? 60 : Config::get('cache.timeout.long') ;
        Cache::setCurrentApiData($collectionsArr, $timeout);

        return API::createResponse($collectionsArr);
    }

    private function filterOnly($collectionPkeys, &$collectionsArr)
    {
        $recursiveFunction = function($a) use(&$recursiveFunction, $collectionPkeys, &$collectionsArr)
        {
            if (!empty($a))
            {
                foreach ($a as $k=>$v)
                {
                    if ( !in_array($v['pkey'], $collectionPkeys) )
                    {
                        unset($a[$k]);
                        continue;
                    }

                    if (!empty($v['children']))
                    {
                        call_user_func($recursiveFunction, $v['children']);
                    }
                }
            }

            return array_values($a);
        };

        array_walk($collectionsArr, function($item, $key) use(&$collectionsArr, $collectionPkeys, $recursiveFunction)
        {
            $children = $item['children'];

            $collectionsArr[$key]['children'] = $recursiveFunction($children);
        });
    }




    /**
     * @api {get} /collections/:pkey/besteller List Best Seller Products in Collection
     * @apiName List Best Seller
     * @apiGroup Product
     *
     * @apiParam {Number} [max=20] Limit of total best seller product.
     * @apiParam {Number} [fill=1] (1 or 0) Whether to fill up product by new product if there are less best seller products than max product.
     *
     * @apiSuccess {Array} data List of best seller products.
     */
    public function getListBestSeller($app, $collectionKey)
    {
        // get bestseller products data from cache
        $bestSellerProducts = Cache::getCurrentApiData();
        if ( !empty($bestSellerProducts) )
        {
            return API::createResponse($bestSellerProducts);
        }

        $filled = Input::get('fill', 1);
        $num = Input::get('max', 20);

        $collection = Collection::with('bestSeller')->where('pkey', $collectionKey)->first();

        // If Empty $collection, Return 404 Response.
        if (empty($collection))
        {
            return API::createResponse(FALSE, 404);
        }

        // If this collection not have best seller, Return 404 Response.
        if ( !$collection->bestSeller )
        {
            // return API::createResponse(FALSE, 404);
            $bestSellerProducts = array();
        }
        else
        {
            $params = array(
                'collectionKey' => $collection->bestSeller->pkey
            );

            $qstr = "?" . http_build_query($params);
            $response = API::get("/api/{$app->pkey}/products/search" . $qstr);

            $bestSellerProducts = $response['data']['products'];
        }

        // ถ้าจำนวน Best Seller Product มีไม่ครบตามจำนวนที่กำหนด ($num)
        // และต้องการเติม Product ให้เต็มครบจำนวน ($filled = TRUE),
        // ให้นำ Product ใน Collection นั้น มาใส่เพิ่มให้ครบ
        if ($filled == TRUE)
        {
            $params2 = array(
                'collectionKey' => $collection->pkey
            );

            $qstr2 = "?" . http_build_query($params2);
            $response2 = API::get("/api/{$app->pkey}/products/search" . $qstr2);

            $allProducts = $response2['data']['products'];

            $count = count($bestSellerProducts);

            $filteredAllProducts = array_filter($allProducts, function($var) use($bestSellerProducts) {
                foreach ($bestSellerProducts as $p)
                {
                    if ($p['pkey'] == $var['pkey'])
                    {
                        return FALSE;
                    }
                }

                return TRUE;
            });

            foreach ($filteredAllProducts as $p)
            {
                if ($count++ >= $num)
                {
                    break;
                }

                $bestSellerProducts[] = $p;
            }
        }

        // Set Cache data
        $timeout = ( Config::has('cache.timeout.long') ) ? 60 : Config::get('cache.timeout.long') ;
        Cache::setCurrentApiData($bestSellerProducts, $timeout);

        return API::createResponse($bestSellerProducts);
    }

    /**
     * @api {get} /collections/:pkey/products List Products in Collection
     * @apiName List Products in Collection
     * @apiGroup Product
     *
     * @apiParam {Number} [page=1] Page number.
     * @apiParam {Number} [limit=20] Limit of product per page.
     * @apiParam {Number} [offset=0] Offset index.
     * @apiParam {String} [orderBy=published_at] Order by published_at / price / discount.
     * @apiParam {Number} [order=ASC] Sort ascending (ASC) or descending (DESC)
     *
     * @apiSuccess {Array} data List of products.
     */
    public function getListProducts($app, $collectionKey)
    {
        $collection = Collection::where('pkey', $collectionKey)->first();

        // If Empty $collection, Return 404 Response.
        if (empty($collection))
        {
            return API::createResponse(FALSE, 404);
        }

        $params = array(
            'limit' => Input::get('limit', 20),
            'offset' => Input::get('offset', 0),
            'orderBy' => Input::get('orderBy', 'published_at'),
            'order' => Input::get('order', 'desc'),
            'collectionKey' => $collection->pkey
        );

        $qstr = "?" . http_build_query($params);
        $response = API::get("/api/{$app->pkey}/products/search" . $qstr);

        return $response;
    }

    public static function eachTree(Closure $func, $collection, $depth = 99, $currentDepth = 0)
    {
        $collection->each($func);

        if ($depth < $currentDepth)
        {
            return null;
        }

        $currentDepth++;

        $collection->each(function($item) use ($func, $depth, $currentDepth) {
            if ($item->children)
            {
                ApiCollectionsController::eachTree($func, $item->children, $depth, $currentDepth);
            }
        });
    }

    private function reformatting(Illuminate\Database\Eloquent\Collection &$collections)
    {
        // $visibleCollectionFields = array('pkey', 'name', 'is_category', 'image_cover', 'translate', 'slug');
        $visibleCollectionFields = array('pkey', 'name', 'is_category', 'translate', 'slug');

        // If get only root Collection, don't get children collection.
        if ( $this->depth == 0 )
        {
            $depthCount = 0;
        }
        else
        {
            $visibleCollectionFields[] = 'children';

            // Depth not more than 10 (Hard Code.)
            $depthCount = $this->depth;
            $depthCount--;
        }

        $arrWith = $this->getArrWith($depthCount);

        // Set Append for Collection.
        $collections->each(function($collection){
            $collection->setAppends(array('essay', 'metas'));
        });

        // Set Visible to all Collections
        $func = function($collection) use($visibleCollectionFields)
        {
            // $collection->image_cover = '';
            // if (!$collection->files->isEmpty())
            // {
            //     $collection->image_cover = array(
            //         'normal' => (string) UP::lookup($collection->files->first()->attachment_id),
            //         'thumbnail' => (string) UP::lookup($collection->files->first()->attachment_id)->scale('square')
            //     );
            // }

            // Get Translate
            $translate = $collection->translate();
            if (!empty($translate))
            {
                $collection->translate = array_except($collection->translate('en_US')->toArray(),  array('id'));
            }
            else
            {
                $collection->translate = null;
            }

            $collection->setVisible($visibleCollectionFields);
        };

        static::eachTree($func, $collections, $depthCount);
    }

    private function getArrWith($depthCount)
    {
        $arrWith = array('files');
        $tmpStr = 'children';

        for ($i=1; $i<=$depthCount; $i++)
        {
            // get only Category Type
            if ($this->isCategory == 1)
            {
                $arrWith[$tmpStr] = function($q) {
                    return $q->where('is_category', 1)->incurrentapp();
                };

                // $arrWith[$tmpStr] = function($q) {
                //     return $q->where('is_category', 1);
                // };
                // $tmpStr .= '.children';
            }
            else
            {
                $arrWith[$tmpStr] = function($q) {
                    return $q->incurrentapp();
                };

                // $arrWith[] = $tmpStr;
                // $tmpStr .= '.children';
            }

            $tmpStr .= '.children';
        }

        return $arrWith;
    }

    public function getRealPkey($app, $pkeyOrItmId)
    {
        $realPkey = Collection::where('pkey', $pkeyOrItmId)->pluck('pkey');

        if (empty($realPkey))
        {
            $realPkey = DB::table('category_maps')->where('itruemart_id', $pkeyOrItmId)->pluck('pkey');
        }

        return API::createResponse( array('pkey' => $realPkey) );
    }
}