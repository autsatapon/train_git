<?php

class CronRepository implements CronRepositoryInterface {

    protected $status = TRUE;

    protected $hour = null;
    protected $min  = null;

    protected $currentProcess = null;

    public function __construct()
    {
        $this->hour = intval(date('H'));
        $this->min = intval(date('i'));
    }

    protected function processIfNotProcessing($cronKey, $cronStatus = 'processing')
    {
        if( Cron::getValue($cronKey) !== $cronStatus )
        {
            Cron::setValue($cronKey, $cronStatus);
            $this->currentProcess = $cronKey;
            return true;
        }
        return false;
    }
    protected function processDone($cronKey = null)
    {
        Cron::setValue($cronKey ?: $this->currentProcess, null);
    }

    /******************** stock ********************/
    // every 15 minutes; DailySync
    public function dailySync()
    {
        /* php artisan command:cron dailySync */
        if ($this->processIfNotProcessing('dailySync'))
        {
            try {
                $startDate = date('Y-m-d H:i:s', strtotime(Cron::getValue('last-dailysync').' +1 sec'));
                $endDate = date('Y-m-d H:i:s');

                $repo = new SupplyChainRepository();
                $repo->dailySync($startDate, $endDate);
            } catch(Exception $e) {
                $this->status = FALSE;
            }

            $this->processDone();
        }

        return $this->status;
    }

    // every 5 minutes; getNewLot
    public function getNewLot()
    {
        /* php artisan command:cron getNewLot */
        if ($this->processIfNotProcessing('getNewLot'))
        {
            try {
                $startTime = date('Y-m-d H:i:s', strtotime(Cron::getValue('last-synclot', '2014-03-01 00:00:00'), '+1 second'));
                $endTime = date('Y-m-d H:i:s');

                $repo = new SupplyChainRepository();
                $repo->syncLot($startTime, $endTime);
            } catch(Exception $e) {
                $this->status = FALSE;
            }

            $this->processDone();
        }

        return $this->status;
    }

    // clear hold stock every 10 minutes
    public function clearHoldstock()
    {
        /* php artisan command:cron clearHoldstock */
        if($this->processIfNotProcessing('clearHoldstock'))
        {
            try {
                $repo = new StockRepository();
                $repo->recheckExpiryHoldStockWithSC();
            } catch(Exception $e) {
                $this->status = FALSE;
            }

            $this->processDone();
        }

        return $this->status;
    }

    // re-cut stock for those that have failed
    public function reCutStock()
    {
        if($this->processIfNotProcessing('reCutStock'))
        {
            try
            {
                $failedCutStocks = Tmp::where('key', 'Broken Cut Stock')->get();
                if (count($failedCutStocks) > 0)
                {
                    $stockRepo = App::make('StockRepositoryInterface');

                    foreach ($failedCutStocks as $cutStock)
                    {
                        $params = json_decode($cutStock->value);
                        $order = Order::find($params['order_id']);

                        if ($order == false)
                        {
                            continue;
                        }

                        if ($stockRepo->cutStock($order, $params['referrer_id']))
                        {
                            $cutStock->delete();
                        }
                    }
                }
            }
            catch (Exception $e)
            {
                $this->status = FALSE;
            }

            $this->processDone();
        }

        return $this->status;
    }

    /******************** payment ********************/
    // at 11:00 get batch file of paid orders
    public function dailyReconcileOrders()
    {
        /* php artisan command:cron dailyReconcileOrders */
        if ($this->processIfNotProcessing('dailyReconcileOrders'))
        {
            try {
                $wetrust = App::make('wetrust');
                $paymentRepo = App::make('PaymentRepositoryInterface');

                // get waiting for reconcile orders
                $orders = Order::where('payment_status', Order::PAYMENT_WAITING)->get();
                foreach ($orders as $order)
                {
                    $paymentRepo->checkReconcile($order);
                }
            } catch(Exception $e) {
                $this->status = FALSE;
            }

            $this->processDone();
        }

        return $this->status;
    }

    // every 5 minutes; requery online orders
    public function autoRequeryOnlineOrders()
    {
        $paymentRepo = App::make('PaymentRepositoryInterface');

        /* php artisan command:cron autoRequeryOnlineOrders */
        try {
            // get unfinished wetrust online orders
            $orders = Order::notExpire()
                    ->where('payment_channel', Order::ONLINE)
                    ->where('payment_status', Order::PAYMENT_WAITING)
                    ->get();


            foreach ($orders as $order)
            {
                $paymentRepo->checkRequery($order);
            }
        } catch(Exception $e) {
            $this->status = FALSE;
        }

        $this->processDone();

        return $this->status;
    }

