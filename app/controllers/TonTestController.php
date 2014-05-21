<?php
class TonTestController extends AdminController {

    public $checkout, $payment, $product, $stock, $supplychainUrl, $cart;

    public function __construct(CartRepository $cart,
                                CheckoutRepositoryInterface $checkout,
                                PaymentRepositoryInterface $payment,
                                ProductRepositoryInterface $product,
                                StockRepositoryInterface $stock)
    {
        $this->cart = $cart;
        $this->checkout = $checkout;
        $this->payment = $payment;
        $this->product = $product;
        $this->stock = $stock;

        $this->supplychainUrl = 'http://esourcing.igetapp.com';
    }

    public function getDiscountEnd()
    {
        echo 'hello world';
        sd('name');

        // $app = PApp::with(array('collections.products'))->find(1);
        $app = PApp::with(
                        array(
                            'collections.products' => function($q){
                                return $q->where('status', 'publish');
                            },
                            'collections.products.variants'
                        ))->find(1);

        PApp::setCurrentApp($app);

        $collections = $app->collections;

        $products = new Illuminate\Database\Eloquent\Collection;

        foreach ($collections as $collection)
        {
            foreach ($collection->products as $product)
            {
                if ( !$products->contains($product->id) )
                {
                    $products->add($product);
                }
            }
        }

        foreach ($products as $product)
        {
            d($product->discount_ended);
        }



        // $productIds = array_values(array_unique($app->collections->fetch('products')->collapse()->lists('id')));

        // d($productIds);
        // d($app->collections()->has('products')->get());
        // $collections = $app->collections()->hasWhere('products', function($q){
        //     $q->where('status', 'publish');
        // })->get();
        // d($collections->count());
    }

    public function getTestProductRepo()
    {
        $pkey = 23313844991766;

        $itmApp = PApp::find(1);
        PApp::setCurrentApp($itmApp);

        $product = $this->getProductByPkey($pkey);
        d($product);
    }






    public function getProductByPkey($pkey)
    {
        $product = Product::where('pkey', $pkey)->where('status', 'publish')->first();

        // If Empty Product, Return 404 Response.
        if ( empty($product) )
        {
            return FALSE;
        }

        // Load Relations
        $this->loadRelations($product);

        // Set Appends and Set Visible fields for product an other fields.
        $this->setAppendsAndVisible($product);

        // Get Media Contents & Image Cover
        $this->getMediaContents($product);

        // Get Price Range
        $this->getPriceRange($product);

        // Get Translate
        $this->getTranslate($product);

        $productArr = $product->toArray();

        return $productArr;
    }

    private function loadRelations(Product $product)
    {
        // $loadArr = array('brand', 'collections', 'styleTypes', 'mediaContents', 'variants', 'variants.mediaContents', 'variants.variantStyleOption', 'variants.variantStyleOption.styleType', 'variants.activeSpecialDiscount');
        $loadArr = array('brand', 'collections', 'mediaContents', 'variants', 'variants.activeSpecialDiscount');

        $product->load($loadArr);
    }

    private function setAppendsAndVisible(Product $product)
    {
        // Set Appends and Set Visible Product Fields
        $appendsProductFields = array('discount_ended', 'metas');
        $product->setAppends($appendsProductFields);
        // $visibleProductFields = array('id', 'pkey', 'title', 'slug', 'description', 'key_feature', 'brand', 'collections', 'installment', 'has_variants', 'variants', 'mediaContents', 'tag', 'policies', 'price_range', 'net_price_range', 'special_price_range', 'percent_discount', 'published_at', 'created_at', 'updated_at', 'image_cover', 'translate', 'metas', 'allow_cod', 'discount_ended');
        $visibleProductFields = array('id', 'pkey', 'title', 'slug', 'description', 'key_feature', 'tag', 'installment', 'allow_cod', 'has_variants', 'published_at', 'created_at', 'updated_at', 'discount_ended', 'mediaContents', 'image_cover', 'metas', 'brand', 'collections', 'price_range', 'net_price_range', 'special_price_range', 'percent_discount', 'translate');
        $product->setVisible($visibleProductFields);

        // Set Visible Brand Fields
        $appendsBrandFields = array('thumbnail');
        $product->brand->setAppends($appendsBrandFields);
        $visibleBrandFields = array('pkey', 'name', 'slug', 'thumbnail');
        $product->brand->setVisible($visibleBrandFields);

        // Set Appends and Set Visible Collection fields.
        if ( !$product->collections->isEmpty() )
        {
            $product->collections->each(function($collection)
            {
                $visibleCollectionsFields = array('pkey', 'name');
                $collection->setVisible($visibleCollectionsFields);
            });
        }
    }

