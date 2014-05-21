<?php

class ApiBrandsController extends ApiBaseController {

    protected $page, $skip, $take;

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
            $this->take = 10;
        }

        $this->skip = $this->take * ($this->page - 1);
    }

    public function getByPkey($app, $brandKey)
    {
        $cacheKey = $app->pkey . '_' . $brandKey;
        // Get brand data from cache.
        if ( Cache::tags('brand')->has($cacheKey) )
        {
            $brandArr = Cache::tags('brand')->get($cacheKey);
            return API::createResponse($brandArr);
        }

        $brand = Brand::where('pkey', $brandKey)->first();

        // If Empty $brand, Return 404 Response.
        if (empty($brand))
        {
            return API::createResponse(FALSE, 404);
        }

        $visibleBrandFields = array('pkey', 'name', 'description', 'metas', 'thumbnail', 'translate', 'slug');
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

        $brandArr = $brand->toArray();

        // Set Cache Data
        $timeout = ( Config::has('cache.timeout.long') ) ? 60 : Config::get('cache.timeout.long') ;
        Cache::tags('brand')->put($cacheKey, $brandArr, $timeout);

        return API::createResponse($brandArr);
    }

    /**
     * @api {get} /brands List brands
     * @apiName Get Brands
     * @apiGroup Brand
     *
     * @apiSuccess {Array} data List of brands.
     */
    public function getIndex()
    {
        // get brand data from cache
        $brandsArr = Cache::getCurrentApiData();
        if ( !empty($brandsArr) )
        {
            return API::createResponse($brandsArr);
        }

        $brandIdArr = Product::where('status', 'publish')->lists('brand_id');

        $brands = Brand::whereIn('id', $brandIdArr)->orderBy('name', 'asc')->get();

        // $visibleBrandFields = array('pkey', 'name');
        $visibleBrandFields = array('pkey', 'name', 'description', 'metas', 'thumbnail', 'translate', 'slug');

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
     * @api {get} /brands/:pkey/products List product of brand
     * @apiName Get Brand's Products
     * @apiGroup Brand
     *
     * @apiParam {Number} [depth=10] Level deep limit.
     * @apiParam {Number} [is_category=0] If 1 will filter only category.
     * @apiParam {Number} [page=1] Page number.
     * @apiParam {Number} [limit=20] Total root collections per page.
     *
     * @apiSuccess {Array} data List of collections and sub-collections.
     */
    public function getListProducts($app, $brandKey)
    {
        $brand = Brand::where('pkey', $brandKey)->first();

        // If Empty $brand, Return 404 Response.
        if (empty($brand))
        {
            return API::createResponse(FALSE, 404);
        }

        $params = array(
            'limit' => Input::get('limit', 20),
            'offset' => Input::get('offset', 0),
            'orderBy' => Input::get('orderBy', 'published_at'),
            'order' => Input::get('order', 'desc'),
            'brandKey' => $brand->pkey
        );

        $qstr = "?" . http_build_query($params);
        $response = API::get("/api/{$app->pkey}/products/search" . $qstr);

        return $response;
    }

    /**
     * @api {get} /brands/flash-sale Get Brands which have FlashSale
     * @apiName Get Brand with Flash Sale
     * @apiGroup Brand
     *
     * @apiSuccess {Array} data List of brands.
     */
    public function getFlashsaleBrands($app)
    {
        // get brand data from cache
        $brandsArr = Cache::getCurrentApiData();
        if ( !empty($brandsArr) )
        {
            return API::createResponse($brandsArr);
        }

        $currentDate = date('Y-m-d H:i:s');

        $specialDiscounts = SpecialDiscount::with(array('productVariant', 'productVariant.product', 'productVariant.product.brand'))
                                        ->where('app_id', $app->id)
                                        ->where('campaign_type', 'flash_sale')
                                        ->where('started_at', '<', $currentDate)
                                        ->where('ended_at', '>', $currentDate)
                                        ->get();

        if ( $specialDiscounts->isEmpty() )
        {
            return API::createResponse(array());
        }

        $endedAtArr = array();
        $campaignName = array();
        foreach ($specialDiscounts as $row)
        {
            $brandId = $row->productVariant->product->brand->id;
            if ( isset($endedAtArr[$brandId]) )
            {
                if ($endedAtArr[$brandId] < $row->ended_at )
                {
                    $endedAtArr[$brandId] = $row->ended_at;
                    $campaignName[$brandId] = $row->discountCampaign->name;
                }
            }
            else
            {
                $endedAtArr[$brandId] = $row->ended_at;
                $campaignName[$brandId] = $row->discountCampaign->name;
            }
        }

        $variantIdArr = $specialDiscounts->lists('variant_id');

        $productIdArr = ProductVariant::whereIn('id', $variantIdArr)->lists('product_id');

        $brandIdArr = Product::whereIn('id', $productIdArr)->lists('brand_id');

        $brands = Brand::whereIn('id', $brandIdArr)->orderBy('name', 'asc')->get();

        // $visibleBrandFields = array('pkey', 'name');
        $visibleBrandFields = array('pkey', 'name', 'metas', 'thumbnail', 'translate', 'slug', 'ended_at', 'campaign_name');
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

            $brand->ended_at = $endedAtArr[$brand->id];
            $brand->campaign_name = $campaignName[$brand->id];
        }

        $brandsArr = $brands->toArray();

        // Set Cache data
        $timeout = ( Config::has('cache.timeout.long') ) ? 60 : Config::get('cache.timeout.long') ;
        Cache::setCurrentApiData($brandsArr, $timeout);

        return API::createResponse($brandsArr);
    }

        /**
     * @api {get} /brands/flash-sale Get Brands which have iTruemart TV
     * @apiName Get Brand with iTruemart TV
     * @apiGroup Brand
     *
     * @apiSuccess {Array} data List of brands.
     */
    public function getItuemartTvBrands($app)
    {
        // get brand data from cache
        $brandsArr = Cache::getCurrentApiData();
        if ( !empty($brandsArr) )
        {
            return API::createResponse($brandsArr);
        }

        $currentDate = date('Y-m-d H:i:s');

        $specialDiscounts = SpecialDiscount::with(array('productVariant', 'productVariant.product', 'productVariant.product.brand'))
                                        ->where('app_id', $app->id)
                                        ->where('campaign_type', 'itruemart_tv')
                                        ->where('started_at', '<', $currentDate)
                                        ->where('ended_at', '>', $currentDate)
                                        ->get();

        if ( $specialDiscounts->isEmpty() )
        {
            return API::createResponse(array());
        }

        $endedAtArr = array();
        foreach ($specialDiscounts as $row)
        {
            $brandId = $row->productVariant->product->brand->id;
            if ( isset($endedAtArr[$brandId]) )
            {
                if ($endedAtArr[$brandId] < $row->ended_at )
                {
                    $endedAtArr[$brandId] = $row->ended_at;
                }
            }
            else
            {
                $endedAtArr[$brandId] = $row->ended_at;
            }
        }

        $variantIdArr = $specialDiscounts->lists('variant_id');

        $productIdArr = ProductVariant::whereIn('id', $variantIdArr)->lists('product_id');

        $brandIdArr = Product::whereIn('id', $productIdArr)->lists('brand_id');

        $brands = Brand::whereIn('id', $brandIdArr)->orderBy('name', 'asc')->get();

        // $visibleBrandFields = array('pkey', 'name');
        $visibleBrandFields = array('pkey', 'name', 'metas', 'thumbnail', 'translate', 'slug', 'ended_at');
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

            $brand->ended_at = $endedAtArr[$brand->id];
        }

        $brandsArr = $brands->toArray();

        // Set Cache data
        $timeout = ( Config::has('cache.timeout.long') ) ? 60 : Config::get('cache.timeout.long') ;
        Cache::setCurrentApiData($brandsArr, $timeout);

        return API::createResponse($brandsArr);
    }

    /**
     * @api {get} /brands/flash-sale Get Brands which have Discount
     * @apiName Get Brand with Discount
     * @apiGroup Brand
     *
     * @apiSuccess {Array} data List of brands.
     */
    public function getDiscountBrands($app)
    {
        // get brand data from cache
        $brandsArr = Cache::getCurrentApiData();
        if ( !empty($brandsArr) )
        {
            return API::createResponse($brandsArr);
        }

        /*
        $now = date('Y-m-d H:i:s');


        $currentApp = PApp::getCurrentApp();

        $variantIds = SpecialDiscount::where('started_at', '<', $now)->where('ended_at', '>', $now)->where('app_id', $currentApp->id)->lists('variant_id');
        $variantIds = array_unique($variantIds);

        if (empty($variantIds))
        {
            return API::createResponse(array());
        }
        */

        $currentDate = date('Y-m-d H:i:s');

        $specialDiscounts = SpecialDiscount::with(array('productVariant', 'productVariant.product', 'productVariant.product.brand'))
                                        ->where('app_id', $app->id)
                                        ->where('started_at', '<', $currentDate)
                                        ->where('ended_at', '>', $currentDate)
                                        ->get();

        if ( $specialDiscounts->isEmpty() )
        {
            return API::createResponse(array());
        }

        $endedAtArr = array();
        foreach ($specialDiscounts as $row)
        {
            $brandId = $row->productVariant->product->brand->id;
            if ( isset($endedAtArr[$brandId]) )
            {
                if ($endedAtArr[$brandId] < $row->ended_at )
                {
                    $endedAtArr[$brandId] = $row->ended_at;
                }
            }
            else
            {
                $endedAtArr[$brandId] = $row->ended_at;
            }
        }

        $variantIds = $specialDiscounts->lists('variant_id');

        $productIds = ProductVariant::whereIn('id', $variantIds)->lists('product_id');

        $brandIds = Product::whereIn('id', $productIds)->lists('brand_id');

        $brands = Brand::whereIn('id', $brandIds)->orderBy('name', 'asc')->get();

        // $visibleBrandFields = array('pkey', 'name');
        $visibleBrandFields = array('pkey', 'name', 'metas', 'thumbnail', 'translate', 'slug', 'ended_at');
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

            $brand->ended_at = $endedAtArr[$brand->id];
        }

        $brandsArr = $brands->toArray();

        // Set Cache data
        $timeout = ( Config::has('cache.timeout.long') ) ? 60 : Config::get('cache.timeout.long') ;
        Cache::setCurrentApiData($brandsArr, $timeout);

        return API::createResponse($brandsArr);
    }

    /**
     * @api {get} /brands/flash-sale Get Brands which have Trueyou
     * @apiName Get Brand with Trueyou
     * @apiGroup Brand
     *
     * @apiSuccess {Array} data List of brands.
     */
    public function getTrueyouBrands($app)
    {
        // get brand data from cache
        $brandsArr = Cache::getCurrentApiData();
        if ( !empty($brandsArr) )
        {
            return API::createResponse($brandsArr);
        }

        /*
        $now = date('Y-m-d H:i:s');
        $currentApp = PApp::getCurrentApp();

        $variantIds = DB::table('variant_promotion')->where('started_at', '<', $now)->where('ended_at', '>', $now)->where('app_id', $currentApp->id)->lists('variant_id');
        $variantIds = array_unique($variantIds);

        if (empty($variantIds))
        {
            return API::createResponse(array());
        }
        */

        $currentDate = date('Y-m-d H:i:s');

        $trueyouPromotions = VariantPromotion::with(array('productVariant', 'productVariant.product', 'productVariant.product.brand'))
                                        ->where('app_id', $app->id)
                                        ->where('started_at', '<', $currentDate)
                                        ->where('ended_at', '>', $currentDate)
                                        ->get();

        if ( $trueyouPromotions->isEmpty() )
        {
            return API::createResponse(array());
        }

        $endedAtArr = array();
        foreach ($trueyouPromotions as $row)
        {
            $brandId = $row->productVariant->product->brand->id;
            if ( isset($endedAtArr[$brandId]) )
            {
                if ($endedAtArr[$brandId] < $row->ended_at )
                {
                    $endedAtArr[$brandId] = $row->ended_at;
                }
            }
            else
            {
                $endedAtArr[$brandId] = $row->ended_at;
            }
        }

        $variantIds = $trueyouPromotions->lists('variant_id');

        $productIds = ProductVariant::whereIn('id', $variantIds)->lists('product_id');

        $brandIds = Product::whereIn('id', $productIds)->lists('brand_id');

        $brands = Brand::whereIn('id', $brandIds)->orderBy('name', 'asc')->get();

        // $visibleBrandFields = array('pkey', 'name');
        $visibleBrandFields = array('pkey', 'name', 'metas', 'thumbnail', 'translate', 'slug', 'ended_at');
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

            $brand->ended_at = $endedAtArr[$brand->id];
        }

        $brandsArr = $brands->toArray();

        // Set Cache data
        $timeout = ( Config::has('cache.timeout.long') ) ? 60 : Config::get('cache.timeout.long') ;
        Cache::setCurrentApiData($brandsArr, $timeout);

        return API::createResponse($brandsArr);
    }

    public function getRealPkey($app, $pkeyOrItmId)
    {
        $realPkey = Brand::where('pkey', $pkeyOrItmId)->pluck('pkey');

        if (empty($realPkey))
        {
            $realPkey = DB::table('brand_maps')->where('itruemart_id', $pkeyOrItmId)->pluck('pkey');
        }

        return API::createResponse( array('pkey' => $realPkey) );
    }
}