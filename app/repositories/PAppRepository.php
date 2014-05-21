<?php

class PAppRepository implements PAppRepositoryInterface {
    
    public function getByPkey($pkey)
    {
        if ( ! $pkey)
        {
            throw new Exception('pkey is required');
        }
        
        $app = PApp::where('pkey', $pkey)->remember(360)->firstOrFail();
        
        return $app;
    }
    
    public function purgeCacheByPkey($pkey)
    {
        $key = PApp::where('pkey', $pkey)->limit(1)->getQuery()->getCacheKey();
        
        return Cache::forget($key);
    }

}