    private function getMediaContents(Product $product)
    {
        // Set Appends, Set Visible fields for Media Content.
        // and Set all media content path
        if ( !$product->mediaContents->isEmpty() )
        {
            $product->mediaContents->each(function($mediaContent)
            {
                $visibleMediaContentsFields = array('mode', 'url', 'thumb');
                $mediaContent->url = (string) $mediaContent->link;
                $mediaContent->setVisible($visibleMediaContentsFields);
                $mediaContent->thumb = array(
                    'normal' => (string) $mediaContent->link,
                    'thumbnails' => array(
                        'small'     => (string) UP::lookup($mediaContent->attachment_id)->scale('s'),
                        'medium'    => (string) UP::lookup($mediaContent->attachment_id)->scale('m'),
                        'square'    => (string) UP::lookup($mediaContent->attachment_id)->scale('square'),
                        'large'     => (string) UP::lookup($mediaContent->attachment_id)->scale('l'),
                        'zoom'      => (string) UP::lookup($mediaContent->attachment_id)->scale('xl')
                    )
                );
            });

            // Set Image Cover
            $mediaImage = $product->mediaContents()->where('mode', 'image')->first();
            if ( !empty($mediaImage) )
            {
                $product->image_cover = array(
                    'normal' => (string) $mediaImage->link,
                    'thumbnails' => array(
                        'small'     => (string) UP::lookup($mediaImage->attachment_id)->scale('s'),
                        'medium'    => (string) UP::lookup($mediaImage->attachment_id)->scale('m'),
                        'square'    => (string) UP::lookup($mediaImage->attachment_id)->scale('square'),
                        'large'     => (string) UP::lookup($mediaImage->attachment_id)->scale('l'),
                        'zoom'      => (string) UP::lookup($mediaImage->attachment_id)->scale('xl')
                    )
                );
            }
        }
    }

    private function getPriceRange(Product $product)
    {
        // set Price Range of product
        $priceMax = 0;
        $netPriceMax = 0;
        $specialPriceMax = 0;
        $dcMax = 0;

        // Is everything OK ? ..... When I didn't load 'activeSpecialDiscount'?
        // $variants = $product->variants()->with('activeSpecialDiscount')->get();
        $variants = $product->variants;

        foreach ($variants as $variant)
        {
            $priceMax = ($priceMax < $variant->price) ? $variant->price : $priceMax ;
            $netPriceMax = ($netPriceMax < $variant->net_price) ? $variant->net_price : $netPriceMax ;
            $specialPriceMax = ($specialPriceMax < $variant->special_price) ? $variant->special_price : $specialPriceMax ;
            $dcMax = ($dcMax < $variant->percent_discount) ? $variant->percent_discount : $dcMax ;
        }

        $priceMin = $priceMax;
        $netPriceMin = $netPriceMax;
        $specialPriceMin = $specialPriceMax;
        $dcMin = $dcMax;

        foreach ($variants as $variant)
        {
            $priceMin = ($priceMin > $variant->price) ? $variant->price : $priceMin ;
            $netPriceMin = ($netPriceMin > $variant->net_price) ? $variant->net_price : $netPriceMin ;
            $specialPriceMin = ($specialPriceMin > $variant->special_price) ? $variant->special_price : $specialPriceMin ;
            $dcMin = ($dcMin > $variant->percent_discount) ? $variant->percent_discount : $dcMin ;
        }

        $product->price_range = array(
            'max' => $priceMax,
            'min' => $priceMin
        );

        $product->net_price_range = array(
            'max' => $netPriceMax,
            'min' => $netPriceMin
        );

        $product->special_price_range = array(
            'max' => $specialPriceMax,
            'min' => $specialPriceMin
        );

        $product->percent_discount = array(
            'max' => $dcMax,
            'min' => $dcMin
        );
    }

