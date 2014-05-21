<?php

namespace Observer;

class ProductObserver {

    public function saved($product)
    {
        $cacheKeyArr = array("api_product_{$product->pkey}");

        foreach ($cacheKeyArr as $cacheKey)
        {
            if (\Cache::has($cacheKey))
            {
                \Cache::forget($cacheKey);
            }
        }
    }

}