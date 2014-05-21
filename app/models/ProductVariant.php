<?php

class ProductVariant extends PCMSModel {

    protected $table = 'variants';
    protected $sortedDimensions = array();
    public static $rules = array(
        //'price'        => 'required|numeric|min:0',
        //'normal_price' => 'required|numeric|min:0'
        'price' => 'if_else:(free_item=no),required+numeric+min:1,required+numeric+min:0'
    );
    protected $appends = array('net_price', 'special_price', 'stock_type_code', 'ep_discount_price');
    public static $labels = array(
        'free_item' => 'Free Item?',
        'net_price' => 'Net. Price',
        'special_price' => 'Special Price',
        'allow_installment' => 'Allow Installment?',
        'installment_period' => 'Installment Period',
    );

    public static function boot()
    {
        $rules = static::$rules;

        static::saving(function($model) use ($rules)
            {
                // if normal_price is set (use special_price).
                if ($model->normal_price > 0)
                {
                    $model->addValidate(
                        array('normal_price' => $model->normal_price), array('normal_price' => 'required|numeric|min:'.$model->price)
                    );
                }

                if ($model->free_item === 'yes')
                {
                    $model->normal_price = $model->normal_price > 0 ? $model->normal_price : $model->price;
                    $model->price = 0;
                }

                // else
                // {
                // 	$model->addValidate(
                // 		array('price' => $model->price),
                // 		array('price' => 'required|numeric|min:1')
                // 	);
                // }
            });

        static::deleted(function($model)
            {
                VariantStyleOption::where('variant_id', $model->id)->delete();
            });

        static::restored(function($model)
            {
                VariantStyleOption::withTrashed('variant_id', $model->id)->restore();
            });

        parent::boot();
    }

    // has many?
//    public function orderShipmentItem()
//    {
//        return $this->belongsTo('OrderShipmentItem', 'inventory_id');
//    }

    public function vendor()
    {
        return $this->belongsTo('VVendor', 'vendor_id');
    }

    public function product()
    {
        // return $this->belongsTo('Product', 'variant_id');
        return $this->belongsTo('Product');
    }

//    public function brand()
//    {
//        return $this->product()->belongsTo('Brand');
//    }

    public function variantStyleOption()
    {
        return $this->hasMany('VariantStyleOption', 'variant_id');
    }

    public function variantStyleOptions()
    {
        return $this->hasMany('VariantStyleOption', 'variant_id');
    }

    public function styleType()
    {
        return $this->belongsTo('StyleType');
    }

    public function styleOptions()
    {
        return $this->belongsToMany('StyleOption', "variant_style_options", "variant_id", "style_option_id")
                ->withPivot('style_type_id');
    }

    /**
     * reletion to media_content
     */
    public function mediaContents()
    {
        return $this->morphMany('MediaContent', 'mediable');
    }

    public function specialDiscounts()
    {
        return $this->hasMany('SpecialDiscount', 'variant_id');
    }

    public function activeSpecialDiscount()
    {
        $now = date('Y-m-d H:i:s');
        $currentApp = PApp::getCurrentApp();

        if ($currentApp == false)
        {
            return null;
        }

        /*
          return $this->hasOne('SpecialDiscount', 'variant_id')
          ->where(function($query)
          {
          $now = date('Y-m-d H:i:s');
          $currentApp = PApp::getCurrentApp();

          return $query->where('started_at', '<', $now)
          ->where('ended_at', '>', $now)
          ->where('app_id', $currentApp->id)
          ->orderBy('discount_price', 'ASC');
          });
         */
        return $this->hasOne('SpecialDiscount', 'variant_id')
                ->where('started_at', '<', $now)
                ->where('ended_at', '>', $now)
                ->where('app_id', $currentApp->id)
                ->orderBy('discount_price', 'ASC');
    }

    public function getActiveTrueyouDiscountAttribute()
    {
        $now = date('Y-m-d H:i:s');
        $currentApp = PApp::getCurrentApp();

        if ($currentApp == false)
        {
            return array();
        }

        $trueYouRed = DB::table('variant_promotion')
            ->select('discount', 'discount_type', 'started_at', 'ended_at')
            ->where('variant_id', $this->id)
            ->where('app_id', $currentApp->id)
            ->where('condition', 'red')
            ->where('started_at', '<', $now)
            ->where('ended_at', '>', $now)
            ->orderBy('id', 'DESC')
            ->first();

        $trueYouBlack = DB::table('variant_promotion')
            ->select('discount', 'discount_type', 'started_at', 'ended_at')
            ->where('variant_id', $this->id)
            ->where('app_id', $currentApp->id)
            ->where('condition', 'black')
            ->where('started_at', '<', $now)
            ->where('ended_at', '>', $now)
            ->orderBy('id', 'DESC')
            ->first();

        if (empty($trueYouRed) && empty($trueYouBlack))
        {
            return array();
        }

        return array(
            'red' => $trueYouRed,
            'black' => $trueYouBlack,
        );
    }