    private function getTranslate(Product $product)
    {
        // Get Translate
        $translate = $product->translate();
        if (!empty($translate))
        {
            $product->translate = array_except($product->translate('en_US')->toArray(),  array('id'));
        }
        else
        {
            $product->translate = null;
        }
    }















































    public function getTestDeleteElastic()
    {
        try
        {
            API::delete("/api-search/document/itruemart/products/288");
        }
        catch(Exception $e)
        {
            d($e);
        }
    }

    public function getRefresh()
    {
        echo date('Y-m-d H:i:s');
        echo '<script>';
        echo 'var delay = 1000;';
        echo 'setTimeout(function(){';
        echo 'window.location.reload();';
        echo '}, delay);';
        echo '</script>';
    }

    // public function getTestUpload()
    // {
    //     $brand = Brand::find(26);

    //     // $imageUrl = 'http://cdn.itruemart.com/files/product/126/3230/mUoigeB70x6XfnLVAzpErs9bvlGq3kdZQJ4v5jVh8P2FIRWcOaKNS1twMuCHTD_original.jpg';
    //     $imageUrl = 'http://203.144.214.70/files/product/126/3230/mUoigeB70x6XfnLVAzpErs9bvlGq3kdZQJ4v5jVh8P2FIRWcOaKNS1twMuCHTD_original.jpg';

    //     $results = UP::inject(array('remote' => true))->upload($brand, $imageUrl)->resize()->getResults();
    //     d($results);
    // }

    public function getTestCurl()
    {
        $url = 'http://cdn.itruemart.com/files/product/126/3230/mUoigeB70x6XfnLVAzpErs9bvlGq3kdZQJ4v5jVh8P2FIRWcOaKNS1twMuCHTD_original.jpg';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $bin = curl_exec($ch);
        curl_close($ch);

        // $bin = file_get_contents($url);

        echo $bin;
    }

    public function getTestUp()
    {
        $brand = Brand::find(26);
        $imageUrl = 'http://cdn.itruemart.com/files/product/126/3230/7faithIJuKOP8ok5S9BxzN0QApLdv4cCTDMqGrVWmwvF1RU36g2ZsjeHVEbXln_big.jpg';
        $results = UP::inject(array('remote' => true))->upload($brand, $imageUrl)->resize()->getResults();

        d($results);
    }

    public function getStyle()
    {
        $colorStyleOption = StyleOption::where('text', 'asdf')->first();
        d($colorStyleOption);
    }

    public function getOrder($id = 94)
    {
        $order = Order::find($id);

        // d($order, $order->shipments);
        //

        if ( !$order->shipments->isEmpty() )
        {
            foreach ($order->shipments as $shipment)
            {
                d($shipment->method->name);
            }
        }
    }

    public function getTonTest()
    {
        $now = date('Y-m-d H:i:s');
        $variantIds = DB::table('special_discounts')->where('started_at', '<', $now)->where('ended_at', '>', $now)->lists('variant_id');
        $variantIds = array_values(array_unique($variantIds));
        $productIds = ProductVariant::whereIn('id', $variantIds)->lists('product_id');
        $productIds = array_values(array_unique($productIds));
        $products = Product::whereIn('id', $productIds)->get();

        d($variantIds, $productIds, $products);
    }

    public function getAppsByProduct()
    {
        $productApps = new Illuminate\Database\Eloquent\Collection;

        $product = Product::first();

        $productCollections = $product->collections;

        if ( !$productCollections->isEmpty() )
        {
            foreach ($productCollections as $collection)
            {
                $collectionApps = $collection->apps;

                if ( !$collectionApps->isEmpty() )
                {
                    foreach($collectionApps as $app)
                    {
                        if ( !$productApps->contains($app->id) )
                        {
                            $productApps->add($app);
                        }
                    }
                }
            }
        }

        return $productApps;
    }

