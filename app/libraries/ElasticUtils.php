<?php

class ElasticUtils {

    public static function removeProduct(Product $product)
    {
        $apps = PApp::all();
        // $apps = static::getAppsByProduct($product);

        // d('DELETE IN APPS', $apps);

        foreach ($apps as $app)
        {
            PApp::setCurrentApp($app);

            $index = $app->slug;
            $type = 'products';
            $id = $product->getKey();

            // d('delete', $app, $index, $type, $id);

            API::delete("/api-search/document/{$index}/{$type}/{$id}", array());
        }
    }

    public static function updateProduct(Product $product)
    {
        // Do nothing when this product doesn't publish yet.
        if ($product->status != 'publish' or $product->active != 1)
        {
            // d('CANNOT UPDATE 555555555', $product->status, $product->active);
            return;
        }

        // $apps = PApp::all();
        $apps = static::getAppsByProduct($product);

        // d('PUT IN APPS', $apps);

        foreach ($apps as $app)
        {
            PApp::setCurrentApp($app);

            $index = $app->slug;
            $type = 'products';

            $id = $product->getKey();
            $doc = static::getDoc($product);

            // s($doc);

            if (!empty($doc))
            {
                // s('put', $app, $index, $type, $id);
                API::put("/api-search/document/{$index}/{$type}/{$id}", $doc);
            }
        }

        self::flushCache($product->pkey);

        // die;
    }

    private static function flushCache($pkey)
    {
        Cache::tags('products')->flush();

        // delete product cache via papp
        $appPkeys = PApp::lists('pkey');
        foreach ($appPkeys ?: array() as $appPkey)
        {
            Cache::tags('product')->forget($appPkey.'_'.$pkey);
        }

        Cache::tags('product')->forget($pkey);
    }

    // private static function getAppsByProduct(Product $product)
    public static function getAppsByProduct(Product $product)
    {
        $productApps = new Illuminate\Database\Eloquent\Collection;

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

	// public static function updateProduct(Product $product)
	// {
	// 	// Do nothing when this product doesn't publish yet.
 //        if ($product->status != 'publish')
 //        {
 //            return;
 //        }

 //        $apps = PApp::all();

 //        foreach ($apps as $app)
 //        {
 //        	// Now, We'll test on "iTrueMart" App Only.
 //            if ($app->id != 1)
 //            {
 //                continue;
 //            }

 //            PApp::setCurrentApp($app);

 //            $index = $app->slug;
 //            $type = 'products';

 //            $id = $product->getKey();
 //            $doc = static::getDoc($product);

 //            if (!empty($doc))
 //            {
 //                API::put("/api-search/document/{$index}/{$type}/{$id}", $doc);
 //            }
 //        }
	// }

	private static function getDoc(Product $product)
    {
        $pkey = $product->pkey;

        $productRepo = new ProductRepository;

        $product = $productRepo->getProductByPkey($pkey);

        return $product;
    }
}