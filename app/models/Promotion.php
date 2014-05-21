<?php

class Promotion extends PCMSModel {

	public static $labels = array(
        'name' => 'Promotion Name',
    );

    public static $rules = array(
		'campaign_id' => 'required',
        'name' => 'required',
        'start_date' => 'required',
        'end_date' => 'required',
        'status' => 'required'
    );

	/*
	protected $condition;

	public function __construct(Period $period)
	{
		$this->period = $
	}

	public function getCondition()
	{
		//$provider = $this->condition->getProvider($provider);

		return $this->$condition;
	}
	*/

	public function promotionCodes()
	{
		return $this->hasMany('PromotionCode');
	}

	public function campaign()
	{
		return $this->belongsTo('Campaign');
	}

	public function promotionCategory()
	{
		return $this->belongsTo('PromotionCategory');
	}

	public function carts()
	{
		return $this->belongsToMany('Cart')->withPivot('promotion_code_id');
	}

	public function note()
    {
        return $this->morphOne('Note', 'noteable');
    }

	public function setConditionsAttribute($value)
	{
		$this->attributes['conditions'] = json_encode($value);
	}

	public function getConditionsAttribute($value)
	{
		return json_decode($value, true);
	}

	public function setEffectsAttribute($value)
	{
		$this->attributes['effects'] = json_encode($value);
	}

	public function getEffectsAttribute($value)
	{
		return json_decode($value, true);
	}

	public function scopeActive($query)
	{
		$currentDateTime = date('Y-m-d H:i:s');

		return $query->where('end_date', '>=', $currentDateTime)->where('start_date', '<=', $currentDateTime)->where('status', 'activate');
	}

	public function checkActive()
	{
		$start_date = strtotime($this->start_date);
		$end_date = strtotime($this->end_date);
		$current_date = time();

		$isActivate = ($this->status == 'activate') ? TRUE : FALSE ;

		return (boolean) ($end_date > $current_date && $current_date > $start_date && $isActivate);
	}

	public function variants()
    {
        return $this->belongsToMany('ProductVariant', 'variant_promotion', 'promotion_id', 'variant_id');
    }

    public function rebuildPromotion()
    {
    	if (! $this->exists)
    	{
    		throw new Exception("Promotion don't exists.");
    		return false;
    	}

        // Now, We'll rebuild only promotion_category == 'trueyou'
        if ($this->promotion_category != 'trueyou')
        {
            return;
        }

        // rebuild only promotion that have status == 'activate', and campaign that have status = 'activate'
        if ($this->checkActive() == FALSE || $this->campaign->checkActive() == FALSE)
        {
            $this->cleanUpTrueyouPromotion();
            // @TODO : #EP
            // $this->cleanUpEpPromotion();
            return;
        }

        $this->rebuildTrueyouPromotion($this->campaign);
        // @TODO : #EP
        // $this->rebuildEpPromotion($this->campaign);


        // clear cache
        // web must clear all because we don't know product that
        // trueyou active or deactive recently is in which cache
        Cache::tags('brand')->flush();
        Cache::tags('brands')->flush();
        Cache::tags('collection')->flush();
        Cache::tags('collections')->flush();
        Cache::tags('product')->flush();
        Cache::tags('products')->flush();
    }

    // private function rebuildTrueyouPromotion(Campaign $campaign)
    // {
    //     $promotionLib = Promotion\Promotion::factory('trueyou');

    //     $followingItems = $this->effects['discount']['following_items'];
    //     switch ($this->effects['discount']['which']) {
    //         case 'brand':
    //             $followingItemIDs = Brand::whereIn('pkey', $followingItems)
    //                                 ->lists('id');
    //             break;

    //         case 'product':
    //             $followingItemIDs = Product::whereIn('pkey', $followingItems)
    //                                 ->lists('id');
    //             break;


    //         case 'variant':
    //             $followingItemIDs = ProductVariant::whereIn('pkey', $followingItems)
    //                                 ->lists('id');
    //             break;

    //         default:
    //             return;
    //             break;
    //     }

    //     $promotionLib->setRoute($this->effects['discount']['which'], $followingItemIDs);

    //     if (! empty($this->effects['discount']['exclude_product']['un_following_items']) )
    //     {
    //         $excludeProductIDs = Product::whereIn('pkey', $this->effects['discount']['exclude_product']['un_following_items'])
    //                             ->lists('id');
    //         $promotionLib->setExcludeProducts($excludeProductIDs);
    //     }

    //     if (! empty($this->effects['discount']['exclude_variant']['un_following_items']) )
    //     {
    //         $excludeVariantIDs = ProductVariant::whereIn('pkey', $this->effects['discount']['exclude_variant']['un_following_items'])
    //                             ->lists('id');
    //         $promotionLib->setExcludeVariants($excludeVariantIDs);
    //     }

    //     $app_id = $campaign->app_id;
    //     $started_at = $this->start_date;
    //     $ended_at = $this->end_date;
    //     $discount_type = $this->effects['discount']['type'];
    //     $discount = ($discount_type == 'price') ? $this->effects['discount']['baht'] : $this->effects['discount']['percent'] ;
    //     // $condition = ($this->conditions['trueyou'][0]['type'] == 'black_card') ? 'black' : 'red' ;
    //     $hint = $this->name;

    //     $condition = 'red';
    //     foreach ($this->conditions['trueyou'] as $val)
    //     {
    //         if (isset($val['type']) && $val['type'] == 'black_card')
    //         {
    //             $condition = 'black';
    //             break;
    //         }
    //     }