    public function getNormalPriceAttribute($normalPrice)
    {
        if ($normalPrice == 0 && isset($this->activeSpecialDiscount))
            return $this->original['price'];

        return $normalPrice;
    }

    public function getPriceAttribute($price)
    {
        if (isset($this->activeSpecialDiscount) && !is_null($this->activeSpecialDiscount))
        {
            $price = min($this->activeSpecialDiscount->discount_price, $price);
        }

        return $price;
    }

    // @TODO : #EP
    public function getEpDiscountPriceAttribute()
    {
        $variantId = $this->id;

        // query from VariantPromotion where condition = ep

        return 0;
    }

    public function getNetPriceAttribute()
    {
        if ($this->price != 0 && $this->normal_price == 0)
        {
            return $this->price;
        }
        else
        {
            return $this->normal_price;
        }
    }

    public function getSpecialPriceAttribute()
    {
        if ($this->price != 0 && $this->normal_price == 0)
        {
            return $this->normal_price;
        }
        else
        {
            return $this->price;
        }
    }

    public function getPercentDiscountAttribute()
    {
        if ($this->special_price > 0)
            return round(100 - ($this->special_price / $this->net_price * 100), 2);
        return 0;
    }

    public function getDimensionMaxAttribute()
    {
        if ($this->sortedDimensions == false)
        {
            $this->sortDimensions();
        }
        return $this->sortedDimensions[0];
    }

    public function getDimensionMidAttribute()
    {
        if ($this->sortedDimensions == false)
        {
            $this->sortDimensions();
        }
        return $this->sortedDimensions[1];
    }

    public function getDimensionMinAttribute()
    {
        if ($this->sortedDimensions == false)
        {
            $this->sortDimensions();
        }
        return $this->sortedDimensions[2];
    }

    public function getShippingWeightAttribute()
    {
        $dimensionWeight = ($this->dimension_width * $this->dimension_length * $this->dimension_height) / 5000;
        $shippingWeight = max($this->weight, $dimensionWeight);
        return $shippingWeight;
    }

    protected function sortDimensions()
    {
        $dimensions = array(
            $this->dimension_width,
            $this->dimension_length,
            $this->dimension_height,
        );

        rsort($dimensions);
        $this->sortedDimensions = $dimensions;
    }

    public function getRemainingAttribute()
    {
        return $this->sc_remaining;
    }

    public function promotions()
    {
        return $this->belongsToMany('Promotion', 'variant_promotion', 'variant_id', 'promotion_id');
    }

    public function getInstallmentAttribute($value)
    {
        if ($value == false)
            return null;
        return json_decode($value);
    }

    public function getAllowInstallmentAttribute()
    {
        if ($this->installment === null)
            return null;
        return $this->installment->allow;
    }

    public function getInstallmentPeriodsAttribute()
    {
        if ($this->allow_installment)
            return isset($this->installment->periods) ? $this->installment->periods : array();
        return array();
    }

    public function getImageAttribute()
    {
        $image = null;

        $this->load('product.styleOptions', 'product.styleTypes');

        $styleTypeMediaSet = $this->product->styleTypes->filter(function($styleType)
                {
                    return ($styleType->pivot->media_set == 1);
                })->first();

        if ($styleTypeMediaSet)
        {
            $variantStyleOptionFilter = function($styleOption) use ($styleTypeMediaSet)
                {
                    return ($styleOption->style_type_id == $styleTypeMediaSet->id);
                };

            $variantStyleOption = $this->styleOptions->filter($variantStyleOptionFilter)->first();

            if ($variantStyleOption)
            {
                $productStyleOptionFilter = function($styleOption) use ($variantStyleOption)
                    {
                        return ($styleOption->id == $variantStyleOption->id);
                    };

                $productStyleOption = $this->product->styleOptions->filter($productStyleOptionFilter)->first();

                if ($productStyleOption)
                {
                    $productStyleOption = ProductStyleOption::with('mediaContents')->find($productStyleOption->pivot->id);

                    $mediaContent = $productStyleOption->mediaContents->first();

                    if ($mediaContent)
                    {
                        $image = (string) $mediaContent->image;
                    }
                }
            }
        }

        if (!$image)
        {
            $image = $this->product->image;
        }

        return $image;
    }

    public function getStockTypeCodeAttribute()
    {
        return static::getStockType($this->stock_type);
    }

    public static function getStockType($stock_type)
    {
        if ($stock_type == 3)
        {
            $stock_type = 3.1;
        }
        elseif ($stock_type == 4)
        {
            $stock_type = 4.1;
        }
        elseif ($stock_type == 5)
        {
            $stock_type = 3.2;
        }
        elseif ($stock_type == 6)
        {
            $stock_type = 4.2;
        }

        return (string) $stock_type;
    }

}

//ProductVariant::observe(new Observer\ProductVariantObserver);
//ProductVariant::observe(new Observer\ProductCache);