    public function getProductsByApp()
    {
        $apps = PApp::all();

        $products = array();

        foreach ($apps as $app)
        {
            $appProducts = new Illuminate\Database\Eloquent\Collection;

            $collections = $app->collections;

            if ( !$collections->isEmpty() )
            {
                foreach ($collections as $collection)
                {
                    $collectionProducts = $collection->products()->whereStatus('publish')->get();

                    if ( !$collectionProducts->isEmpty() )
                    {
                        foreach ($collectionProducts as $product)
                        {
                            if ( !$appProducts->contains($product->id) )
                            {
                                $appProducts->add($product);
                            }
                        }
                    }
                }
            }

            $products[$app->slug] = $appProducts;
        }

        // d($products);

        foreach ($products as $p)
        {
            d($p->toArray());
        }
    }


















    public function getTestFlashSale()
    {
        $product = Product::first();
        ElasticUtils::updateProduct($product);
    }


    public function getArrayWalk()
    {
        $arr1 = array();

        $arr2 = array(
            "red"   => null,
            "black" => array(
                'discount'      => "99",
                'discount_type' => "percent",
                'started_at'    => "2014-01-15 00:00:00",
                'ended_at'      => "2014-01-22 00:00:00",
            )
        );

        $arr3 = array(
            "red"   => array(
                'discount'      => "10",
                'discount_type' => "price",
                'started_at'    => "2014-01-15 00:00:00",
                'ended_at'      => "2014-01-22 00:00:00",
            ),
            "black" => null,
        );

        $arr4 = array(
            "red"   => array(
                'discount'      => "20",
                'discount_type' => "price",
                'started_at'    => "2014-01-15 00:00:00",
                'ended_at'      => "2014-01-22 00:00:00",
            ),
            "black" => array(
                'discount'      => "50",
                'discount_type' => "percent",
                'started_at'    => "2014-01-15 00:00:00",
                'ended_at'      => "2014-01-22 00:00:00",
            )
        );

        $a1 = array_key_exists('red', $arr1);
        $a2 = array_key_exists('red', $arr2);
        $a3 = array_key_exists('red', $arr3);
        $a4 = array_key_exists('red', $arr4);
        $a5 = array_key_exists('black', $arr1);
        $a6 = array_key_exists('black', $arr2);
        $a7 = array_key_exists('black', $arr3);
        $a8 = array_key_exists('black', $arr4);

        // $func = function($item, $key)
        // {
        //     if (empty($item))
        //     {
        //         return FALSE;
        //     }
        // };

        // $a1 = array_walk($arr1, $func);
        // $a2 = array_walk($arr2, $func);
        // $a3 = array_walk($arr3, $func);
        // $a4 = array_walk($arr4, $func);

        d($a1, $a2, $a3, $a4, $a5, $a6, $a7, $a8);
    }





    public function getVariant()
    {
        $variants = ProductVariant::all();

        foreach ($variants as $v)
        {
            echo "<h4>{$v->id} - {$v->title}</h4>";

            if ( !$v->variantStyleOptions->isEmpty() )
            {
                // d($v->variantStyleOptions->toArray());
                // echo '<p>variant มี style options</p>';

                foreach ($v->variantStyleOptions as $vso)
                {
                    $mediaContents = $vso->mediaContents()->where('mode', 'image')->get();
                    if ( !$mediaContents->isEmpty() )
                    {
                        // d($vso->id, $mediaContents->image);
                        foreach ($mediaContents as $m)
                        {
                            $img = (string) $m->image;
                            s($vso->id, $img);
                        }
                    }
                }
            }
            else
            {
                echo '<p>ไม่มี style options</p>';
            }

            echo "<br><br><br>";
        }
    }

    public function getPrice()
    {
        $itmApp = PApp::find(1);
        PApp::setCurrentApp($itmApp);

        $currentApp = PApp::getCurrentApp();
        d($currentApp);

        $products = Product::all();

        foreach ($products as $product)
        {
            d($product->price, $product->normal_price, $product->net_price, $product->special_price);
        }
    }

    public function getApps()
    {
        $apps = Papp::all();

        foreach ($apps as $app)
        {
            echo $app->slug;
            echo '<br>';
        }
    }




    public function getMetadata($id)
    {
        $collection = Collection::find($id);
        $metasRawArr = $collection->metadatas->toArray();

        $metas = array();
        foreach ($metasRawArr as $meta)
        {
            $metas[$meta['key']] = $meta['value'];
        }

        s($metas);
    }




