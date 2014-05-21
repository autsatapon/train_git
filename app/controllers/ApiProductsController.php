<?php

class ApiProductsController extends ApiBaseController {

    protected $product;

    public function __construct(ProductRepositoryInterface $product)
    {
        parent::__construct();

        $this->product = $product;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        /*
        $product = new ProductRepository();

        $products = $product->executeFormSearch()->with('brand','variants')->get();
        $products = $products->toArray();

        // $response = array(
        //     'data' => $products,
        //     'code' => 100
        // );

        return API::createResponse($products);
        */
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  PApp $app
     * @param  int  $pkey
     * @return Response
     */
    /**
     * @api {get} /products/:pkey Get Product detail by Pkey
     * @apiName Get product
     * @apiGroup Product
     *
     * @apiSuccess {Array} data Product data.
     */
    public function show($app, $pkey)
    {
        // ================ Don't query on database ================
        // $product = Product::where('pkey', $pkey)->first();
        // $productArr = $this->product->getProductByPkey($pkey);
        // if (!$productArr)
        // {
        //      return API::createResponse(FALSE, 404);
        // }
        // Set Cache : Timeout = 60 min.
        // $cacheTimeout = 60;
        // Cache::put($cacheKey, $productArr, $cacheTimeout);

        // $productArr['policies'] = $this->product->getPolicies($product);
        // return API::createResponse($productArr);
        // ================ Don't query on database ================

        // ================ Use This Instead ! ================
        // Get product data from cache.
        $cacheKey = $app->pkey . '_' . $pkey;
        if ( Cache::tags('product')->has($cacheKey) )
        {
            $productData = Cache::tags('product')->get($cacheKey);
            return API::createResponse($productData);
        }

        $product = Product::where('pkey', $pkey)->first();

        $productId = $product->id;
        if (!$productId)
        {
            return API::createResponse(FALSE, 404);
        }
        $elasticaResult = API::get("/api-search/search/{$app->slug}/products/{$productId}", array());

        if (empty($elasticaResult['data']))
        {
            return API::createResponse(FALSE, 404);
        }

        // Reformatting
        $elasticaResult['data']['created_at'] = str_replace('T', ' ', $elasticaResult['data']['created_at']);
        $elasticaResult['data']['updated_at'] = str_replace('T', ' ', $elasticaResult['data']['updated_at']);
        $elasticaResult['data']['published_at'] = str_replace('T', ' ', $elasticaResult['data']['published_at']);

        $elasticaResult['data']['policies'] = $this->product->getPolicies($product);

        $productData = $elasticaResult['data'];

        // Set Cache Data
        $timeout = ( Config::has('cache.timeout.long') ) ? 60 : Config::get('cache.timeout.long') ;
        Cache::tags('product')->put($cacheKey, $productData, $timeout);

        return API::createResponse($productData);
        // ================ Use This Instead ! ================
    }

    public function getVariantByPkey($app, $pkey)
    {
        $variant = ProductVariant::where('pkey', $pkey)->first();

        return API::createResponse($variant);
    }

    public function getVariantByInventoryId($app, $inventoryId)
    {
        $variant = ProductVariant::where('inventory_id', $inventoryId)->first();

        return API::createResponse($variant);
    }

    /**
     * @api {get} /inventories/:inventory_id/remaining Check stock by Inventory Id(s)
     * @apiName Check remaining
     * @apiGroup Product
     *
     * @apiExample Example usage:
     *     /inventories/8583/remaining
     *     /inventories/10751,10753,10755/remaining
     *
     * @apiSuccess {Array} data Stock remaining data.
     */
    public function checkRemaining($app, $inventoryId)
    {
        $stockRepo = new StockRepository;
        $inventoryIds = explode(',', $inventoryId);

        if (count($inventoryIds))
        {
            $stockRemainings = $stockRepo->checkRemainings($app->id, $inventoryIds);
            return API::createResponse(array('data' => array('remaining' => $stockRemainings)));
        }
        return API::createResponse(array('data' => array('remaining' => 0)), 400);

        // $inventoryIds = explode(',', $inventoryId);
        // $count = count($inventoryIds);

        // if ($count === 1)
        // {
  //        $stockRemaining = $stockRepo->checkRemaining($app->id, $inventoryId);
  //        return API::createResponse(array('data' => array('remaining' => $stockRemaining)));
  //       }
  //       else if ($count > 1)
  //       {
  //        $stockRemainings = array();
  //        foreach ($inventoryIds as $inventoryId)
  //        {
  //            $stockRemainings[$inventoryId] = $stockRepo->checkRemaining($app->id, $inventoryId);
  //        }
  //        return API::createResponse(array('data' => array('remaining' => $stockRemainings)));
  //       }
  //      return API::createResponse(array('data' => array('remaining' => 0)), 400);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  PApp $app
     * @param  int  $pkey
     * @return Response
     */
    public function edit($app, $pkey)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  PApp $app
     * @param  int  $pkey
     * @return Response
     */
    public function update($app, $pkey)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  PApp $app
     * @param  int  $pkey
     * @return Response
     */
    public function destroy($app, $pkey)
    {
        //
    }

    public function getRealPkey($app, $pkeyOrItmId)
    {
        $realPkey = Product::where('pkey', $pkeyOrItmId)->pluck('pkey');

        if (empty($realPkey))
        {
            $realPkey = DB::table('product_maps')->where('itruemart_id', $pkeyOrItmId)->pluck('pkey');
        }

        return API::createResponse( array('pkey' => $realPkey) );
    }

}