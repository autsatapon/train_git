<?php

namespace Observer;

class ProductVariantObserver extends ProductSearchObserver {

    // public function saved($variant)
    // {
    //     $cacheKeyArr = array("api_product_{$variant->product->pkey}");

    //     foreach ($cacheKeyArr as $cacheKey)
    //     {
    //         if (\Cache::has($cacheKey))
    //         {
    //             \Cache::forget($cacheKey);
    //         }
    //     }
    // }


    // public function created($model)
    // {
    //     $model = $model->product;

    //     return parent::created($model);
    // }

}