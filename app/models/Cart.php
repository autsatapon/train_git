<?php

class Cart extends PCMSModel {

    protected $fillable = array('app_id', 'customer_ref_id');
    public static $rules = array(
        'app_id' => 'required',
        'customer_ref_id' => 'required',
        'customer_type' => 'required|in:user,non-user'
    );
    protected $with = array('cartDetails');

    protected $promotionCode = array();
    protected $promotionData = array();
    protected $discountCampaignData = array();
    protected $cashVoucher = 0;

    const USER = 'user';
    const NON_USER = 'non-user';

    public static $autoKey = false;

    /**
     * Relations
     */

    public function cartDetails()
    {
        return $this->hasMany('CartDetail');
    }

    public function cartTrueyou()
    {
        return $this->hasOne('CartTrueyou');
    }

    /**
     * Polymorphic Relations for ValidPromotion
     * @return object
     */
    public function validPromotions()
    {
        return $this->morphMany('ValidPromotion', 'promotionable');
    }

    /**
     * Getter & Setter
     */

    public function getTotalPriceAttribute()
    {
        $totalPrices = $this->cartDetails->lists('totalPrice');

        return array_sum($totalPrices);
    }

    public function getShipmentsAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setShipmentsAttribute($value)
    {
        $this->attributes['shipments'] = json_encode($value);
    }

    public function province()
    {
        return $this->belongsTo('Province', 'customer_province_id');
    }

    public function city()
    {
        return $this->belongsTo('City', 'customer_city_id');
    }

    public function district()
    {
        return $this->belongsTo('District', 'customer_district_id');
    }

    /*
      public function getTotalItemAttribute()
      {
      $items = $this->cartDetails->lists('quantity');

      return array_sum($items);
      }

      public function getItemAttribute()
      {
      return $this->cartDetails->count();
      }
     */

    public function getTotalQtyAttribute()
    {
        $items = $this->cartDetails->lists('quantity');

        return array_sum($items);
    }

    public function getTotalItemAttribute()
    {
        return $this->cartDetails->count();
    }

    public function getSubtotalAttribute()
    {
        $total = $this->total;

        if ($this->discount)
        {
            $total -= $this->discount;
        }

        return $total > 0 ? $total : 0;
    }

    public function getPromotionCodeAttribute()
    {
        return $this->promotionCode;
    }

    public function addPromotionCode($code)
    {
        if (! in_array('promotionCode', $this->appends))
        {
            $this->appends[] = 'promotionCode';
        }
        $this->promotionCode[] = $code;
    }

    public function setPromotionCode($code = null)
    {
        $this->promotionCode = array();
        if ($code)
        {
            $this->addPromotionCode($code);
        }
    }

    public function getPromotionDataAttribute()
    {
        return $this->promotionData;
    }

    public function addPromotionData($data)
    {
        if (! in_array('promotionData', $this->appends))
        {
            $this->appends[] = 'promotionData';
        }
        $this->promotionData[] = $data;
    }

    public function setPromotionData($data = null)
    {
        $this->promotionData = array();
        if ($data)
        {
            $this->addPromotionData($data);
        }
    }

    public function getCashVoucherAttribute()
    {
        return $this->cashVoucher;
    }

    public function setCashVoucher($cash = 0)
    {
        if (! in_array('cashVoucher', $this->appends))
        {
            $this->appends[] = 'cashVoucher';
        }

        $this->cashVoucher = $cash;
    }

    public function getDiscountCampaignDataAttribute()
    {
        return $this->discountCampaignData;
    }

    public function addDiscountCampaignData($data)
    {
        if (! in_array('discountCampaignData', $this->appends))
        {
            $this->appends[] = 'discountCampaignData';
        }
        $this->discountCampaignData[] = $data;
    }

    public function setDiscountCampaignData($data = null)
    {
        $this->discountCampaignData = array();
        if ($data)
        {
            $this->addDiscountCampaignData($data);
        }
    }

}