    /******************** order tracking ********************/
    public function clearExpiryOrders()
    {
        if ($this->processIfNotProcessing('clearExpiryOrders'))
        {
            try {

                $query = Order::with('validPromotions.promotion')->where('expired_at', '<=', date('Y-m-d 23:59:59', strtotime('yesterday')))->where('order_status', Order::STATUS_WAITING);
                $expiryOrders = $query->get();

                if ($expiryOrders->count() > 0)
                {
                    // Jo - cancel coupon code
                    foreach ($expiryOrders as $key => $order) {

                        // check payment channel that don't online - skip it
                        if(strtolower($order->payment_channel) !== Order::ONLINE)
                        {
                            continue;
                        }

                        // order should have payment channel is online

                        // cancel order
                        $validPromotions = $order->validPromotions;

                        // check again for promotion that valided
                        if (! $validPromotions || $validPromotions->count() > 1)
                        {
                            continue;
                        }

                        // check every valid promotions
                        foreach ($validPromotions as $key => $validPromotion) {

                            // valid promotion should have promotion but for make sure..
                            if (! $validPromotions->promotion)
                            {
                                continue;
                            }

                            $category = @$validPromotions->promotion->promotion_category ?: null;

                            // check category that not match coupon_code or cash_voucher
                            if ( ! in_array($category, array('coupon_code', 'cash_voucher')))
                            {
                                continue;
                            }

                            // double check
                            if (array_get($validPromotions->meta, 'type') != 'promotion_code')
                            {
                                continue;
                            }

                            // so this promotion should have code
                            // we will recover code to alive again
                            // and delete valid promotion

                            // first get promotion code id
                            $promotionCodeId = array_get($validPromotions->meta, 'data.id');

                            // then delete valid promotion
                            $validPromotions->delete();

                            $promotionCode = PromotionCode::find($promotionCodeId);

                            // check promotion code record
                            if (! $promotionCode)
                            {
                                continue;
                            }

                            // delete promotion code log
                            $promotionCode->promotionCodeLogs()->whereOrderId($order->getKey())->delete();

                            // recovery it!
                            $promotionCode->recoverCode();

                            // find in member promotion code if it exists

                            $promotionCode->load('memberPromotionCodes');

                            // get member from order
                            // should be relation hasOne from order
                            // when project upgrade laravel to 4.1
                            $member = Member::whereSsoId($order->customer_ref_id)->first();

                            if ($member)
                            {
                                // found member of this order so....

                                $memberPromotionCodeFilter = function($model) use ($order) {
                                    return ($member->sso_id == $order->customer_ref_id);
                                };

                                // get member promotion code
                                $memberPromotionCode = $promotionCode->memberPromotionCodes
                                                            ->filter($memberPromotionCodeFilter)
                                                            ->first();

                                if ($memberPromotionCode)
                                {
                                    // member has promotion code that he/she reserved
                                    // so undo used time
                                    $memberPromotionCode->used_at = null;
                                    $memberPromotionCode->order_id = null;
                                    $memberPromotionCode->save();
                                }
                            }

                        }

                    }


                    // TODO notify user that his order is expired

                    $query->update(array(
                        'payment_status' => Order::PAYMENT_EXPIRE,
                        'order_status' => Order::STATUS_EXPIRE,
                    ));
                }

            } catch(Exception $e) {
                $this->status = FALSE;
            }

            $this->processDone();
        }

        return $this->status;
    }

    // at 24:00 notify offline users
    public function notifyOfflineUsers()
    {
        /* php artisan command:cron notifyOfflineUsers */
        if (Cron::getValue('last-notifyOfflineUsers') !== date('Y-m-d') && $this->processIfNotProcessing('notifyOfflineUsers'))
        {
            try {
                $unpaidOfflineOrders = Order::notExpire(date('Y-m-d 00:00:00'))
                                            ->where('payment_channel', Order::OFFLINE)
                                            ->where('payment_status', Order::PAYMENT_WAITING);

                if ($unpaidOfflineOrders->count() > 0)
                {
                    $messageRepo = App::make('MessageRepositoryInterface');

                    foreach ($unpaidOfflineOrders as $order)
                    {
                        $messageRepo->pleasePayYourOrder($order);
                    }
                }

                Cron::setValue('last-notifyOfflineUsers', date('Y-m-d'));
            } catch(Exception $e) {
                $this->status = FALSE;
            }

            $this->processDone();
        }

        return $this->status;
    }

