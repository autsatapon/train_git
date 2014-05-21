<?php

class StockRepository implements StockRepositoryInterface {

    protected $supplychainUrl = null;
    protected $items = null;

    const HOLD_TEMP = '30 minutes';
    const HOLD_ONLINE = '3 days';
    const HOLD_OFFLINE = '4 days';

    public function __construct()
    {
        $this->supplychainUrl = Config::get('supplychain.url');
    }

    public function getSCStock($inventory_ids)
    {
        $scRepo = new SupplyChainRepository;

        if(is_array($inventory_ids))
            $inventory_ids = implode(',', $inventory_ids);

        $data = $scRepo->curlGet($this->supplychainUrl.'/api_v2/check_stock?inventory_id='.$inventory_ids);
        if($data===false)
            return array();

        $json = json_decode($data, true);
        $json = $json['jsonData'];
        if($json['statusCode']!=200)
            return array();

        $stockData = $json['data'];

        return $stockData ?: array();
    }

    public function updateSCStock($stockData)
    {
        foreach ($stockData as $stockInventory)
        {
            $inventory_id = $stockInventory['inventory_id'];
            $stockLots = $stockInventory['lots'];

            // sync lot with SupplyChain
            // if (is_array($stockLots))
            // {
            //     foreach ($stockLots as $lotDetail)
            //     {
            //         VariantLot::where('inventory_id', $inventory_id)->where('lot_no', $lotDetail['id'])
            //                     ->update(array(
            //                         'sc_remaining'  => intval($lotDetail['remaining']),
            //                         'sc_hold'       => intval($lotDetail['hold']),
            //                         'cost'          => floatval($lotDetail['cost_inc_vat']),
            //                     ));
            //     }
            // }

            // schedule Cron checkStock from nearest_expire_at
            $nearestExpireAt = $stockInventory['nearest_expire_at'];
            $nearestExpireAt = null;
            if ($nearestExpireAt != false)
            {
                $expireTime = strtotime($nearestExpireAt);
                $nearestExpireAt = date('Y-m-d H:i:s', $expireTime);
            }
            else
            {
                $nearestExpireAt = null;
            }

            ProductVariant::where('inventory_id', $inventory_id)->update(array(
                'sc_hold_expired_at' => $nearestExpireAt
            ));
        }
    }

    public function checkSCStock($inventory_ids)
    {
        $stockDetails = $this->getSCStock($inventory_ids);
        if ($stockDetails == false)
            return array();

        $this->updateSCStock($stockDetails);

        return $stockDetails ?: array();
    }

    // public function checkRemaining($app_id, $inventory_id)
    // {
    //     $repo = $this;

    //     // check stock from Cache -> PCMS Variant Lot -> SC API
    //     return Cache::tags("stock-$inventory_id")->remember("remaining-$app_id-$inventory_id", 60, function() use ($repo, $inventory_id, $app_id)
    //     {
    //         $variantLots = VariantLot::with(array('quotas'=>function($query) use ($app_id)
    //             {
    //                 return $query->where('app_id', $app_id);
    //             }))
    //             ->where('inventory_id', $inventory_id)
    //             ->orderBy('priority')->get();

    //         // if never sync with SC; sync then
    //         $variantLot = $variantLots->first();

    //         if (is_null($variantLot))
    //         {
    //             return 0;
    //         }

    //         if($variantLot->sc_remaining === null)
    //         {
    //             $repo->checkSCStock($inventory_id);
    //             $variantLots = VariantLot::with(array('quotas'=>function($query) use ($app_id)
    //                 {
    //                     return $query->where('app_id', $app_id);
    //                 }))
    //                 ->where('inventory_id', $inventory_id)
    //                 ->orderBy('priority')->get();
    //         }

    //         $remaining = 0;
    //         foreach($variantLots as $variantLot)
    //         {
    //             // use quota if has lot quota and quota remaining is lower than lot remaining
    //             $remaining += ($variantLot->quotas!=false && isset($variantLot->quotas[0]) && $variantLot->quotas[0]>=$variantLot->remaining ? $variantLot->quotas[0]->remaining : $variantLot->remaining);
    //         }

    //         return $remaining;
    //     });
    // }