    public function getCheckout()
    {
        $order = Order::find(31);
        $wetrust = App::make('wetrust');
        $rs = $wetrust->buildXML($order);
        $rs2 = \Wetrust\RC4::EncryptRC4($config['rc4key'], $rs);

        d($rs, $rs2);
    }

    public function getHoldstock()
    {
        // $holdPeriod = 259200;
        $holdPeriod = 30;
        $dataHoldStock = array(
            'orderId'        => 999999,
            'paymentChannel' => 'Online',
            'holdPeriod'     => $holdPeriod,
            'inventory'      => array()
        );

        $dataHoldStock['inventory'][1] = array(
            'inventoryid' => 8584,
            'total'       => 1,
            'lotID'       => 0
        );

        $params = array();
        $params['holdstock'] = json_encode( array("data" => $dataHoldStock) );

        $curl = new Curl;
        $response = $curl->simple_post("{$this->supplychainUrl}/api/setHoldStock", $params);

        $response = json_decode($response, TRUE);

        d($response);
        die();












        $orderId = 1;
        $appId = 4;
        $itemInventoryId = 8584;
        $qty = 1;


        $asdf = $this->stock->pickup($appId, $itemInventoryId, $qty);
        d($asdf);
    }

    public function getAsdf()
    {
        $product = Product::find(12);
        $policies = $this->product->getPolicies($product);

        d($policies);
    }









    public function getTestTest()
    {
        $order = Order::find(43);
        d($order);die();
    }


    public function getPaymentData()
    {
        $order = Order::find(43);
        // return $this->payment->buildXmlData($order);
    }













    public function getShippingMethod($id)
    {
        $cartDetail = CartDetail::find($id);

        $rs = $this->c->getShippingMethodsByCartDetail($cartDetail);

        d($rs);
    }

    /*
    protected $accesses = array(
        'getIndex'  => array('groups' => 'admin|mod'),
        'getAaa'    => array('groups' => 'admin'),
        'getBbb'    => array('groups' => 'admin'),
        'getCcc'    => array('groups' => 'admin'),
        'getDdd'    => array('groups' => 'admin'),
    );
    */

    /*
    protected $accesses = array(
        'getIndex'  => array('permissions' => 'users'),
        'getAaa'    => array('permissions' => 'blog|product'),
        'getBbb'    => array('permissions' => 'product'),
        'getCcc'    => array('permissions' => 'blog|users|product'),
        'getDdd'    => array('permissions' => 'users|test'),
    );
    */

    public function getShippingFee()
    {
        // d($this->c);
        echo '555';
        $this->c->calculateShippingFee(1,1,Input::get('weight'));
    }

    public function getXxx()
    {

        $asdf = Product::whereId(1)->first();

        d($asdf);

        /*
        $variant = ProductVariant::find(1);
        $variant->title .= 'xxx';
        // $product->title = 'Samsung Galaxy Tab 2 7.0';

        $variant->save();
        */

        /*
        $collection = Collection::find(1);
        $collection->products()->sync(array(1,2,3));
        */

/*
        $data = Cache::get('asdf');
        d($data);

        if ($data === NULL)
        {
            Cache::put('asdf', 'CACHE VALUE', 1);
            $data = Cache::get('asdf');
            d($data);
        }
*/


    }

    public function getApi()
    {
        $users = User::all();
        return API::createResponse($users);
    }

    public function getVariantBrand()
    {
        $variant = ProductVariant::find(2);
        $variant->load('product');

        $product = $variant->product;
        $product->load('brand');

        $brand = $product->brand;

        d($variant->title);
        d($product->title);
        d($brand->name);
    }

    public function getEditor()
    {
        $product = Product::find(1);
        $this->data['product'] = $product;
        return $this->theme->of('tontest.editor', $this->data)->render();
    }

    public function getIndex()
    {
        echo 'Test Index';
    }

    public function getAaa()
    {
        echo 'Test AAA';
    }

    public function getBbb()
    {
        echo 'Test BBB';
    }

    public function getCcc()
    {
        echo 'Test CCC';
    }