    /******************** promotion ********************/
    public function updatePromotionProducts()
    {
        /* php artisan command:cron updatePromotionProducts */
        // if($this->processIfNotProcessing('updatePromotionProducts'))
        // {
        //     try {
                $this->updateActivePromotion();
                $this->updateExpiredPromotion();
        //     } catch(Exception $e) {
        //         $this->status = FALSE;
        //     }

        //     $this->processDone();
        // }

        return $this->status;
    }

    // protected function getActivePromotions()
    // {
    //     $PApp = PApp::getCurrentApp();
    //     $promotions = array();

    //     if (! $PApp)
    //     {
    //         return $promotions;
    //     }

    //     // get all promotion that currently active
    //     $with = array(
    //         "promotions" => function($query)
    //         {
    //             return $query->active();
    //         }
    //     );
    //     $campaigns = Campaign::with($with)->whereAppId($PApp->getKey())->active()->get();

    //     // fetch all promotions in each campaign
    //     // and group them as array
    //     $campaigns->each(function($campaign) use (&$promotions) {
    //         $campaign->promotions->each(function($promotion) use (&$promotions) {
    //             if (empty($promotions[$promotion->getKey()]))
    //             {
    //                 $promotions[$promotion->getKey()] = $promotion;
    //             }
    //         });
    //     });

    //     // return as new array
    //     return array_values($promotions);
    // }

    protected function updateActivePromotion()
    {
        $now = date('Y-m-d H:i:s');
        $variantIds = DB::table('special_discounts')->where('started_at', '<', $now)->where('ended_at', '>', $now)->lists('variant_id');

        if (!empty($variantIds))
        {
            $variantIds = array_unique(array_unique($variantIds));
            $this->updateProductsByVariantIds($variantIds);
        }

        // get promotion trueyou that nearly active

        $cronKey = 'last-checking-promotion-nearly-active';

        $oldTime = Cron::getValue($cronKey) ?: strtodate('datetime', 'first day of last month');
        // $oldTime = strtodate('datetime', 'first day of last month');
        $promotions = Promotion::with('campaign')
                        ->wherePromotionCategory('trueyou')
                        ->where('start_date', '>=', $oldTime)
                        ->where('start_date', '<=', $now)
                        ->active()
                        ->get();

        $promotions->each(function($promotion) {

            $promotion->rebuildPromotion();

            // $discount = array_get($promotion->effects, 'discount', array());
            // if (array_get($discount, 'on') != 'following')
            // {
            //     return false;
            // }

            // $which = array_get($discount, 'which');

            // // get pkey from discount
            // $pkeyFromPromotionEffect = array_get($discount, 'following_items', array());

            // switch ($which) {
            //     case 'brand':
            //         $model = new Brand;
            //         break;

            //     case 'collection':
            //         $model = new Collection;
            //         break;

            //     case 'product':
            //         $model = new Product;
            //         break;

            //     case 'variant':
            //         $model = new ProductVariant;
            //         break;

            //     default:
            //         return false;
            //         break;
            // }

            // if ($pkeyFromPromotionEffect == false)
            // {
            //     return false;
            // }

            // $collection = $model->whereIn('pkey', $pkeyFromPromotionEffect)
            //         ->select('id', 'pkey')
            //         ->get();

            // if ($which == 'variant')
            // {
            //     $pkey = $collection->lists('pkey');
            // }
            // else
            // {
            //     $pkey = \PKeysRepository::prepare($collection, 'child')
            //             ->setExclude('product', explodeFilter(',', array_get($discount, 'exclude_product.un_following_items')))
            //             ->setExclude('variant', explodeFilter(',', array_get($discount, 'exclude_variant.un_following_items')))
            //             ->get();
            // }

            // $pkey = array_flatten($pkey);

            // if ($pkey == false)
            // {
            //     return false;
            // }

            // // we should get pkey that belongs to variant
            // $variants = ProductVariant::with('product')->whereIn('pkey', $pkey)->select('id', 'product_id')->get();

            // $variants->each(function($variant) use ($promotion, $discount) {

            //     $appId = $promotion->campaign->app_id;
            //     $promotionId = $promotion->getKey();
            //     $variantId = $variant->getKey();

            //     $discountType = array_get($discount, 'type');

            //     // check discount type
            //     if (! in_array($discountType, array('price', 'percent')))
            //     {
            //         return false;
            //     }

            //     if ($discountType == 'price')
            //     {
            //         $discountValue = array_get($discount, 'baht');
            //         if (! $discountValue)
            //         {
            //             return false;
            //         }
            //     }

            //     if ($discountType == 'percent')
            //     {
            //         $discountValue = array_get($discount, 'percent');
            //         if (! $discountValue)
            //         {
            //             return false;
            //         }
            //     }

            //     $startAt = $promotion->start_date;
            //     if (strtotime($startAt) < strtotime($promotion->campaign->start_date))
            //     {
            //         $startAt = $promotion->campaign->start_date;
            //     }

            //     $endAt = $promotion->end_date;
            //     if (strtotime($endAt) > strtotime($promotion->campaign->end_date))
            //     {
            //         $endAt = $promotion->campaign->end_date;
            //     }

            //     $trueyous = array_get($promotion->conditions, 'trueyou');

            //     foreach ($trueyous as $key => $trueyou) {
            //         $cardType = array_get($trueyou, 'type');

            //         if (! in_array($cardType, array('red_card', 'black_card')))
            //         {
            //             continue;
            //         }

            //         $card = str_replace('_card', '', $cardType);

            //         $variantPromotion = VariantPromotion::whereAppId($appId)
            //                 ->wherePromotionId($promotionId)
            //                 ->whereVariantId($variantId)
            //                 ->whereCondition($card)
            //                 ->first();

            //         if (! $variantPromotion)
            //         {
            //             $variantPromotion = new VariantPromotion;
            //             $variantPromotion->app_id = $appId;
            //             $variantPromotion->promotion_id = $promotionId;
            //             $variantPromotion->variant_id = $variantId;
            //             $variantPromotion->condition = $card;
            //         }

            //         $variantPromotion->discount = $discountValue;
            //         $variantPromotion->discount_type = $discountType;
            //         $variantPromotion->started_at = $startAt;
            //         $variantPromotion->ended_at = $endAt;
            //         $variantPromotion->hint = $promotion->name;
            //         // s($variantPromotion->toArray());
            //         $variantPromotion->save();

            //     }


            //     $variant->product->touch();

            // });

        });

        Cron::setValue($cronKey, $now);
    }