    public function getAppRemainings($app_id, $inventory_ids)
    {
        // check stock from Cache -> SC API

        if (! is_array($inventory_ids))
        {
            $inventory_ids = array($inventory_ids);
        }

        $cachedStocks = array();
        $nonCachedStocks = array();

        // get stock from cache
        foreach ($inventory_ids as $i => $inventory_id)
        {
            $inventoryId = intval($inventory_id);
            if ($inventoryId <= 0)
            {
                unset($inventory_ids[$i]);
                continue;
            }

            $stockData = Cache::tags("stock_{$inventory_id}")->get('stock');
            if ($stockData == false)
            {
                // d($inventory_id, 'non chached');
                array_push($nonCachedStocks, $inventory_id);
            }
            else
            {
                // d($inventory_id, $cachedStocks);
                $cachedStocks[$inventory_id] = $stockData;
            }
        }

        // get stock from SC for those with no cache
        $scStocks = $this->checkSCStock($nonCachedStocks);
        foreach ($scStocks as $inventory_id => $scStock)
        {
            $nearestExpireAt = $scStock['nearest_expire_at'];
            if ($nearestExpireAt == false)
            {
                $expireTime = 60;
            }
            else
            {
                $expireTime = max(time() - strtotime($nearestExpireAt), 0);
            }
            Cache::tags("stock_{$inventory_id}")->put('stock', $scStock, $expireTime);
        }

        // merge
        $stockData = $cachedStocks + $scStocks;

        // --- get remaining ---
        $quotaData = array();

        // get remaining from cache
        foreach ($inventory_ids as $inventory_id)
        {
            $quotaData[$inventory_id] = array(
                'lots' => array(),
            );

            $cachedData = Cache::tags("stock_{$inventory_id}")->get("remaining:{$app_id}:{$inventory_id}");
            if ($cachedData == false)
            {
                // calculate remaining from lot and quota
                $lotNumbers = array_keys($stockData[$inventory_id]['lots']);

                if ($lotNumbers == false)
                {
                    $variantLots = array();
                }
                else
                {
                    $variantLots = VariantLot::with(array('quotas'=>function($query) use ($app_id)
                        {
                            return $query->where('app_id', $app_id);
                        }))
                        ->where('inventory_id', $inventory_id)
                        ->whereIn('lot_no', $lotNumbers)
                        ->orderBy('priority')->get();
                }

                $remaining = 0;
                foreach($variantLots as $variantLot)
                {
                    $lotRemainingData = array_get($stockData, "$inventory_id.lots.{$variantLot->lot_no}");
                    if ($lotRemainingData == false)
                    {
                        continue;
                    }

                    $lotRemaining = $lotRemainingData['remaining'];

                    // if there is quota set for this app
                    if ($variantLot->quotas != false && isset($variantLot->quotas[0]))
                    {
                        $quota = $variantLot->quotas[0];

                        // if quota remaining is positive and lower than lot remaining
                        if ($quota->remaining !== null && $quota->remaining > 0 && $quota->remaining < $lotRemaining)
                            $lotQuota = $quota->remaining;
                        else
                            $lotQuota = $lotRemaining;
                    }
                    else
                    {
                        $lotQuota = $lotRemaining;
                    }

                    $remaining += $lotQuota;
                    $quotaData[$inventory_id]['lots'][$variantLot->lot_no] = $lotQuota;
                }

                $quotaData[$inventory_id]['remaining'] = $remaining;

                // set expire time
                $nearestExpireAt = $stockData[$inventory_id]['nearest_expire_at'];
                if ($nearestExpireAt == false)
                {
                    $expireTime = 60;
                }
                else
                {
                    $expireTime = max(time() - strtotime($nearestExpireAt), 0);
                }
                Cache::tags("stock_{$inventory_id}")->put("remaining:{$app_id}:{$inventory_id}", $quotaData[$inventory_id], $expireTime);
            }
            else
            {
                // return data
                $quotaData[$inventory_id] = $cachedData;
            }
        }

        return $quotaData;
    }

    public function checkRemainings($app_id, $inventory_ids)
    {
        $remainingData = $this->getAppRemainings($app_id, $inventory_ids);

        $remainingStocks = array();
        foreach ($remainingData as $inventory_id => $data)
        {
            $remainingStocks[$inventory_id] = array_get($data, 'remaining');
        }
        return $remainingStocks;
    }

    public function updateRemaining($inventory_id)
    {
        // clear Cache
        Cache::tags("stock_{$inventory_id}")->flush();

        // clear PCMS Variant Lot (force sc_remaining to NULL)
        VariantLot::where('inventory_id', $inventory_id)->update(array(
            'sc_remaining' => null,
            'sc_hold' => null,
        ));

        // TODO: also clear Variant Quota
    }