    public function getDdd()
    {
        echo 'Test DDD';
    }

    public function getProduct()
    {
        echo '555';
    }







    public function getCacheSection()
    {
        if ( Cache::tags('kousuke')->has('aaa') )
        {
            echo 'Cache Hit';
            $arr = Cache::tags('kousuke')->get('aaa');
        }
        else
        {
            echo 'Cache Miss';
            $arr = array(1,3,5,7,9);
            Cache::tags('kousuke')->put('aaa', $arr, 1);
        }

        d($arr);
    }

    public function getCache()
    {
        // d( strstr(Route::currentRouteAction(), '@', true) );
        // d( Cache::tags('banner')->getApiCacheKey() ); die();
        // echo Cache::generateCacheKeyByUri();
        // echo Cache::getPrefix();
        // die();

        // $params = Input::all();
        // $qstr = http_build_query($params);


        // $cacheKey = 'api/45311375168544/banners?position=15|16|17|18|19|20|21|22|23|24|25|26|27|28|29|30|31|32|2|33';

        // $section = 'collection';
        // $cacheKey = '3441387446408';

        // $asdf = array();
        // if (Cache::tags($section)->has($cacheKey))
        // {
        //     $asdf = Cache::tags($section)->get($cacheKey);
        //     echo 'Cache HIT !';
        // }
        // else
        // {
        //     echo 'Cache Miss ! ';
        // }

        // d( $cacheKey, $asdf );

        // $cacheKey = 'api/45311375168544/brands';

        // $asdf = array();
        // if (Cache::tags('brands')->has($cacheKey))
        // {
        //     $asdf = Cache::tags('brands')->get($cacheKey);
        //     echo 'Cache HIT !';
        // }
        // else
        // {
        //     echo 'Cache Miss ! ';
        // }

        // d( $cacheKey, $asdf );

        $arr = array(
            'api/45311375168544/collections/flash-sale',
            'api/45311375168544/collections/trueyou',
            'api/45311375168544/collections/itruemart-tv',
            'api/45311375168544/collections/discount',
        );

        foreach ($arr as $a)
        {
            $cacheKey = $a;

            $asdf = array();
            if (Cache::tags('collections')->has($cacheKey))
            {
                $asdf = Cache::tags('collections')->get($cacheKey);
                echo 'Cache HIT !';
            }
            else
            {
                echo 'Cache Miss ! ';
            }

            d( $cacheKey, $asdf );
        }

        $arr = array(
            'api/45311375168544/brands/flash-sale',
            'api/45311375168544/brands/trueyou',
            'api/45311375168544/brands/itruemart-tv',
            'api/45311375168544/brands/discount',
        );

        foreach ($arr as $a)
        {
            $cacheKey = $a;

            $asdf = array();
            if (Cache::tags('brands')->has($cacheKey))
            {
                $asdf = Cache::tags('brands')->get($cacheKey);
                echo 'Cache HIT !';
            }
            else
            {
                echo 'Cache Miss ! ';
            }

            d( $cacheKey, $asdf );
        }



        // Cache::tags('collections')->flush();

        // $cacheKey = '67713836244520';

        // $asdf = array();
        // if (Cache::tags('brand')->has($cacheKey))
        // {
        //     $asdf = Cache::tags('brand')->get($cacheKey);
        //     echo 'Cache HIT !';
        // }
        // else
        // {
        //     echo 'Cache Miss ! ';
        // }

        // d( $cacheKey, $asdf );
    }

    public function getTime()
    {
        $start = microtime(true);

        // $data = API::get('/api/45311375168544/banners?position=15|16|17|18|19|20|21|22|23|24|25|26|27|28|29|30|31|32|2|33', array());
        $data = API::get('/api/45311375168544/products/search', array());

        $time_taken = microtime(true) - $start;

        echo $time_taken;
        echo '<br>';

        // $start = microtime(true);

        // // $data = API::get('/api/45311375168544/banners?position=15|16|17|18|19|20|21|22|23|24|25|26|27|28|29|30|31|32|2|33', array());
        // $data = API::get('/api/45311375168544/brands', array());

        // $time_taken = microtime(true) - $start;

        // echo $time_taken;
    }

}