    protected function updateExpiredPromotion()
    {
        $now = date('Y-m-d H:i:s');
        $variantIds = DB::table('special_discounts')->where('ended_at', '<', $now)->lists('variant_id');

        if (!empty($variantIds))
        {
            $variantIds = array_values(array_unique($variantIds));
            $this->updateProductsByVariantIds($variantIds);
        }


        // get promotion trueyou that nearly expire

        $cronKey = 'last-checking-promotion-nearly-expire';

        $oldTime = Cron::getValue($cronKey) ?: strtodate('datetime', 'first day of last month');
        $promotions = Promotion::with('campaign')
                        ->wherePromotionCategory('trueyou')
                        ->where('end_date', '>=', $oldTime)
                        ->where('end_date', '<=', $now)
                        ->get();

        $promotions->each(function($promotion) {
            $promotion->rebuildPromotion();
            // $productsID = array();

            // $variantsID = VariantPromotion::wherePromotionId($promotion->getKey())->lists('variant_id');

            // if (count($variantsID))
            // {
            //     $listProductsID = ProductVariant::whereIn('id', $variantsID)->lists('product_id');
            //     if (count($listProductsID))
            //     {
            //         $productsID = array_merge($productsID, $listProductsID);
            //     }
            // }


            // VariantPromotion::wherePromotionId($promotion->getKey())->delete();

            // if (count($productsID))
            // {
            //     $products = Product::whereIn('id', $productsID)->get();
            //     $products->each(function($product) {
            //         $product->touch();
            //     });
            // }
        });

        Cron::setValue($cronKey, $now);
    }

    protected function updateProductsByVariantIds($variantIds = array())
    {
        $productIds = ProductVariant::whereIn('id', $variantIds)->lists('product_id');

        if (!empty($productIds))
        {
            $productIds = array_values(array_unique($productIds));

            $products = Product::whereIn('id', $productIds)->get();

            if (!empty($products))
            {
                foreach ($products as $product)
                {
                    $product->updated_at = date('Y-m-d H:i:s');
                    $product->save();
                    //ElasticUtils::updateProduct($product);
                }
            }
        }

    }
}