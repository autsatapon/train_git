<?php

class ShopRepository {

    public function buildShops(Illuminate\Database\Eloquent\Collection $materials)
    {
        $shopIds = array_unique($materials->lists('shop_id'));
        if (count($shopIds) > 0)
        {
            $existingShops = Shop::whereIn('shop_id', $shopIds)->get()->lists('shop_id');
            $newShops = array_diff($shopIds, $existingShops);

            foreach ($newShops as $newShop)
            {
                $shop = new Shop;
                $shop->shop_id = $newShop;
                $shop->save();
            }
        }
    }

}