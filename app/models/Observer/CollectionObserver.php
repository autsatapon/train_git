<?php

namespace Observer;

class CollectionObserver {

    public function saved($collection)
    {
        $cacheKeyArr = array("api_collections");

        foreach ($cacheKeyArr as $cacheKey)
        {
            if (\Cache::has($cacheKey))
            {
                \Cache::forget($cacheKey);
            }
        }
    }

}