    //     $attrs = array(
    //         'app_id'        => $app_id,
    //         'started_at'    => $started_at,
    //         'ended_at'      => $ended_at,
    //         'discount'      => $discount,
    //         'discount_type' => $discount_type,
    //         'condition'     => $condition,
    //         'hint'          => $hint,
    //     );

    //     $promotionLib->attach($this, $attrs);

    //     $variantsID = VariantPromotion::wherePromotionId($this->getKey())->lists('variant_id');
    //     if (count($variantsID))
    //     {
    //         $listProductsID = ProductVariant::whereIn('id', $variantsID)->lists('product_id');
    //         if (count($listProductsID))
    //         {
    //             $products = Product::whereIn('id', $listProductsID)->get();
    //             $products->each(function($product) {
    //                 $product->touch();
    //             });
    //         }
    //     }

    //     // sd(DB::GetQueryLog());
    // }

    private function rebuildTrueyouPromotion()
    {

        $discount = array_get($this->effects, 'discount', array());

        if (array_get($discount, 'on') != 'following')
        {
            return false;
        }

        $which = array_get($discount, 'which');

        // get pkey from discount
        $pkeyFromPromotionEffect = array_get($discount, 'following_items', array());

        switch ($which) {
            case 'brand':
                $model = new Brand;
                break;

            case 'collection':
                $model = new Collection;
                break;

            case 'product':
                $model = new Product;
                break;

            case 'variant':
                $model = new ProductVariant;
                break;

            default:
                return false;
                break;
        }

        if ($pkeyFromPromotionEffect == false)
        {
            return false;
        }

        $collection = $model->whereIn('pkey', $pkeyFromPromotionEffect)
                ->select('id', 'pkey')
                ->get();

        if ($which == 'variant')
        {
            $pkey = $collection->lists('pkey');
        }
        else
        {
            $pkey = \PKeysRepository::prepare($collection, 'child')
                    ->setExclude('product', explodeFilter(',', array_get($discount, 'exclude_product.un_following_items')))
                    ->setExclude('variant', explodeFilter(',', array_get($discount, 'exclude_variant.un_following_items')))
                    ->get();
        }

        $pkey = array_flatten($pkey);

        if ($pkey == false)
        {
            return false;
        }

        // we should get pkey that belongs to variant
        $variants = ProductVariant::with('product')->whereIn('pkey', $pkey)->select('id', 'product_id')->get();

        $productIDs = array();

        foreach ($variants as $key => $variant) {


            $appId = $this->campaign->app_id;
            $promotionId = $this->getKey();
            $variantId = $variant->getKey();

            $discountType = array_get($discount, 'type');

            // check discount type
            if (! in_array($discountType, array('price', 'percent')))
            {
                return false;
            }

            if ($discountType == 'price')
            {
                $discountValue = array_get($discount, 'baht');
                if (! $discountValue)
                {
                    return false;
                }
            }

            if ($discountType == 'percent')
            {
                $discountValue = array_get($discount, 'percent');
                if (! $discountValue)
                {
                    return false;
                }
            }

            $startAt = $this->start_date;
            if (strtotime($startAt) < strtotime($this->campaign->start_date))
            {
                $startAt = $this->campaign->start_date;
            }

            $endAt = $this->end_date;
            if (strtotime($endAt) > strtotime($this->campaign->end_date))
            {
                $endAt = $this->campaign->end_date;
            }

            $trueyous = array_get($this->conditions, 'trueyou');

            foreach ($trueyous as $key => $trueyou) {
                $cardType = array_get($trueyou, 'type');

                if (! in_array($cardType, array('red_card', 'black_card')))
                {
                    continue;
                }

                $card = str_replace('_card', '', $cardType);

                $variantPromotion = VariantPromotion::whereAppId($appId)
                        ->wherePromotionId($promotionId)
                        ->whereVariantId($variantId)
                        ->whereCondition($card)
                        ->first();

                if (! $variantPromotion)
                {
                    $variantPromotion = new VariantPromotion;
                    $variantPromotion->app_id = $appId;
                    $variantPromotion->promotion_id = $promotionId;
                    $variantPromotion->variant_id = $variantId;
                    $variantPromotion->condition = $card;
                }

                $variantPromotion->discount = $discountValue;
                $variantPromotion->discount_type = $discountType;
                $variantPromotion->started_at = $startAt;
                $variantPromotion->ended_at = $endAt;
                $variantPromotion->hint = $this->name;
                // s($variantPromotion->toArray());
                $variantPromotion->save();

            }

            $productIDs[] = $variant->product->getKey();

        }

        if (count($productIDs) > 0)
        {
            $products = Product::whereIn('id', $productIDs)->get();
            $products->each(function($product) {
                $product->touch();
            });
        }

        // sd(DB::GetQueryLog());

    }

    private function cleanUpTrueyouPromotion()
    {
        $productIDs = array();

        $variantsID = VariantPromotion::wherePromotionId($this->getKey())->lists('variant_id');

        if (count($variantsID))
        {
            $listProductsID = ProductVariant::whereIn('id', $variantsID)->lists('product_id');
            if (count($listProductsID))
            {
                $productIDs = array_merge($productIDs, $listProductsID);
            }
        }

        VariantPromotion::wherePromotionId($this->getKey())->delete();

        if (count($productIDs))
        {
            $products = Product::whereIn('id', $productIDs)->get();
            $products->each(function($product) {
                $product->touch();
            });
        }
    }

}