    /**
     * Pickup n items for App by inventory_id
     * @param  [type] $app_id
     * @param  [type] $inventory_id
     * @param  [type] $quantity
     * @return  Cost and Lot No. (false; if not enough)
     */
    public function pickup($app_id, $inventory_id, $quantity)
    {
        // if not enough
        $remainingData = $this->checkRemainings($app_id, $inventory_id);
        if( array_get($remainingData, $inventory_id) < $quantity )
            return false;

        // $scStock = $this->checkSCStock($inventory_id);
        // if( $scStock===false || $scStock['total']-$scStock['hold'] < $quantity )
        //  return false;

        $variantLots = VariantLot::with(array('quotas'=>function($query) use ($app_id, $inventory_id)
            {
                return $query->where('app_id', $app_id);
            }))
            ->where('inventory_id', $inventory_id)
            // ->where('sc_remaining', '>', '0')
            ->orderBy('priority')->get();

        $items = array(
            'items' => array(),
            'totalCost' => 0,
        );
        $itemCount = 0;
        foreach($variantLots as $variantLot)
        {
            $remaining = ($variantLot->quotas!=false && isset($variantLot->quotas[0]) ? $variantLot->quotas[0]->remaining : $variantLot->remaining);
            for($lotCount=0; $lotCount<$remaining; $lotCount++)
            {
                if($itemCount>=$quantity)
                    break;

                array_push( $items['items'], array(
                    'lot_id' => $variantLot->id,
                    'lot_no' => $variantLot->lot_no,
                    'inventory_id' => $inventory_id,
                    'cost' => floatval($variantLot->cost),
                ));
                $items['totalCost'] += floatval($variantLot->cost);

                $itemCount++;
            }
        }
        $items['averageCost'] = $items['totalCost'] / count($items['items']);

        return $items;
    }

    /**
     * Pickup n items for App by inventory_id
     * @param  [type] $app_id
     * @param  [type] $inventory_id
     * @param  [type] $quantity
     * @return  Cost and Lot No. (false; if not enough)
     */
    public function pickupItems($app_id, $items)
    {
        // $items = array(
        //    '38265' => 2,
        //    '42278' => 1,
        // )
        $stockData = $this->checkSCStock(array_keys($items));
        $pickedItems = array();
        foreach ($items as $pickingInventoryId => $quantity)
        {
            if (array_get($stockData, "$pickingInventoryId.remaining") < $quantity || $quantity <= 0)
            {
                return false;
            }

            $pickedItems[$pickingInventoryId] = array(
                'lots' => array(),
                'averageCost' => 0,
                'totalCost' => 0,
            );

            $pickingQuantity = $quantity;
            foreach ($stockData[$pickingInventoryId]['lots'] as $lotNo => $inventoryLotData)
            {
                if ($pickingQuantity == 0)
                {
                    break;
                }
                if ($inventoryLotData['remaining'] == 0)
                {
                    continue;
                }

                if ($inventoryLotData['remaining'] < $pickingQuantity)
                {
                    $availableItem = $inventoryLotData['remaining'];
                }
                else
                {
                    $availableItem = $pickingQuantity;
                }
                $pickingQuantity -= $availableItem;

                $cost = floatval($inventoryLotData['cost_inc_vat']);
                array_push($pickedItems[$pickingInventoryId]['lots'], array(
                    'lot_no' => $lotNo,
                    'cost' => $cost,
                    'quantity' => $availableItem,
                ));

                $pickedItems[$pickingInventoryId]['totalCost'] += $cost * $availableItem;
            }
            $pickedItems[$pickingInventoryId]['averageCost'] = $pickedItems[$pickingInventoryId]['totalCost'] / $quantity;
        }

        // $pickedItems = array(
        //     '38265' => array(
        //         'lots' => array(
        //             array(
        //                 'lot_no' => 0,
        //                 'cost'   => 480,
        //                 'quantity' => 1,
        //             ),
        //             array(
        //                 'lot_no' => 2,
        //                 'cost'   => 520,
        //                 'quantity' => 1,
        //             ),
        //         ),
        //         'averageCost' => 500,
        //         'totalCost' => 1000,
        //     ),
        //     '42278' => array(
        //         'lots' => array(
        //             array(
        //                 'lot_no' => 5,
        //                 'cost'   => 1200,
        //                 'quantity' => 1,
        //             )
        //         ),
        //         'averageCost' => 1200,
        //         'totalCost'   => 1200,
        //     )
        // );
        return $pickedItems;
    }

    // public function processItems($orderId, $paymentChannel, $items = null)
    // {
    //     if( $items!==null )
    //         $this->items = $items;

    //     $curl = new Curl;
    //     $curl->simple_post($this->supplychainUrl.'/api/setHoldStock', array(
    //         'holdstock' => array(
    //             'data' => array(
    //                 'orderId' => $orderId,
    //                 'paymentChannel' => $paymentChannel,
    //                 'holdPeriod' => $time, // <---
    //                 'inventory' => array(
    //                     array(
    //                         'inventoryid' => $id, // <---
    //                         'total' => $quantity, // <---
    //                         'lotID' => $lot_no // <---
    //                     )
    //                 )
    //             )
    //         )
    //     ));
    // }

