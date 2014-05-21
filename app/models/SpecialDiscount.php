<?php

class SpecialDiscount extends PCMSModel {

    public static $autoKey = false;

    protected $softDelete = false;

    public static $rules = array(
        'app_id' => 'required',
        'campaign_type' => 'required',
        'variant_id' => 'required|integer',
        'inventory_id' => 'required|integer',
        // 'name' => 'required',
        'description' => 'required',
        'discount' => 'required|integer',
        'discount_type' => 'required',
        'discount_price' => 'required|numeric',
        'started_at' => 'required',
        'ended_at' => 'required'
    );


    public function productVariant()
    {
        return $this->belongsTo('ProductVariant', 'variant_id');
    }

    public function discountCampaign()
    {
        return $this->belongsTo('DiscountCampaign', 'discount_campaign_id');
    }

    public function buildDiscountPrice($netPrice)
    {
        if (is_null($this->discount))
        {
            throw new Exception("Discount value is null.");
        }

        if (! in_array($this->discount_type, array('percent', 'price')))
        {
            throw new Exception("Discount type is required.");
        }

        // discount is percent
        if ($this->discount_type == 'percent')
        {
            // 100 > discount > 0
            $this->discount = max(min($this->discount, 100), 0);
            $discount_price = $netPrice - (($this->discount / 100) * $netPrice);
        }
        else
        {
            // net price > discount > 0
            $this->discount = max(min($this->discount, $netPrice), 0);
            $discount_price = $netPrice - $this->discount;
        }

        // net price > discount_price > 0
        $this->discount_price = max(min($discount_price, $netPrice), 0);
    }
}