    public function recheckExpiryHoldStockWithSC()
    {
        // get inventories that reach sc_hold_expired_at
        $expiryHoldInventories = ProductVariant::whereNotNull('sc_hold_expired_at')->where('sc_hold_expired_at', '<', date('Y-m-d H:i:s'))->get()->lists('inventory_id');

        // check those stock with SupplyChain
        $this->checkSCStock($expiryHoldInventories);
    }

    public function flush($app_id)
    {
        Cache::tags("stock_remaining_{$app_id}")->flush();
    }




    public function holdStock(Order $order, $pickupItems)
    {
        $inventories = array();
        foreach ($pickupItems as $pickupInventoryId => $pickupItem)
        {
            foreach ($pickupItem['lots'] as $pickupLot)
            {
                array_push($inventories, array(
                    'inventory_id'  => $pickupInventoryId,
                    'lot_id'        => $pickupLot['lot_no'],
                    'qty'           => $pickupLot['quantity'],
                ));
            }
        }

        $isCOD = strtolower($order->payment->code) === Order::COD ? true : false;
        $paymentChannel = strtolower($order->payment_channel);

        $params = array(
            'order_id'      => $order->order_id,
            'hold_type'     => $isCOD ? 'permanent' : 'temporary',
            'hold_until'    => $isCOD ? null : date('Y-m-d H:i:s', strtotime('+'.(
                                    $paymentChannel === Order::ONLINE
                                    ? static::HOLD_TEMP
                                    : static::HOLD_OFFLINE
                                ))),
            'payment_channel' => $paymentChannel,
            'inventories'   => $inventories
        );
        // d($params);

        $curl = new Curl;
        $data = $curl->simple_post($this->supplychainUrl.'/api_v2/hold_stock', $params);
        // d($data);
        if($data===false)
        {
            throw new Exception('SupplyChain Timeout');
        }

        $json = json_decode($data, true);
        $json = $json['jsonData'];
        if($json['statusCode']!=200)
        {
            throw new Exception('SupplyChain rejected: '.$data);
        }

        $stockData = $json['data'];
        $this->updateSCStock($stockData);
        // d($stockData);

        return true;
    }

    public function extendHoldStock(Order $order, $holdUntil = null)
    {
        $paymentChannel = strtolower($order->payment_channel);

        if ($order->order_status === Order::STATUS_EXPIRE || $order->order_status === Order::STATUS_CANCEL)
        {
            return false;
        }

        $params = array(
            'order_id' => $order->order_id,
            'hold_type' => 'temporary',
            'hold_until' => $holdUntil ?: date('Y-m-d H:i:s', strtotime('+'.(
                                    $paymentChannel === Order::ONLINE
                                    ? static::HOLD_ONLINE
                                    : static::HOLD_OFFLINE
                                ))),
        );
        // d($params);

        $curl = new Curl;
        $data = $curl->simple_post($this->supplychainUrl.'/api_v2/extend_hold_stock', $params);
        // d($data);
        if($data===false)
        {
            throw new Exception('SupplyChain Timeout');
        }

        $json = json_decode($data, true);
        $json = $json['jsonData'];
        if($json['statusCode']!=200)
        {
            throw new Exception('SupplyChain rejected: '.$data);
        }

        $stockData = $json['data'];
        $this->updateSCStock($stockData);
        // d($stockData);

        return true;
    }

    public function cutStock(Order $order, $reference)
    {
        $params = array(
            'order_id' => $order->order_id,
            'referrer_id' => $reference,
        );

        // d($params);
        $temp = new Tmp;
        $temp->key = 'Cut Stock Param';
        $temp->value = json_encode($params);
        $temp->save();

        $curl = new Curl;
        $data = $curl->simple_post($this->supplychainUrl.'/api_v2/cut_stock', $params);

        $tmp = new Tmp;
        $tmp->key = 'Cut Stock Response';
        $tmp->value = json_encode($data);
        $tmp->save();

        // d($data);
        if($data===false)
        {
            // Log broken cut stock if SupplyChain timeout so we can re cut later by Cron
            $key = 'Broken Cut Stock';
            $value = json_encode($params);

            $existing = Tmp::where('key', $key)->where('value', $value)->get();
            if (count($existing) == 0)
            {
                $temp = new Tmp;
                $temp->key = $key;
                $temp->value = $value;
                $temp->save();
            }

            throw new Exception('SupplyChain Timeout');
        }

        $json = json_decode($data, true);
        $json = $json['jsonData'];
        if($json['statusCode']!=200)
        {
            throw new Exception('SupplyChain rejected: '.$data);
        }

        $stockData = $json['data'];
        $this->updateSCStock($stockData);
        // d($stockData);

        return true;
    }

}